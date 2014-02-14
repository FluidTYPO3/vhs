<?php
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
 * Explode ViewHelper
 *
 * Explodes a string by $glue
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class Tx_Vhs_ViewHelpers_Iterator_ExplodeViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var string
	 */
	protected $method = 'explode';

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('content', 'string', 'String to be exploded by glue)', FALSE, '');
		$this->registerArgument('glue', 'string', 'String used as glue in the string to be exploded. Use glue value of "constant:NAMEOFCONSTANT" (fx "constant:LF" for linefeed as glue)', FALSE, ',');
		$this->registerArgument('as', 'string', 'Template variable name to assign. If not specified returns the result array instead');
	}

	/**
	 * Render method
	 *
	 * @return mixed
	 */
	public function render() {
		$content = $this->arguments['content'];
		$as = $this->arguments['as'];
		$glue = $this->resolveGlue();
		$contentWasSource = FALSE;
		if (TRUE === empty($content)) {
			$content = $this->renderChildren();
			$contentWasSource = TRUE;
		}
		$output = call_user_func_array($this->method, array($glue, $content));
		if (TRUE === empty($as) || TRUE === $contentWasSource) {
			return $output;
		}
		$variables = array($as => $output);
		$content = Tx_Vhs_Utility_ViewHelperUtility::renderChildrenWithVariables($this, $this->templateVariableContainer, $variables);
		return $content;
	}

	/**
	 * Detects the proper glue string to use for implode/explode operation
	 *
	 * @return string
	 */
	protected function resolveGlue() {
		$glue = $this->arguments['glue'];
		if (FALSE !== strpos($glue, ':') && 1 < strlen($glue)) {
			// glue contains a special type identifier, resolve the actual glue
			list ($type, $value) = explode(':', $glue);
			switch ($type) {
				case 'constant':
					$glue = constant($value);
					break;
				default:
					$glue = $value;
			}
		}
		return $glue;
	}

}
