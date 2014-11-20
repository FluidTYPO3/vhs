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
 * @protection off
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 */
class CaseViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 * @dataProvider getInputsAndExpectedOutputs
	 * @param string $input
	 * @param string $case
	 * @param string $expectedOutput
	 */
	public function convertsToExpectedFormat($input, $case, $expectedOutput) {
		$result1 = $this->executeViewHelper(array('string' => $input, 'case' => $case));
		$result2 = $this->executeViewHelperUsingTagContent('Text', $input, array('case' => $case));
		$this->assertEquals($expectedOutput, $result1);
		$this->assertEquals($expectedOutput, $result2);
	}

	/**
	 * @return array
	 */
	public function getInputsAndExpectedOutputs() {
		return array(
			array('lowerstring', CaseViewHelper::CASE_UPPER, 'LOWERSTRING'),
			array('UPPERSTRING', CaseViewHelper::CASE_LOWER, 'upperstring'),
			array('lots of words', CaseViewHelper::CASE_UCWORDS, 'Lots Of Words'),
			array('lowerstring', CaseViewHelper::CASE_UCFIRST, 'Lowerstring'),
			array('UPPERSTRING', CaseViewHelper::CASE_LCFIRST, 'uPPERSTRING'),
			array('lowercase_underscored', CaseViewHelper::CASE_CAMELCASE, 'LowercaseUnderscored'),
			array('lowercase_underscored', CaseViewHelper::CASE_LOWERCAMELCASE, 'lowercaseUnderscored'),
			array('CamelCase', CaseViewHelper::CASE_UNDERSCORED, 'camel_case'),
			array('unknown format MIXED WITH All Cases', 'unsupported', 'unknown format MIXED WITH All Cases')
		);
	}

}
