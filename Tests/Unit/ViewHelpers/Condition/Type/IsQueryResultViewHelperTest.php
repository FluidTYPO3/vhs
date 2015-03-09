<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Condition\Type;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * @protection off
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 */
class IsQueryResultViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function rendersThenChildIfConditionMatched() {
		$queryResult = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\QueryResult',
			array('toArray', 'initialize', 'rewind', 'valid', 'count'), array(), '', FALSE);
		$this->assertEquals('then', $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'value' => $queryResult)));
	}

	/**
	 * @test
	 */
	public function rendersElseChildIfConditionNotMatched() {
		$this->assertEquals('else', $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'value' => 1)));
	}

}
