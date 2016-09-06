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
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

/**
 * Pops the last value off $subject (but does not change $subject itself as array_pop would).
 */
class PopViewHelper extends AbstractViewHelper
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
    }

    /**
     * @return mixed
     */
    public function render()
    {
        $subject = $this->getArgumentFromArgumentsOrTagContentAndConvertToArray('subject');
        $output = array_pop($subject);
        return $this->renderChildrenWithVariableOrReturnInput($output);
    }
}
