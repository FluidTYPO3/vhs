<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Format;

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
