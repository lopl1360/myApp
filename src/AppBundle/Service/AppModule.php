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


	public function getAppConfig($appName)
	{
		return $this->em
			->getRepository('AppBundle:Apps')
			->find($appName);
	}
}
