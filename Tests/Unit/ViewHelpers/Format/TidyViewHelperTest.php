<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
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
			$this->markTestSkipped('No tidy support');
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
			$this->markTestSkipped('No tidy support');
			return;
		}
		$source = '<foo> <bar>
			</bar>			</foo>';
		$test = $this->executeViewHelper(array('content' => $source));
		$this->assertNotSame($source, $test);
	}

}
