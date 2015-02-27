<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Security;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\ChildNodeAccessInterface;

/**
 * ### Security: Allow
 *
 * Allows access to the child content based on given arguments.
 * The ViewHelper is a condition based ViewHelper which means it
 * supports the `f:then` and `f:else` child nodes - you can use
 * this behaviour to invert the access (i.e. use f:else in a check
 * if a frontend user is logged in, if you want to hide content
 * from authenticated users):
 *
 *     <v:security.allow anyFrontendUser="TRUE">
 *         <f:then><!-- protected information displayed --></f:then>
 *         <f:else><!-- link to login form displayed --></f:else>
 *     </v:security.allow>
 *
 * Is the mirror opposite of `v:security.deny`.
 *
 * @author Claus Due
 * @package Vhs
 * @subpackage ViewHelpers\Security
 */
class AllowViewHelper extends AbstractSecurityViewHelper implements ChildNodeAccessInterface {

	/**
	 * Render allow - i.e. render "then" child if arguments are satisfied
	 *
	 * @return string
	 */
	public function render() {
		$evaluation = $this->evaluateArguments();
		if (TRUE === $evaluation) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}

}
