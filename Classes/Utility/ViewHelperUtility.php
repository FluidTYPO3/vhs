<?php
namespace FluidTYPO3\Vhs\Utility;
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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\TemplateVariableContainer;

/**
 * ViewHelper Utility
 *
 * Contains methods to manipulate and interact with
 * ViewHelperVariableContainer instances from ViewHelpers.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage Utility
 */
class ViewHelperUtility {

	/**
	 * Returns a backup of all $variables from $variableContainer and removes them.
	 *
	 * @param TemplateVariableContainer $variableContainer
	 * @param array $variables
	 * @return array
	 */
	public static function backupVariables(TemplateVariableContainer $variableContainer, array $variables) {
		$backups = array();

		foreach ($variables as $variableName => $variableValue) {
			if (TRUE === $variableContainer->exists($variableName)) {
				$backups[$variableName] = $variableContainer->get($variableName);
				$variableContainer->remove($variableName);
			}
			$variableContainer->add($variableName, $variableValue);
		}

		return $backups;
	}

	/**
	 * Restores $variables in $variableContainer
	 *
	 * @param TemplateVariableContainer $variableContainer
	 * @param array $variables
	 * @param array $backups
	 * @return void
	 */
	public static function restoreVariables(TemplateVariableContainer $variableContainer, array $variables, array $backups) {
		foreach ($variables as $variableName => $variableValue) {
			$variableContainer->remove($variableName);
			if (TRUE === isset($backups[$variableName])) {
				$variableContainer->add($variableName, $variableValue);
			}
		}
	}

	/**
	 * Renders tag content of ViewHelper and inserts variables
	 * in $variables into $variableContainer while keeping backups
	 * of each existing variable, restoring it after rendering.
	 * Returns the output of the renderChildren() method on $viewHelper.
	 *
	 * @param AbstractViewHelper $viewHelper
	 * @param TemplateVariableContainer $variableContainer
	 * @param array $variables
	 * @return mixed
	 */
	public static function renderChildrenWithVariables(AbstractViewHelper $viewHelper, TemplateVariableContainer $variableContainer, array $variables) {
		$backups = self::backupVariables($variableContainer, $variables);
		$content = $viewHelper->renderChildren();
		self::restoreVariables($variableContainer, $variables, $backups);

		return $content;
	}

	/**
	 * @param mixed $candidate
	 * @param boolean $useKeys
	 * @return array
	 */
	public static function arrayFromArrayOrTraversableOrCSV($candidate, $useKeys = TRUE) {
		if (TRUE === $candidate instanceof \Traversable) {
			return iterator_to_array($candidate, $useKeys);
		}
		if (TRUE === is_string($candidate)) {
			return GeneralUtility::trimExplode(',', $candidate, TRUE);
		}
		if (FALSE === is_array($candidate)) {
			return array($candidate);
		}
		return (array) $candidate;
	}

}
