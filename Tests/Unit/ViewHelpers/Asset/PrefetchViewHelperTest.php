<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Asset;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Cedric Ziel <cedric@cedric-ziel.com>
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
 * ************************************************************* */

/**
 * @protection off
 * @author Cedric Ziel <cedric@cedric-ziel.com>
 * @package Vhs
 * @subpackage ViewHelpers\Asset
 */
class PrefetchViewHelperTest extends \FluidTYPO3\Vhs\ViewHelpers\AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function buildReturnsMetaTag() {
		$instance = $this->buildViewHelperInstance(array('domains' => 'test.com,test2.com', 'force' => TRUE));
		$instance->render();
		$result = $instance->build();
		$this->assertStringStartsWith('<meta', $result);
	}

}
