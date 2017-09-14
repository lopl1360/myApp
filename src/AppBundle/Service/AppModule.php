<?php
// src/AppBundle/Service/AppModule.php
namespace AppBundle\Service;

use AppBundle\Entity\Apps;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManager;

class AppModule
{
	private $em;
	
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}
	
	public function addNewApp($appName, $appConfig)
	{
		$app = new Apps();
		$app->setAppName($appName);
		$app->setAppConfig($appConfig);
		try{
			$this->em->persist($app);
			$this->em->flush();
		}
		catch (\Doctrine\DBAL\DBALException $e)
		{
			throw $e;
		}

	}

	public function findApp($appName)
	{
		return $this->em
    			->getRepository('AppBundle:Apps')
			->find($appName);
	}

	public function updateAppConfig(Apps $app, $config)
	{
		try{
			$app->setAppConfig($config);
			$this->em->persist($app);
			$this->em->flush();
		}
		catch (\Doctrine\DBAL\DBALException $e)
		{
			throw $e;
		}
	}

	public function updateGameStatus(Apps $app, $gameName, $newStatus)
	{
		$appConfig = $app->getAppConfig();
		foreach ($appConfig as $game => &$gameConfig)
		{
			if ($game == $gameName)
			{
				$gameConfig['Enable'] = $newStatus ? true : false;
			}
		}

		$this->updateAppConfig($app, $appConfig);
	}

	public function getEnabledGames(Apps $app)
	{
		$appConfig = $app->getAppConfig();
		foreach ($appConfig as $game => $gameConfig)
		{
			if (!$gameConfig['Enable'])
			{
				unset($appConfig[$game]);
			}
		}

		return $appConfig;
	}
}
