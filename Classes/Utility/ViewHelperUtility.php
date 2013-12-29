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
class Tx_Vhs_Utility_ViewHelperUtility {

	/**
	 * Returns a backup of all $variables from $variableContainer and removes them.
	 *
	 * @param Tx_Fluid_Core_ViewHelper_TemplateVariableContainer $variableContainer
	 * @param array $variables
	 * @return array
	 */
	public static function backupVariables(Tx_Fluid_Core_ViewHelper_TemplateVariableContainer $variableContainer, array $variables) {
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
	 * @param Tx_Fluid_Core_ViewHelper_TemplateVariableContainer $variableContainer
	 * @param array $variables
	 * @param array $backups
	 * @return void
	 */
	public static function restoreVariables(Tx_Fluid_Core_ViewHelper_TemplateVariableContainer $variableContainer, array $variables, array $backups) {
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
	 * @param Tx_Fluid_Core_ViewHelper_AbstractViewHelper $viewHelper
	 * @param Tx_Fluid_Core_ViewHelper_TemplateVariableContainer $variableContainer
	 * @param array $variables
	 * @return mixed
	 */
	public static function renderChildrenWithVariables(Tx_Fluid_Core_ViewHelper_AbstractViewHelper $viewHelper, Tx_Fluid_Core_ViewHelper_TemplateVariableContainer $variableContainer, array $variables) {
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
		if (TRUE === $candidate instanceof Traversable) {
			return iterator_to_array($candidate, $useKeys);
		}
		if (TRUE === is_string($candidate)) {
			return t3lib_div::trimExplode(',', $candidate, TRUE);
		}
		if (FALSE === is_array($candidate)) {
			return array($candidate);
		}
		return (array) $candidate;
	}

	/**
	 * @return boolean
	 */
	public static function insideUncachedEnvironment() {
		$objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$configurationManager = $objectManager->get('Tx_Extbase_Configuration_ConfigurationManagerInterface');
		$contentObject = $configurationManager->getContentObject();
		return 1 === $GLOBALS['TSFE']->no_cache || 1 === $contentObject->INT_include;
	}

}
