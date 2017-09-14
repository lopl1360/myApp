<?php
// src/AppBundle/Controller/AppController.php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Sections;
use AppBundle\Entity\Games;
use AppBundle\Entity\Apps;
use AppBundle\Service\AppModule;
use AppBundle\Service\GameModule;
use AppBundle\Service\SectionModule;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AppController extends Controller
{

	private function response($status, $report)
	{
		return new JsonResponse(['Status' => $status, 'Report' => $report]);
	}

	private function getAppObject(AppModule $appModule, $appName)
	{
		if (!$appName)
		{
			throw new \Exception('The app name should not be empty');
		}

		$app = $appModule->findApp($appName);
		if (!$app)
		{
			throw new \Exception('This app does not exist.');
		}

		return $app;
	}

	/**
	 * @Route("/create/{appName}")
	 */
	public function createApp(GameModule $gameModule, AppModule $appModule,  $appName)
	{
		if (!$appName)
		{
			throw new \Exception('The app name should not be empty');
		}

		$appConig = [];
		$games = $gameModule->getAllGames();

		if (!$games)
		{
			throw new \Exception('No game found');
		}

		foreach ($games as $game)
		{
			$appConfig[$game->getGame()]['Sections'] = $game->getSections();
			$appConfig[$game->getGame()]['Enable'] = true;
		}

		try{
			$appModule->addNewApp($appName, $appConfig);
		}	
		catch (\Doctrine\DBAL\DBALException $e)
		{
			throw new \Exception("App $appName has NOT been created. Message:" . $e->getMessage());
		}

		return $this->response('Success', "App $appName has been created");
	}

	/**
	 * @Route("/gameList/{appName}")
	 */
	public function gameList(AppModule $appModule, GameModule $gameModule, $appName)
	{
		$data = $games = [];
		$app = $this->getAppObject($appModule, $appName);
		foreach ($app->getAppConfig() as $gameName => $sections)
		{
			$game = [];
			$game['key'] = $gameName;
			$game['label']  = $gameModule->getLabel($gameName);
			$games []= $game;
			
		}
		
		$data['data'] = $games;
		return new JsonResponse($data);;
	}

	/**
	 * @Route("/flipGame/{appName}")
	 */
	public function flipGame(AppModule $appModule, $appName, Request $request)
	{
		$params = array();
		$app = $this->getAppObject($appModule, $appName);
		$content = $request->getContent();
		if (!empty($content))
		{
			$games = json_decode($content, true); // 2nd param to get as array
			foreach ($games as $gameName => $status)
			{
				$appModule->updateGameStatus($app, $gameName, $status);	
			}
			
		}
		return new JsonResponse($app->getAppConfig());
	}

	/**
	 * @Route("/sections/{appName}")
	 */
	public function getActiveSections(GameModule $gameModule, SectionModule $sectionModule, AppModule $appModule, $appName)
	{
		$app = $this->getAppObject($appModule, $appName);
		$sections = $activeSections = $activesection = $activeGame = $activeGames = $data = [];
		$enabledGames = $appModule->getEnabledGames($app);
		foreach ($enabledGames as $game => $config)
		{
			foreach ($config['Sections'] as $section)
			{
					$sections[$section] []= $game;
			}
		}

		foreach ($sections as $section => $games)
		{
			$activeGames = $activeGame = [];
			$activeSection['key'] = $section;
			$activeSection['label'] = $sectionModule->getLabel($section);
			foreach ($games as $game)
			{
				$activeGame['key'] = $game;
				$activeGame['label'] = $gameModule->getLabel($game);
				$activeGames []= $activeGame;	
			}

			$activeSection['Games'] = $activeGames;
			$activeSections []= $activeSection;
		}

		$data['data'] = $activeSections;
		return new JsonResponse($data);
	}
}
?>
