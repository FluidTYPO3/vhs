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
 * Base class: Extension ViewHelpers
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Extension\Path
 */
abstract class Tx_Vhs_ViewHelpers_Extension_AbstractExtensionViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('extensionName', 'string', 'Name, in UpperCamelCase, of the extension to be checked', FALSE, NULL, TRUE);
	}

	/**
	 * @return string
	 */
	protected function getExtensionKey() {
		$extensionName = $this->getExtensionName();
		return \TYPO3\CMS\Core\Utility\GeneralUtility::camelCaseToLowerCaseUnderscored($extensionName);
	}

	/**
	 * @throws Exception
	 * @return mixed
	 */
	protected function getExtensionName() {
		if (TRUE === isset($this->arguments['extensionName']) && FALSE === empty($this->arguments['extensionName'])) {
			return $this->arguments['extensionName'];
		}
		$request = $this->controllerContext->getRequest();
		$extensionName = $request->getControllerExtensionName();
		if (TRUE === empty($extensionName)) {
			throw new RuntimeException('Unable to read extension name from ControllerContext and value not manually specified', 1364167519);
		}
		return $extensionName;
	}

}
