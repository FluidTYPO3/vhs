<?php
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

/**
 * @protection on
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 */
class Tx_Vhs_ViewHelpers_Format_Placeholder_LipsumViewHelperTest extends Tx_Vhs_ViewHelpers_AbstractViewHelperTest {

	/**
	 * @var array
	 */
	protected $arguments = array(
		'paragraphs' => 5,
		'skew' => 0,
		'html' => FALSE,
		'parseFuncTSPath' => ''
	);

	/**
	 * @test
	 */
	public function supportsParagraphCount() {
		$arguments = $this->arguments;
		$firstRender = $this->executeViewHelper($arguments);
		$arguments['paragraphs'] = 6;
		$secondRender = $this->executeViewHelper($arguments);
		$this->assertLessThan(strlen($secondRender), strlen($firstRender));
	}

	/**
	 * @test
	 */
	public function supportsHtmlArgument() {
		$arguments = $this->arguments;
		$arguments['html'] = TRUE;
		$test = $this->executeViewHelper($arguments);
		$this->assertNotEmpty($test);
	}

	/**
	 * @test
	 */
	public function detectsFileByShortPath() {
		$arguments = $this->arguments;
		$arguments['lipsum'] = 'EXT:vhs/Tests/Fixtures/Files/foo.txt';
		$test = $this->executeViewHelper($arguments);
		$this->assertNotEmpty($test);
	}

	/**
	 * @test
	 */
	public function canFallBackWhenUsingFileAndFileDoesNotExist() {
		$arguments = $this->arguments;
		$arguments['lipsum'] = 'EXT:vhs/None.txt';
		$test = $this->executeViewHelper($arguments);
		$this->assertNotEmpty($test);
	}

}
