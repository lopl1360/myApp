<?php
// src/AppBundle/Service/GameModule.php
namespace AppBundle\Service;

use AppBundle\Entity\Games;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManager;

class GameModule
{
	private $em;
	
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}
	
	public function getAllGames()
	{
		return $this->em
			->getRepository('AppBundle:Games')
			->findAll();
	}
}
