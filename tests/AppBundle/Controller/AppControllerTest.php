<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Controller\AppController;

class AppControllerTest extends WebTestCase
{
	private $controller;

	protected function setUp()
	{
		$this->controller = new AppController();
		
	}

	function createAppProvider()
	{
		return [
			'no name' => [
				'',
				False,
				['section1'],
				0,
				'The app name should not be empty'
				
			],
			'correct' => [
				'aa',
				False,
				['section1'],
				1,
				Null
			],
			'No games' => [
				'aa',
				True,
				['section1'],
				0,
				'No game found'
			],
		];
	}

	/**
	 * @dataProvider createAppProvider
	 */
	public function testCreateApp($app, $emptyGames, $sections, $numberNewApp, $expectedException)
	{
		$gamesMock = $this->getMockBuilder('AppBundle\Entity\Games')
			->setMethods(['getsections'])
			->disableOriginalConstructor()
			->GetMock();

		$gamesMock->method('getSections')
			->will($this->returnValue($sections));
			
		$gameModuleMock = $this->getMockBuilder('AppBundle\Service\GameModule')
			->setMethods(['getAllGames'])
			->disableOriginalConstructor()
			->GetMock();

		if (!$emptyGames)
		{
			$gameModuleMock->method('getAllGames')
				->will($this->returnValue([$gamesMock]));
		}
		else
		{
			$gameModuleMock->method('getAllGames')
				->will($this->returnValue([]));
		}

		$appModuleMock = $this->getMockBuilder('AppBundle\Service\AppModule')
			->setMethods(['addNewApp'])
			->disableOriginalConstructor()
			->GetMock();

		$appModuleMock->expects($this->exactly($numberNewApp))
			->method('addNewApp')
			->will($this->returnValue(0));

		if( $expectedException != NULL )
		{
			$this->setExpectedException( "\Exception", $expectedException );
		}

		$this->controller->createApp($gameModuleMock, $appModuleMock, $app);
	}
}
