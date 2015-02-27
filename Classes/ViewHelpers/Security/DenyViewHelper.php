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
 * ### Security: Deny
 *
 * Denies access to the child content based on given arguments.
 * The ViewHelper is a condition based ViewHelper which means it
 * supports the `f:then` and `f:else` child nodes.
 *
 * Is the mirror opposite of `v:security.allow`.
 *
 * @author Claus Due
 * @package Vhs
 * @subpackage ViewHelpers\Security
 */
class DenyViewHelper extends AbstractSecurityViewHelper implements ChildNodeAccessInterface {

	/**
	 * Render deny - i.e. render "else" child only if arguments are satisfied,
	 * resulting in an inverse match.
	 *
	 * @return string
	 */
	public function render() {
		$evaluation = $this->evaluateArguments();
		if (FALSE === $evaluation) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}

}
