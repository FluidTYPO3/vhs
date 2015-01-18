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
	 * Set up this testcase
	 */
	public function setUp() {
		parent::setUp();
		$GLOBALS['TSFE'] = $this->getMock('TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController', array(), array(), '', FALSE);
	}

	/**
	 * @disabledtest
	 */
	public function returnsNullIfRegisterDoesNotExist() {
		$name = uniqid();
		$this->assertEquals(NULL, $this->executeViewHelper(array('name' => $name)));
	}

	/**
	 * @disabledtest
	 */
	public function returnsValueIfRegisterExists() {
		$name = uniqid();
		$value = uniqid();
		$GLOBALS['TSFE']->register[$name] = $value;
		$this->assertEquals($value, $this->executeViewHelper(array('name' => $name)));
	}

}
