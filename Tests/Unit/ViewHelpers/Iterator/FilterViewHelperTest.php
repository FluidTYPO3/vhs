<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * @protection on
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 */
class FilterViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function nullSubjectCallsRenderChildrenToReadValue() {
		$subject = ['test' => 'test'];
		$arguments = [
			'preserveKeys' => TRUE
		];
		$result = $this->executeViewHelperUsingTagContent('Array', $subject, $arguments);
		$this->assertSame($subject, $result);
	}

	/**
	 * @test
	 */
	public function filteringEmptySubjectReturnsEmptyArrayOnInvalidSubject() {
		$arguments = [
			'subject' => new \DateTime('now')
		];
		$result = $this->executeViewHelper($arguments);
		$this->assertSame($result, []);
	}

	/**
	 * @test
	 */
	public function supportsIterators() {
		$array = ['test' => 'test'];
		$iterator = new \ArrayIterator($array);
		$arguments = [
			'subject' => $iterator,
			'filter' => 'test',
			'preserveKeys' => TRUE
		];
		$result = $this->executeViewHelper($arguments);
		$this->assertSame($result, $array);
	}

	/**
	 * @test
	 */
	public function supportsPropertyName() {
		$array = [['test' => 'test']];
		$iterator = new \ArrayIterator($array);
		$arguments = [
			'subject' => $iterator,
			'filter' => 'test',
			'propertyName' => 'test',
			'preserveKeys' => TRUE
		];
		$result = $this->executeViewHelper($arguments);
		$this->assertSame($result, $array);
	}

}
