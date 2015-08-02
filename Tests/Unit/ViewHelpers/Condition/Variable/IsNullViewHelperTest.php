<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Condition\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * @protection off
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 */
class IsNullViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function rendersThenChildIfVariableIsNull() {
		$arguments = [
			'value' => NULL,
			'then' => 'then',
			'else' => 'else'
		];
		$result = $this->executeViewHelper($arguments);
		$this->assertEquals($arguments['then'], $result);
	}

	/**
	 * @test
	 */
	public function rendersElseChildIfVariableIsNotNull() {
		$arguments = [
			'value' => TRUE,
			'then' => 'then',
			'else' => 'else'
		];
		$result = $this->executeViewHelper($arguments);
		$this->assertEquals($arguments['else'], $result);
	}

}
