<?php
// src/AppBundle/Entity/Apps.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Apps")
 */
class Apps
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="string", length=100)
	 */
	private $appName;

	/**
	 * @ORM\Column(type="json_array", length=1000)
	 */
	private $appConfig;

	public function setAppName($name)
	{
		$this->appName = $name;
	}

	public function setAppConfig($config)
	{
		$this->appConfig = $config;
	}
}
?>

