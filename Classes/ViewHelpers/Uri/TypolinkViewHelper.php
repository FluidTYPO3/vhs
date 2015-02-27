<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Uri;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### TypolinkViewhelper
 *
 * Renders a uri with the TypoLink function.
 * Can be used with the LinkWizard
 *
 * For more info on the typolink function, please consult the offical core-documentation:
 * http://docs.typo3.org/typo3cms/TyposcriptIn45MinutesTutorial/TypoScriptFunctions/Typolink/Index.html
 *
 * ### Examples
 *
 *     <!-- tag -->
 *     <v:uri.typolink configuration="{typoLinkConfiguration}" />
 *     <v:uri.typolink configuration="{object}">My LinkText</v:uri.typolink>
 *     <!-- with a {parameter} variable containing the PID -->
 *     <v:uri.typolink configuration="{parameter: parameter}" />
 *     <!-- with a {fields.link} variable from the LinkWizard (incl. 'class', 'target' etc.) inside a flux form -->
 *     <v:uri.typolink configuration="{parameter: fields.link}" />
 *     <!-- same with a {page} variable from fluidpages -->
 *     <v:uri.typolink configuration="{parameter: page.uid}" />
 *     <!-- With extensive configuration -->
 *     <v:uri.typolink configuration="{parameter: page.uid, additionalParams: '&print=1', title: 'Follow the link'}">Click Me!</v:uri.typolink>
 *
 * @author Cedric Ziel <cedric@cedric-ziel.com>, Cedric Ziel - Internetdienstleistungen & EDV
 * @package Vhs
 * @subpackage ViewHelpers
 */
class TypolinkViewHelper extends AbstractViewHelper {

	/**
	 * Initializes the arguments for the ViewHelper
	 */
	public function initializeArguments() {
		$this->registerArgument('configuration', 'array', 'The typoLink configuration', TRUE);
	}

	/**
	 * @return mixed
	 */
	public function render() {
		return $GLOBALS['TSFE']->cObj->typoLink_URL($this->arguments['configuration']);
	}

}
