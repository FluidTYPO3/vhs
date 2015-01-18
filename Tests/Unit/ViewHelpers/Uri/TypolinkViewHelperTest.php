<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Uri;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * @protection on
 * @package Vhs
 */
class TypolinkViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function renderCallsTypoLinkFunctionOnContentObject() {
		$class = $this->getViewHelperClassName();
		$mock = new $class();
		$mock->setArguments(array('configuration' => array('foo' => 'bar')));
		$GLOBALS['TSFE'] = new TypoScriptFrontendController(array(), 1, 0);
		$GLOBALS['TSFE']->cObj = $this->getMock('TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer', array('typoLink_URL'));
		$GLOBALS['TSFE']->cObj->expects($this->once())->method('typoLink_URL')->with(array('foo' => 'bar'))->will($this->returnValue('foobar'));
		$result = $this->callInaccessibleMethod($mock, 'render');
		$this->assertEquals('foobar', $result);
	}

}
