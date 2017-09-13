<?php
// src/AppBundle/Service/SectionModule.php
namespace AppBundle\Service;

use AppBundle\Entity\Sections;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManager;

class SectionModule
{
	private $em;
	
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	public function findSection($sectionKey)
	{
		return $this->em
    			->getRepository('AppBundle:Sections')
			->find($sectionKey);
	}

	public function getLabel($sectionKey)
	{
		return $this->findSection($sectionKey)->getLabel();
	}
}
