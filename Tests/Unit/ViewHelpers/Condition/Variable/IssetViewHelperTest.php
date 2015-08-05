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
class IssetViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function rendersThenChildIfVariableIsSet() {
		$arguments = [
			'name' => 'test',
			'then' => 'then',
			'else' => 'else'
		];
		$variables = [
			'test' => TRUE
		];
		$result = $this->executeViewHelper($arguments, $variables);
		$this->assertEquals($arguments['then'], $result);
	}

	/**
	 * @test
	 */
	public function rendersElseChildIfVariableIsNotSet() {
		$arguments = [
			'name' => 'test',
			'then' => 'then',
			'else' => 'else'
		];
		$variables = [];
		$result = $this->executeViewHelper($arguments, $variables);
		$this->assertEquals($arguments['else'], $result);
	}

}
