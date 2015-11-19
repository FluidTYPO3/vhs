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
		$this->setExpectedException('TYPO3\CMS\Fluid\Core\ViewHelper\Exception', 'Use of Markdown requires the "markdown" shell utility to be installed');
		$this->executeViewHelper(array('text' => 'test < test', 'trim' => TRUE, 'htmlentities' => TRUE));
	}

	/**
	 * @test
	 */
	public function rendersUsingArgument() {
		$this->setExpectedException('TYPO3\CMS\Fluid\Core\ViewHelper\Exception', 'Use of Markdown requires the "markdown" shell utility to be installed');
		$this->executeViewHelper(array('text' => 'test', 'trim' => TRUE, 'htmlentities' => FALSE));
	}

	/**
	 * @test
	 */
	public function rendersUsingTagContent() {
		$this->setExpectedException('TYPO3\CMS\Fluid\Core\ViewHelper\Exception', 'Use of Markdown requires the "markdown" shell utility to be installed');
		$this->executeViewHelperUsingTagContent('Text', 'test', array('trim' => TRUE, 'htmlentities' => FALSE));
	}

}
