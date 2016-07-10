<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

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
 */
class UnsetViewHelper extends AbstractViewHelper
{

    /**
     * Unsets variable $name if it exists in the container
     *
     * @param string $name
     * @return void
     */
    public function render($name)
    {
        if (true === $this->templateVariableContainer->exists($name)) {
            $this->templateVariableContainer->remove($name);
        }
    }
}
