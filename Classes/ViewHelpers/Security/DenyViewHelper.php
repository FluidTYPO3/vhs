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
 */
class DenyViewHelper extends AbstractSecurityViewHelper implements ChildNodeAccessInterface
{

    /**
     * Overridden condition evaluation - full negation of verdict
     *
     * @param array|null $arguments
     * @return bool
     */
    protected static function evaluateCondition($arguments = null)
    {
        return !parent::evaluateCondition($arguments);
    }
}
