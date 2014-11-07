<?php
namespace FluidTYPO3\Vhs\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * If content is empty use alternative text (can also be LLL:labelname shortcut or LLL:EXT: file paths).
 *
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers
 */
class OrViewHelper extends AbstractViewHelper {

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('alternative', 'mixed', 'Alternative if content is empty, can use LLL: shortcut', FALSE, '');
		$this->registerArgument('arguments', 'array', 'Arguments to be replaced in the resulting string', FALSE, NULL);
		$this->registerArgument('extensionName', 'string', 'UpperCamelCase extension name without vendor prefix', FALSE, NULL);
	}

	/**
	 * @param $content string
	 * @return string
	 */
	public function render($content = NULL) {
		$alternative = $this->arguments['alternative'];
		$arguments = (array) $this->arguments['arguments'];

		if (0 === count($arguments)) {
			$arguments = NULL;
		}

		if (NULL === $content) {
			$content = $this->renderChildren();
		}

		if (FALSE === empty($content)) {
			return NULL !== $arguments ? vsprintf($content, $arguments) : $content;
		}

		if (FALSE === is_string($alternative) || 0 !== strpos($alternative, 'LLL:')) {
			return $alternative;
		}

		if (0 === strpos($alternative, 'LLL:EXT:')) {
			return LocalizationUtility::translate($alternative, NULL, $arguments);
		}

		$translate = substr($alternative, 4);
		$extensionName = $this->arguments['extensionName'];

		if (NULL === $extensionName) {
			$request = $this->controllerContext->getRequest();
			$extensionName = $request->getControllerExtensionName();
		}

		$content = LocalizationUtility::translate($translate, $extensionName, $arguments);
		if (NULL === $content) {
			$content = $alternative;
		}

		return $content;
	}

}
