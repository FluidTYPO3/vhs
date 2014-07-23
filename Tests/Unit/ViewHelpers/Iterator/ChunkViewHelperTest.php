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
 * @author Björn Fromme <fromme@dreipunktnull.com>
 * @package Vhs
 */
class ChunkViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function returnsConfiguredItemNumberIfFixed() {
		$arguments = array(
			'count' => 5,
			'fixed' => TRUE,
			'subject' => array('a', 'b', 'c', 'd', 'e'),
		);
		$result = $this->executeViewHelper($arguments);
		$this->assertCount(5, $result);
	}

	/**
	 * @test
	 */
	public function returnsConfiguredItemNumberIfFixedAndSubjectIsEmpty() {
		$arguments = array(
			'count' => 5,
			'fixed' => TRUE,
			'subject' => array(),
		);
		$result = $this->executeViewHelper($arguments);
		$this->assertCount(5, $result);
	}

	/**
	 * @test
	 */
	public function returnsExpectedItemNumberIfNotFixed() {
		$arguments = array(
			'count' => 4,
			'subject' => array('a', 'b', 'c', 'd', 'e'),
		);
		$result = $this->executeViewHelper($arguments);
		$this->assertCount(2, $result);
	}

	/**
	 * @test
	 */
	public function returnsEmptyResultForEmptySubjectAndNotFixed() {
		$arguments = array(
			'count' => 5,
			'subject' => array(),
		);
		$result = $this->executeViewHelper($arguments);
		$this->assertCount(0, $result);
	}

	/**
	 * @test
	 */
	public function returnsEmptyResultForZeroCount() {
		$arguments = array(
			'count' => 0,
			'subject' => array('a', 'b', 'c', 'd', 'e'),
		);
		$result = $this->executeViewHelper($arguments);
		$this->assertCount(0, $result);
	}

	/**
	 * @test
	 */
	public function preservesArrayKeysIfRequested() {
		$arguments = array(
			'count' => 2,
			'subject' => array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5),
			'preserveKeys' => TRUE,
		);
		$result = $this->executeViewHelper($arguments);

		$expected = array(array('a' => 1, 'b' => 2), array('c' => 3, 'd' => 4), array('e' => 5));
		$this->assertEquals($expected, $result);
	}

}
