<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
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
use FluidTYPO3\Vhs\ViewHelpers\AbstractViewHelperTest;

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
		$subject = array('test' => 'test');
		$arguments = array(
			'preserveKeys' => TRUE
		);
		$result = $this->executeViewHelperUsingTagContent('Array', $subject, $arguments);
		$this->assertSame($subject, $result);
	}

	/**
	 * @test
	 */
	public function filteringEmptySubjectReturnsEmptyArrayOnInvalidSubject() {
		$arguments = array(
			'subject' => new \DateTime('now')
		);
		$result = $this->executeViewHelper($arguments);
		$this->assertSame($result, array());
	}

	/**
	 * @test
	 */
	public function supportsIterators() {
		$array = array('test' => 'test');
		$iterator = new \ArrayIterator($array);
		$arguments = array(
			'subject' => $iterator,
			'filter' => 'test',
			'preserveKeys' => TRUE
		);
		$result = $this->executeViewHelper($arguments);
		$this->assertSame($result, $array);
	}

	/**
	 * @test
	 */
	public function supportsPropertyName() {
		$array = array(array('test' => 'test'));
		$iterator = new \ArrayIterator($array);
		$arguments = array(
			'subject' => $iterator,
			'filter' => 'test',
			'propertyName' => 'test',
			'preserveKeys' => TRUE
		);
		$result = $this->executeViewHelper($arguments);
		$this->assertSame($result, $array);
	}

}
