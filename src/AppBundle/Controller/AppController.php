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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AppController extends Controller
{

	private function response($status, $report)
	{
		return new JsonResponse(['Status' => $status, 'Report' => $report]);
	}

	/**
	 * @Route("/create/{appName}")
	 */
	public function createApp(GameModule $gameModule, AppModule $appModule,  $appName)
	{
		$appConig = [];
		$games = $gameModule->getAllGames();

		if (!$games)
		{
			throw $this->respond('failed', 'No game found');
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
			return $this->response('failed', "App $appName has NOT been created. Message:" . $e->getMessage());
		}

		return $this->response('Success', "App $appName has been created");
	}

	/**
	 * @Route("/gameList/{appName}")
	 */
	public function gameList(AppModule $appModule, $appName)
	{
		$data = $games = [];
		if (!$appName)
		{
			return $this->response('failed', 'The app name should not be empty');
		}

		$app = $appModule->findApp($appName);
		if (!$app)
		{
			return $this->response('failed', 'The app name has not been added.');
		}

		foreach ($app->getAppConfig() as $gameName => $sections)
		{
			$game = [];
			$game['key'] = $gameName;
			$game['label']  = $this->getGameLabel($gameName);
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
		$app = $appModule->findApp($appName);
		$content = $request->getContent();
		if (!empty($content))
		{
			$games = json_decode($content, true); // 2nd param to get as array
			foreach ($games as $gameName => $status)
			{
				
			}
			
		}
		return new JsonResponse($params);
	}

	private function getGameLabel($gameName)
	{
		return $this->getDoctrine()
			->getRepository('AppBundle:Games')
			->find($gameName)
			->getLabel();
	}
}
?>
