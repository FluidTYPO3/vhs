<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ArrayConsumingViewHelperTrait;
use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Adds one variable to the end of the array and returns the result.
 *
 * Example:
 *
 *     <f:for each="{array -> v:iterator.push(add: additionalObject, key: 'newkey')}" as="combined">
 *     ...
 *     </f:for>
 */
class PushViewHelper extends AbstractViewHelper
{

    use TemplateVariableViewHelperTrait;
    use ArrayConsumingViewHelperTrait;


    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerAsArgument();
        $this->registerArgument('subject', 'mixed', 'Input to work on - Array/Traversable/...');
        $this->registerArgument('add', 'mixed', 'Member to add to end of array', true);
        $this->registerArgument('key', 'mixed', 'Optional key to use. If key exists the member will be overwritten!');
    }

    /**
     * @return mixed
     */
    public function render()
    {
        $subject = $this->getArgumentFromArgumentsOrTagContentAndConvertToArray('subject');
        $add = $this->arguments['add'];
        $key = $this->arguments['key'];
        if ($key) {
            $subject[$key] = $add;
        } else {
            $subject[] = $add;
        }
        return $this->renderChildrenWithVariableOrReturnInput($subject);
    }
}
