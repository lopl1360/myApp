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
		$app->setAppConfig(json_encode($appConfig));
		try{
			$this->applyToDBase($app);
		}	
		catch (\Doctrine\DBAL\DBALException $e)
		{
			return $this->response('failed', "App $appName has NOT been created. Message:" . $e->getMessage());
		}

		return $this->response('Success', "App $appName has been created");
	}
}
?>
