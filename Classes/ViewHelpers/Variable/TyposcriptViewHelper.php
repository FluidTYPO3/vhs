<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Variable;

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
 *     <v:variable.set name="menuSettings" value="{v:variable.typoscript(path: 'lib.menu.main.stdWrap')}" />
 *     <v:variable.set name="wrap" value="{menuSettings.wrap -> v:iterator.explode(glue: '|')}" />
 *
 *     <!-- This in the loop which renders the menu (see "VHS: manual menu rendering" in FAQ): -->
 *     {wrap.0}{menuItem.title}{wrap.1}
 *
 *     <!-- An additional example to demonstrate very compact conditions which prevent wraps from being displayed -->
 *     {wrap.0 -> f:if(condition: settings.wrapBefore)}{menuItem.title}{wrap.1 -> f:if(condition: settings.wrapAfter)}
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Var
 */
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TyposcriptViewHelper extends AbstractViewHelper {

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * Render
	 *
	 * @param string $path
	 * @return mixed
	 */
	public function render($path = NULL) {
		if (NULL === $path) {
			$path = $this->renderChildren();
		}
		if (TRUE === empty($path)) {
			return NULL;
		}
		$all = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		$segments = explode('.', $path);
		$value = $all;
		foreach ($segments as $path) {
			if (TRUE === isset($value[$path . '.'])) {
				$value = $value[$path . '.'];
			} else {
				$value = $value[$path];
			}
		}
		if (TRUE === is_array($value)) {
			$value = GeneralUtility::removeDotsFromTS($value);
		}
		return $value;
	}

}
