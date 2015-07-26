<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * @protection on
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 */
class CallViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function throwsRuntimeExceptionIfObjectNotFound() {
		$this->setExpectedException('RuntimeException', NULL, 1356849652);
		$this->executeViewHelper(['method' => 'method', 'arguments' => []]);
	}

	/**
	 * @test
	 */
	public function throwsRuntimeExceptionIfMethodNotFound() {
		$object = new \ArrayIterator(['foo', 'bar']);
		$this->setExpectedException('RuntimeException', NULL, 1356834755);
		$this->executeViewHelper(['method' => 'notfound', 'object' => $object, 'arguments' => []]);
	}

	/**
	 * @test
	 */
	public function executesMethodOnObjectFromArgument() {
		$object = new \ArrayIterator(['foo', 'bar']);
		$result = $this->executeViewHelper(['method' => 'count', 'object' => $object, 'arguments' => []]);
		$this->assertEquals(2, $result);
	}

	/**
	 * @test
	 */
	public function executesMethodOnObjectFromChildContent() {
		$object = new \ArrayIterator(['foo', 'bar']);
		$result = $this->executeViewHelperUsingTagContent('ObjectAccessor', 'v', ['method' => 'count', 'arguments' => []], ['v' => $object]);
		$this->assertEquals(2, $result);
	}

}
