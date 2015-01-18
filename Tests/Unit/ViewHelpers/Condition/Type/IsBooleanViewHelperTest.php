<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Condition\Type;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * @protection off
 * @package Vhs
 */
class IsBooleanViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function rendersThenChildIfConditionMatched() {
		$this->assertEquals('then', $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'value' => TRUE)));
	}

	/**
	 * @test
	 */
	public function rendersElseChildIfConditionNotMatched() {
		$this->assertEquals('else', $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'value' => new \stdClass())));
	}

}
