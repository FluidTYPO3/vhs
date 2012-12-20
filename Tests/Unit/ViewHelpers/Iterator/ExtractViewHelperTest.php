<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Andreas Lappe <nd@kaeufli.ch>, kaeufli.ch
 *  
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
 * @author Andreas Lappe <nd@kaeufli.ch>
 * @package Vhs
 */
class Tx_Vhs_ViewHelpers_Iterator_ExtractViewHelperTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	/**
	 * @var Tx_Vhs_ViewHelpers_Condition_BrowserViewHelper
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = $this->getAccessibleMock('Tx_Vhs_ViewHelpers_Iterator_ExtractViewHelper', array('hasArgument'));
	}

	public function tearDown() {
		unset($this->fixture);
	}

	public function nestedStructures() {
		$structures = array(
			// structure, key, glue, expected
			'simple indexed_search searchWords array' => array(
				array(
					0 => array(
						'sword' => 'firstWord',
						'oper' => 'AND'
					),
				),
				'sword',
				' ',
			   	'firstWord'
			),
			'interesting indexed_search searchWords array' => array(
				array(
					0 => array(
						'sword' => 'firstWord',
						'oper' => 'AND'
					),
					1 => array(
						'sword' => 'secondWord',
						'oper' => 'AND'
					),
					3 => array(
						'sword' => 'thirdWord',
						'oper' => 'AND'
					)
				),
				'sword',
				', ',
			   	'firstWord, secondWord, thirdWord'
			),
			'ridiculously nested array' => array(
				array(
					array(
						array(
							array(
								array(
									array(
										'l' => 'some'
									)
								)
							),
							array(
								'l' => 'text'
							)
						)
					)
				),
				'l',
				' ',
				'some text'
			)
		);

		return $structures;
	}

	/**
	 * @test
	 * @dataProvider nestedStructures
	 */
	public function recursivelyExtractKey($structure, $key, $glue, $expected) {
		$this->assertSame(
			$expected,
			$this->fixture->render($structure, $key, $glue)
		);
	}
}
?>