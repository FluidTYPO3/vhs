<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Claus Due <claus@wildside.dk>
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
 * @protection off
 * @author Claus Due <claus@wildside.dk>
 * @package Vhs
 */
class Tx_Vhs_ViewHelpers_If_Type_IsTraversableViewHelperTest extends Tx_Vhs_ViewHelpers_AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function rendersThenChildIfConditionMatched() {
		$this->assertEquals('then', $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'value' => new \TYPO3\CMS\Extbase\Persistence\ObjectStorage())));
	}

	/**
	 * @test
	 */
	public function rendersElseChildIfConditionNotMatched() {
		$this->assertEquals('else', $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'value' => 1)));
	}

}
