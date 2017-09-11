<?php
// src/AppBundle/Entity/Games.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Games")
 */
class Games
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="string", length=100)
	 */
	private $game;

	/**
	 * @ORM\Column(type="string", length=100)
	 */
	private $label;

	/**
	 * @ORM\Column(type="json_array", length=1000)
	 */
	private $sections;

	public function setGame($game)
	{
		$this->game = $game;
	}

	public function setLabel($label)
	{
		$this->label = $label;
	}
	
	public function setSections($sections)
	{
		$this->sections = $sections;
	}

	public function getGame()
	{
		return $this->game;
	}

	public function getLabel()
	{
		return $this->label;
	}
	
	public function getSections()
	{
		return $this->sections;
	}
}
?>

