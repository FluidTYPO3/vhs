<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Extension;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * ### Extension: Loaded (Condition) ViewHelper
 *
 * Condition to check if an extension is loaded.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Extension
 */
class LoadedViewHelper extends AbstractConditionViewHelper {

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('extensionName', 'string', 'Name of extension that must be loaded in order to evaluate as TRUE, UpperCamelCase', TRUE);
	}

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		$extensionName = $this->arguments['extensionName'];
		$extensionKey = GeneralUtility::camelCaseToLowerCaseUnderscored($extensionName);
		$isLoaded = ExtensionManagementUtility::isLoaded($extensionKey);
		if (TRUE === $isLoaded) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}

}
