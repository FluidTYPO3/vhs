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
 * ### Variable: Unset
 *
 * Quite simply, removes a currently available variable
 * from the TemplateVariableContainer:
 *
 *     <!-- Data: {person: {name: 'Elvis', nick: 'King'}} -->
 *     I'm {person.name}. Call me "{person.nick}". A ding-dang doo!
 *     <v:variable.unset name="person" />
 *     <f:if condition="{person}">
 *         <f:else>
 *             You saw this coming...
 *             <em>Elvis has left the building</em>
 *         </f:else>
 *     </f:if>
 *
 * At the time of writing this, `v:variable.unset` is not able
 * to remove members of for example arrays:
 *
 *     <!-- DOES NOT WORK! -->
 *     <v:variable.unset name="myObject.propertyName" />
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Var
 */
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

class UnsetViewHelper extends AbstractViewHelper {

	/**
	 * Unsets variable $name if it exists in the container
	 *
	 * @param string $name
	 * @return void
	 */
	public function render($name) {
		if (TRUE === $this->templateVariableContainer->exists($name)) {
			$this->templateVariableContainer->remove($name);
		}
	}

}
