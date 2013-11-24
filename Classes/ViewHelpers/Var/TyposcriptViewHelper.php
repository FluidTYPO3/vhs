<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Claus Due <claus@wildside.dk>, Wildside A/S
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
 * ### Variable: TypoScript
 *
 * Accesses Typoscript paths. Contrary to the Fluid-native
 * `f:cObject` this ViewHelper does not render objects but
 * rather retrieves the values. For example, if you retrieve
 * a TypoScript path to a TMENU object you will receive the
 * array of TypoScript defining the menu - not the rendered
 * menu HTML.
 *
 * A great example of how to use this ViewHelper is to very
 * quickly migrate a TypoScript-menu-based site (for example
 * currently running TemplaVoila + TMENU-objects) to a Fluid
 * ViewHelper menu based on `v:page.menu` or `v:page.breadCrumb`
 * by accessing key configuration options such as `entryLevel`
 * and even various `wrap` definitions.
 *
 * A quick example of how to parse a `wrap` TypoScript setting
 * into two variables usable for a menu item:
 *
 *     <!-- This piece to be added as far up as possible in order to prevent multiple executions -->
 *     <v:var.set name="menuSettings" value="{v:var.typoscript(path: 'lib.menu.main.stdWrap')}" />
 *     <v:var.set name="wrap" value="{menuSettings.wrap -> v:iterator.explode(glue: '|')}" />
 *
 *     <!-- This in the loop which renders the menu (see "VHS: manual menu rendering" in FAQ): -->
 *     {wrap.0}{menuItem.title}{wrap.1}
 *
 *     <!-- An additional example to demonstrate very compact conditions which prevent wraps from being displayed -->
 *     {wrap.0 -> f:if(condition: settings.wrapBefore)}{menuItem.title}{wrap.1 -> f:if(condition: settings.wrapAfter)}
 *
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Vhs
 * @subpackage ViewHelpers\Var
 */
class Tx_Vhs_ViewHelpers_Var_TyposcriptViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * Render
	 *
	 * @param string $path
	 * @return mixed
	 */
	public function render($path = NULL) {
		if ($path === NULL) {
			$path = $this->renderChildren();
		}
		if (!$path) {
			return NULL;
		}
		$all = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		$segments = explode('.', $path);
		$value = $all;
		foreach ($segments as $path) {
			if (isset($value[$path . '.'])) {
				$value = $value[$path . '.'];
			} else {
				$value = $value[$path];
			}
		}
		if (is_array($value)) {
			$value = \TYPO3\CMS\Core\Utility\GeneralUtility::removeDotsFromTS($value);
		}
		return $value;
	}

}
