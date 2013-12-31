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
 * Once: Instance
 *
 * Displays nested content or "then" child once per instance
 * of the content element or plugin being rendered, as identified
 * by the contentObject UID (or globally if no contentObject
 * is associated).
 *
 * "Once"-style ViewHelpers are purposed to only display their
 * nested content once per XYZ, where the XYZ depends on the
 * specific type of ViewHelper (session, cookie etc).
 *
 * In addition the ViewHelper is a ConditionViewHelper, which
 * means you can utilize the f:then and f:else child nodes as
 * well as the "then" and "else" arguments.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Once
 */
class Tx_Vhs_ViewHelpers_Once_InstanceViewHelper extends Tx_Vhs_ViewHelpers_Once_AbstractOnceViewHelper {

	/**
	 * @return string
	 */
	protected function getIdentifier() {
		if (isset($this->arguments['identifier']) === TRUE && $this->arguments['identifier'] !== NULL) {
			return $this->arguments['identifier'];
		}
		$request = $this->controllerContext->getRequest();
		$identifier = implode('_', array(
			$request->getControllerActionName(),
			$request->getControllerObjectName(),
			$request->getPluginName(),
			$request->getControllerExtensionName()
		));
		return $identifier;
	}

	/**
	 * @return void
	 */
	protected function storeIdentifier() {
		$index = get_class($this);
		$identifier = $this->getIdentifier();
		if (is_array($GLOBALS[$index]) === FALSE) {
			$GLOBALS[$index] = array();
		}
		$GLOBALS[$index][$identifier] = TRUE;
	}

	/**
	 * @return boolean
	 */
	protected function assertShouldSkip() {
		$index = get_class($this);
		$identifier = $this->getIdentifier();
		return isset($GLOBALS[$index][$identifier]);
	}

}
