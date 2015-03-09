<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\System;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * @protection on
 * @author Cedric Ziel <cedric@cedric-ziel.com>
 * @package Vhs
 */
class UniqIdViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function returnsUniqueIds() {
		$arguments = array('prefix' => '', 'moreEntropy' => FALSE);
		$result1 = $this->executeViewHelper($arguments);
		$result2 = $this->executeViewHelper($arguments);
		$this->assertNotEquals($result1, $result2);
	}

}
