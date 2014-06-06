<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format;
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
class MarkdownViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function supportsHtmlEntities() {
		$test = $this->executeViewHelper(array('text' => 'test < test', 'trim' => TRUE, 'htmlentities' => TRUE));
		if (FALSE === strpos($test, 'Use of Markdown requires the "markdown" shell utility to be installed')) {
			$this->assertSame("<p>test &lt; test</p>\n", $test);
		} else {
			$this->assertStringStartsWith('Use of Markdown requires the "markdown" shell utility to be installed', $test);
		}
	}

	/**
	 * @test
	 */
	public function rendersUsingArgument() {
		$test = $this->executeViewHelper(array('text' => 'test', 'trim' => TRUE, 'htmlentities' => FALSE));
		if (FALSE === strpos($test, 'Use of Markdown requires the "markdown" shell utility to be installed')) {
			if (0 === strpos($test, '<')) {
				$this->assertSame("<p>test</p>\n", $test);
			} else {
				$this->assertSame('test', $test);
			}
		} else {
			$this->assertStringStartsWith('Use of Markdown requires the "markdown" shell utility to be installed', $test);
		}
	}

	/**
	 * @test
	 */
	public function rendersUsingTagContent() {
		$test = $this->executeViewHelperUsingTagContent('Text', 'test', array('trim' => TRUE, 'htmlentities' => FALSE));
		if (FALSE === strpos($test, 'Use of Markdown requires the "markdown" shell utility to be installed')) {
				if (0 === strpos($test, '<')) {
				$this->assertSame("<p>test</p>\n", $test);
			} else {
				$this->assertSame('test', $test);
			}
		} else {
			$this->assertStringStartsWith('Use of Markdown requires the "markdown" shell utility to be installed', $test);
		}
	}

}
