<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Variable\Register;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * @protection off
 * @author Stefan Neufeind <info (at) speedpartner.de>
 * @package Vhs
 */
class GetViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function silentlyIgnoresMissingFrontendController() {
		$result = $this->executeViewHelper(array('name' => 'name'));
		$this->assertNull($result);
	}

	/**
	 * @test
	 */
	public function returnsNullIfRegisterDoesNotExist() {
		$GLOBALS['TSFE'] = $this->getMock('TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController', array(), array(), '', FALSE);
		$name = uniqid();
		$this->assertEquals(NULL, $this->executeViewHelper(array('name' => $name)));
	}

	/**
	 * @test
	 */
	public function returnsValueIfRegisterExists() {
		$GLOBALS['TSFE'] = $this->getMock('TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController', array(), array(), '', FALSE);
		$name = uniqid();
		$value = uniqid();
		$GLOBALS['TSFE']->register[$name] = $value;
		$this->assertEquals($value, $this->executeViewHelper(array('name' => $name)));
	}

}
