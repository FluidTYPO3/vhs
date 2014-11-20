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
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * @protection on
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 */
class TidyViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function throwsErrorWhenNoTidyIsInstalled() {
		$instance = $this->createInstance();
		ObjectAccess::setProperty($instance, 'hasTidy', FALSE, TRUE);
		$this->setExpectedException('RuntimeException', NULL, 1352059753);
		$instance->render('test');
	}

	/**
	 * @test
	 */
	public function canTidySourceFromTagContent() {
		$instance = $this->createInstance();
		if (FALSE === ObjectAccess::getProperty($instance, 'hasTidy', TRUE)) {
			return;
		}
		$source = '<foo> <bar>
			</bar>			</foo>';
		$test = $this->executeViewHelperUsingTagContent('Text', $source);
		$this->assertNotSame($source, $test);
	}

	/**
	 * @test
	 */
	public function canTidySourceFromArgument() {
		$instance = $this->createInstance();
		if (FALSE === ObjectAccess::getProperty($instance, 'hasTidy', TRUE)) {
			return;
		}
		$source = '<foo> <bar>
			</bar>			</foo>';
		$test = $this->executeViewHelper(array('content' => $source));
		$this->assertNotSame($source, $test);
	}

}
