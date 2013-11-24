<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Claus Due <claus@wildside.dk>, Wildside A/S
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
 * Case Formatting ViewHelper
 *
 * Formats string case according to provided arguments
 *
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Vhs
 * @subpackage ViewHelpers\Format
 */
class Tx_Vhs_ViewHelpers_Format_CaseViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	const CASE_UPPER = 'upper';
	const CASE_LOWER = 'lower';
	const CASE_UCWORDS = 'ucwords';
	const CASE_UCFIRST = 'ucfirst';
	const CASE_LCFIRST = 'lcfirst';
	const CASE_CAMELCASE = 'CamelCase';
	const CASE_LOWERCAMELCASE = 'lowerCamelCase';
	const CASE_UNDERSCORED = 'lowercase_underscored';

	/**
	 * @param string $string
	 * @param string $case
	 * @return string
	 */
	public function render($string = NULL, $case = NULL) {
		if ($string === NULL) {
			$string = $this->renderChildren();
		}
		switch ($case) {
			case self::CASE_LOWER: $string = mb_strtolower($string); break;
			case self::CASE_UPPER: $string = mb_strtoupper($string); break;
			case self::CASE_UCWORDS: $string = ucwords($string); break;
			case self::CASE_UCFIRST: $string{0} = strtoupper($string{0}); break;
			case self::CASE_LCFIRST: $string{0} = strtolower($string{0}); break;
			case self::CASE_CAMELCASE: $string = \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($string); break;
			case self::CASE_LOWERCAMELCASE: $string = \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToLowerCamelCase($string); break;
			case self::CASE_UNDERSCORED: $string = \TYPO3\CMS\Core\Utility\GeneralUtility::camelCaseToLowerCaseUnderscored($string); break;
			default: break;
		}
		return $string;
	}

}
