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
 ***************************************************************/

/**
 * ### L (localisation) ViewHelper
 *
 * An extremely shortened and much more dev-friendly
 * alternative to f:translate. Automatically outputs
 * the name of the LLL reference if it is not found
 * and the default value is not set, making it much
 * easier to identify missing labels when translating.
 *
 * ### Examples
 *
 *     <v:l>some.label</v:l>
 *     <v:l key="some.label" />
 *     <v:l arguments="{0: 'foo', 1: 'bar'}">some.label</v:l>
 *
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Vhs
 * @subpackage ViewHelpers
 */
class Tx_Vhs_ViewHelpers_LViewHelper extends Tx_Fluid_ViewHelpers_TranslateViewHelper implements t3lib_Singleton {

	/**
	 * @param string $key
	 * @param string $default
	 * @param boolean $htmlEscape
	 * @param array $arguments
	 * @param string $extensionName
	 * @return string
	 */
	public function render($key = NULL, $default = NULL, $htmlEscape = TRUE, array $arguments = NULL, $extensionName = NULL) {
		if (NULL === $key) {
			$key = $this->renderChildren();
		}
		if (NULL === $default) {
			$default = $key;
		}
		if (NULL === $extensionName) {
			$extensionName = $request->getControllerExtensionName();
		}
		$value = Tx_Extbase_Utility_Localization::translate($key, $extensionName, $arguments);
		if (NULL === $value) {
			$value = $default;
			if (is_array($arguments)) {
				$value = vsprintf($value, $arguments);
			}
		} elseif ($htmlEscape) {
			$value = htmlspecialchars($value);
		}
		return $value;
	}

}
