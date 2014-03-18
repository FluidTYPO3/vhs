<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Andreas Lappe <nd@kaeufli.ch>, kaeufli.ch
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
class Tx_Vhs_ViewHelpers_Iterator_ExtractViewHelperTest extends Tx_Vhs_ViewHelpers_AbstractViewHelperTest {

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

	public function simpleStructures() {
		$structures = array(
			// structure, key, expected
			'flat associative array' => array(
				array('myKey' => 'myValue'),
				'myKey',
				'myValue'
			),
			'deeper associative array' => array(
				array(
					'myFirstKey' => array(
						'mySecondKey' => array(
							'myThirdKey' => 'myValue'
						)
					)
				),
				'myFirstKey.mySecondKey.myThirdKey',
				'myValue'
			),
		);

		return $structures;
	}

	public function constructObjectStorageContainingFrontendUser() {
		$storage = new Tx_Extbase_Persistence_ObjectStorage();
		$user1 = new Tx_Extbase_Domain_Model_FrontendUser();
		$user2 = new Tx_Extbase_Domain_Model_FrontendUser();
		$user3 = new Tx_Extbase_Domain_Model_FrontendUser();
		$user1->setFirstName('Peter');
		$user2->setFirstName('Paul');
		$user3->setFirstName('Marry');
		$storage->attach($user1);
		$storage->attach($user2);
		$storage->attach($user3);

		return $storage;
	}

	public function constructObjectStorageContainingFrontendUsersWithUserGroups() {
		$storage = new Tx_Extbase_Persistence_ObjectStorage();
		$userGroup1 = new Tx_Extbase_Domain_Model_FrontendUserGroup('my first group');
		$userGroup2 = new Tx_Extbase_Domain_Model_FrontendUserGroup('my second group');
		$user1 = new Tx_Extbase_Domain_Model_FrontendUser();
		$user2 = new Tx_Extbase_Domain_Model_FrontendUser();
		$user1->addUsergroup($userGroup1);
		$user2->addUsergroup($userGroup2);
		$storage->attach($user1);
		$storage->attach($user2);

		return $storage;
	}

	public function nestedStructures() {
		$structures = array(
			// structure, key, expected
			'simple indexed_search searchWords array' => array(
				array(
					0 => array(
						'sword' => 'firstWord',
						'oper' => 'AND'
					),
				),
				'sword',
				array(
					'firstWord'
				)
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
				array(
					'firstWord',
					'secondWord',
					'thirdWord'
				)
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
				array(
					0 => 'some',
					1 => 'text',
				)
			),
			'ObjectStorage containing FrontendUser' => array(
				$this->constructObjectStorageContainingFrontendUser(),
				'firstname',
				array(
					'Peter',
					'Paul',
					'Marry'
				)
			),
		);

		return $structures;
	}

	/**
	 * @test
	 * @dataProvider nestedStructures
	 */
	public function recursivelyExtractKey($structure, $key, $expected) {
		$recursive = TRUE;
		$this->assertSame(
			$expected,
			$this->fixture->render($key, $structure, $recursive)
		);
	}

	/**
	 * @test
	 * @dataProvider simpleStructures
	 */
	public function extractByKeyExtractsKeyByPath($structure, $key, $expected) {
		$this->assertSame(
			$expected,
			$this->fixture->extractByKey($structure, $key)
		);
	}
}
