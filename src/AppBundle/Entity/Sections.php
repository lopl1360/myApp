<?php
// src/AppBundle/Entity/Sections.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Sections")
 */
class Sections
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="string", length=100)
	 */
	private $keyString;

	/**
	 * @ORM\Column(type="string", length=100)
	 */
	private $labelString;

	public function setKey($key)
	{
		$this->keyString = $key;
	}

	public function setLabel($label)
	{
		$this->labelString = $label;
	}
}
?>

