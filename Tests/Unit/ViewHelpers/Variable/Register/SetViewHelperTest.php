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
class SetViewHelperTest extends AbstractViewHelperTest {

	/**
	 * Set up this testcase
	 */
	public function setUp() {
		parent::setUp();
		$GLOBALS['TSFE'] = $this->getMock('TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController', array(), array(array(), 1, 1));
	}

	/**
	 * @test
	 */
	public function canSetRegister() {
		$name = uniqid();
		$value = uniqid();
		$this->executeViewHelper(array('name' => $name, 'value' => $value));
		$this->assertEquals($value, $GLOBALS['TSFE']->register[$name]);
	}

	/**
	 * @test
	 */
	public function canSetVariableWithValueFromTagContent() {
		$name = uniqid();
		$value = uniqid();
		$this->executeViewHelperUsingTagContent('Text', $value, array('name' => $name));
		$this->assertEquals($value, $GLOBALS['TSFE']->register[$name]);
	}

}
