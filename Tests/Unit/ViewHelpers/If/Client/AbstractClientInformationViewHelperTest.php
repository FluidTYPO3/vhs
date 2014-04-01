<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Andreas Lappe <nd@kaeufli.ch>, kaeufli.ch
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

/**
 * @author Andreas Lappe <nd@kaeufli.ch>, kaeufli.ch
 * @package Vhs
 */
abstract class Tx_Vhs_ViewHelpers_If_Client_AbstractClientInformationViewHelperTest extends Tx_Vhs_ViewHelpers_AbstractViewHelperTest {

	/**
	 * @var Tx_Vhs_ViewHelpers_If_Client_IsBrowserViewHelper
	 */
	protected $fixture;

	public function setUp() {
		// Uses an actual implementation of this abstract class to test the methods
		$this->fixture = new Tx_Vhs_ViewHelpers_If_Client_IsBrowserViewHelper();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function getUserAgentReturnsString() {
		$userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/536.26.17 (KHTML, like Gecko) Version/6.0.2 Safari/536.26.17';
		$this->fixture->setUserAgent($userAgent);

		$this->assertSame(
			$userAgent,
			$this->fixture->getUserAgent()
		);
	}

	/**
	 * @test
	 */
	public function getBrowsersReturnsArray() {
		$this->fixture->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/536.26.17 (KHTML, like Gecko) Version/6.0.2 Safari/536.26.17');

		$this->assertSame(
			array(
				'webkit' => '536.26',
				'safari' => '536.26'
			),
			$this->fixture->getBrowsers()
		);
	}

	/**
	 * @test
	 */
	public function getSystemsReturnsArray() {
		$this->fixture->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/536.26.17 (KHTML, like Gecko) Version/6.0.2 Safari/536.26.17');

		$this->assertSame(
			array('mac'),
			$this->fixture->getSystems()
		);
	}
}
