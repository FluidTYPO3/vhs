<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Fixtures\Domain\Model\Foo;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * @protection off
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 */
class SetViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function canSetVariable() {
		$variables = new \ArrayObject(array('test' => TRUE));
		$this->executeViewHelper(array('name' => 'test', 'value' => FALSE), $variables);
		$this->assertFalse($variables['test']);
	}

	/**
	 * @test
	 */
	public function canSetVariableInExistingArrayValue() {
		$variables = new \ArrayObject(array('test' => array('test' => TRUE)));
		$this->executeViewHelper(array('name' => 'test.test', 'value' => FALSE), $variables);
		$this->assertFalse($variables['test']['test']);
	}

	/**
	 * @test
	 */
	public function ignoresNestedVariableIfRootDoesNotExist() {
		$variables = new \ArrayObject(array('test' => array('test' => TRUE)));
		$result = $this->executeViewHelper(array('name' => 'doesnotexist.test', 'value' => FALSE), $variables);
		$this->assertNull($result);
	}

	/**
	 * @test
	 */
	public function ignoresNestedVariableIfRootDoesNotAllowSetting() {
		$domainObject = new Foo();
		$variables = new \ArrayObject(array('test' => $domainObject));
		$result = $this->executeViewHelper(array('name' => 'test.propertydoesnotexist', 'value' => FALSE), $variables);
		$this->assertNull($result);
	}

	/**
	 * @test
	 */
	public function ignoresNestedVariableIfRootPropertyNameIsInvalid() {
		$variables = new \ArrayObject(array('test' => 'test'));
		$result = $this->executeViewHelper(array('name' => 'test.test', 'value' => FALSE), $variables);
		$this->assertNull($result);
	}

	/**
	 * @test
	 */
	public function canSetVariableWithValueFromTagContent() {
		$variables = new \ArrayObject(array('test' => TRUE));
		$this->executeViewHelperUsingTagContent('Boolean', FALSE, array('name' => 'test'), $variables);
		$this->assertFalse($variables['test']);
	}

}
