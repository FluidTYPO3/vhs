<?php
namespace FluidTYPO3\Vhs\Tests\Unit\View;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use FluidTYPO3\Vhs\View\UncacheTemplateView;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;

/**
 * Class UncacheTemplateViewTest
 */
class UncacheTemplateViewTest extends UnitTestCase {

	/**
	 * @test
	 */
	public function callUserFunctionReturnsEarlyIfPartialEmpty() {
		$mock = $this->getMock($this->getClassName(), array('prepareContextsForUncachedRendering'));
		$configuration = array('partial' => '');
		$mock->expects($this->never())->method('prepareContextsForUncachedRendering');
		$mock->callUserFunction('', $configuration, '');
	}

	/**
	 * @test
	 */
	public function callUserFunctionReturnsCallsExpectedMethodSequence() {
		$mock = $this->getMock($this->getClassName(), array('prepareContextsForUncachedRendering', 'renderPartialUncached'));
		$context = new ControllerContext();
		$configuration = array('partial' => 'dummy', 'section' => 'dummy', 'controllerContext' => $context);
		$mock->expects($this->once())->method('prepareContextsForUncachedRendering');
		$mock->expects($this->once())->method('renderPartialUncached');
		$mock->callUserFunction('', $configuration, '');
	}

	/**
	 * @test
	 */
	public function prepareContextsForUncachedRenderingCallsExpectedMethodSequence() {
		$controllerContext = new ControllerContext();
		$renderingContext = $this->getMock('TYPO3\CMS\Fluid\Core\Rendering\RenderingContext', array('setControllerContext'));
		$renderingContext->expects($this->once())->method('setControllerContext')->with($controllerContext);
		$mock = $this->getMock($this->getClassName(), array('setRenderingContext'));
		$mock->expects($this->once())->method('setRenderingContext')->with($renderingContext);
		$this->callInaccessibleMethod($mock, 'prepareContextsForUncachedRendering', $renderingContext, $controllerContext);
	}

	/**
	 * @test
	 */
	public function renderPartialUncachedDelegatesToRenderPartial() {
		$renderingContext = new RenderingContext();
		$mock = $this->getMock($this->getClassName(), array('renderPartial'));
		$mock->expects($this->once())->method('renderPartial')->will($this->returnValue('test'));
		$result = $this->callInaccessibleMethod($mock, 'renderPartialUncached', $renderingContext, 'dummy');
		$this->assertEquals('test', $result);
	}

	/**
	 * @return mixed|string
	 */
	protected function getClassName() {
		$class = substr(get_class($this), 0, -4);
		$class = str_replace('Tests\\Unit\\', '', $class);
		return $class;
	}

}
