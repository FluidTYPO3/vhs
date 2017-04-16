<?php
namespace FluidTYPO3\Vhs\ViewHelpers;

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
 * ### ExtConf ViewHelper
 *
 * Reads settings from ext_conf_template.txt
 *
 * ### Examples
 *
 * {v:extConf(name:'foo',extension_key:'bar')}
 *
 * Returns setting 'foo' from extension 'bar' located in ext_conf_template.txt
 *
 * @author Harry Glatz <glatz@analog.de>
 * @package Vhs
 * @subpackage ViewHelpers
 */
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

class ExtConfViewHelper extends AbstractViewHelper {
	/**
	 * @param string $name
	 * @param string $extension_key
	 * @return string
	 */

	public function render($name, $extension_key) {
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$extension_key]);
		return $extConf[$name];
	}
}
