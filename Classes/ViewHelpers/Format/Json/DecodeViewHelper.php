<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format\Json;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

/**
 * Converts the JSON encoded argument into a PHP variable
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Format\Json
 */
class DecodeViewHelper extends AbstractViewHelper {

	/**
	 * @param string $json
	 * @throws Exception
	 * @return mixed
	 */
	public function render($json = NULL) {
		if (NULL === $json) {
			$json = $this->renderChildren();
			if (TRUE === empty($json)) {
				return NULL;
			}
		}

		$value = json_decode($json, TRUE);

		if (JSON_ERROR_NONE !== json_last_error()) {
			throw new Exception('The provided argument is invalid JSON.', 1358440054);
		}

		return $value;
	}

}
