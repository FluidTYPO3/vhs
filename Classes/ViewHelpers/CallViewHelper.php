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
 * ### Call ViewHelper
 *
 * Calls a method on an existing object. Usable as inline or tag.
 *
 * ### Examples
 *
 *     <!-- inline, useful as argument, for example in f:for -->
 *     {object -> v:call(method: 'toArray')}
 *     <!-- tag, useful to quickly output simple values -->
 *     <v:call object="{object}" method="unconventionalGetter" />
 *     <v:call method="unconventionalGetter">{object}</v:call>
 *     <!-- arguments for the method -->
 *     <v:call object="{object}" method="doSomethingWithArguments" arguments="{0: 'foo', 1: 'bar'}" />
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers
 */
class Tx_Vhs_ViewHelpers_CallViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @param string $method
	 * @param object $object
	 * @param array $arguments
	 * @throws Exception
	 * @return mixed
	 */
	public function render($method, $object = NULL, array $arguments = array()) {
		if ($object === NULL) {
			$object = $this->renderChildren();
			if (is_object($object) === FALSE) {
				throw new RuntimeException('Using v:call requires an object either as "object" attribute, tag content or inline argument', 1356849652);
			}
		}
		if (!method_exists($object, $method)) {
			throw new RuntimeException('Method "' . $method . '" does not exist on object of type ' . get_class($object), 1356834755);
		}
		return call_user_func_array(array($object, $method), $arguments);
	}

}
