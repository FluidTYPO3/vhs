<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Condition\Form;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Fixtures\Domain\Model\Bar;
use FluidTYPO3\Vhs\Tests\Fixtures\Domain\Model\Foo;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * @protection off
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 */
class IsRequiredViewHelperTest extends AbstractViewHelperTest {

	public function testRenderThenWithSingleProperty() {
		$domainObject = new Foo();
		$result = $this->executeViewHelper(array('property' => 'bar', 'object' => $domainObject, 'then' => 'then'));
		$this->assertEquals('then', $result);
	}

	public function testRenderElseWithSingleProperty() {
		$domainObject = new Foo();
		$result = $this->executeViewHelper(array('property' => 'foo', 'object' => $domainObject, 'else' => 'else'));
		$this->assertEquals('else', $result);
	}

	public function testRenderThenWithNestedSingleProperty() {
		$domainObject = new Bar();
		$result = $this->executeViewHelper(array('property' => 'foo.bar', 'object' => $domainObject, 'then' => 'then'));
		$this->assertEquals('then', $result);
	}

	public function testRenderElseWithNestedSingleProperty() {
		$domainObject = new Bar();
		$result = $this->executeViewHelper(array('property' => 'foo.foo', 'object' => $domainObject, 'else' => 'else'));
		$this->assertEquals('else', $result);
	}

	public function testRenderThenWithNestedMultiProperty() {
		$domainObject = new Bar();
		$result = $this->executeViewHelper(array('property' => 'bars.bar.foo.bar', 'object' => $domainObject, 'then' => 'then'));
		$this->assertEquals('then', $result);
	}

	public function testRenderElseWithNestedMultiProperty() {
		$domainObject = new Bar();
		$result = $this->executeViewHelper(array('property' => 'bars.foo.foo', 'object' => $domainObject, 'else' => 'else'));
		$this->assertEquals('else', $result);
	}

}
