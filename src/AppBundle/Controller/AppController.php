<?php
// src/AppBundle/Controller/AppController.php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Sections;
use AppBundle\Entity\Games;
use AppBundle\Entity\Apps;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AppController extends Controller
{
	private function response($status, $report)
	{
		return new JsonResponse(['Status' => $status, 'Report' => $report]);
	}

	private function applyToDBase($object)
	{
		try{
			$em = $this->getDoctrine()->getManager();
			$em->persist($object);
			$em->flush();
		}
		catch (\Doctrine\DBAL\DBALException $e)
		{
			throw $e;
		}
		
	}

	/**
	 * @Route("/create/{appName}")
	 */
	public function createApp($appName)
	{
		$appConig = [];
		$games = $this->getDoctrine()
			->getRepository('AppBundle:Games')
			->findAll();

		if (!$games)
		{
			throw $this->respond('failed', 'No game found');
		}

		foreach ($games as $game)
		{
			$appConfig[$game->getGame()]['Sections'] = $game->getSections();
		}

		$app = new Apps();
		$app->setAppName($appName);
		$app->setAppConfig($appConfig);
		try{
			$this->applyToDBase($app);
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
	public function gameList($appName)
	{
		$data = $games = [];
		if (!$appName)
		{
			return $this->response('failed', 'The app name should not be empty');
		}

		$app = $this->getDoctrine()
    			->getRepository('AppBundle:Apps')
			->find($appName);

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

	private function getGameLabel($gameName)
	{
		return $this->getDoctrine()
			->getRepository('AppBundle:Games')
			->find($gameName)
			->getLabel();
	}
}
?>
