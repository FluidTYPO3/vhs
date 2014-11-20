<?php
namespace FluidTYPO3\Vhs\ViewHelpers;
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
 ***************************************************************/

/**
 * @protection on
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 */
class OrViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 * @dataProvider getRenderTestValues
	 * @param array $arguments
	 * @param mixed $expected
	 */
	public function testRender($arguments, $expected) {
		$result = $this->executeViewHelper($arguments);
		$content = $arguments['content'];
		unset($arguments['content']);
		$result2 = $this->executeViewHelperUsingTagContent('Text', (string) $content, $arguments);
		$this->assertEquals($expected, $result);
		$this->assertEquals($result, $result2);
	}

	/**
	 * @return array
	 */
	public function getRenderTestValues() {
		return array(
			array(array('content' => 'alt', 'alternative' => 'alternative'), 'alt'),
			array(array('content' => '', 'alternative' => 'alternative'), 'alternative'),
			array(array('content' => NULL, 'alternative' => 'alternative'), 'alternative'),
			array(array('content' => 0, 'alternative' => 'alternative'), 'alternative'),
			array(
				array(
					'content' => 0,
					'alternative' => 'LLL:EXT:extensionmanager/Resources/Private/Language/locallang.xlf:extensionManager'
				),
				'Extension Manager'
			),
			array(
				array('content' => 0, 'alternative' => 'LLL:extensionManager', 'extensionName' => 'extensionmanager'),
				'Extension Manager'
			),
			array(
				array('content' => 0, 'alternative' => 'LLL:notfound'),
				'LLL:notfound'
			),
		);
	}

}
