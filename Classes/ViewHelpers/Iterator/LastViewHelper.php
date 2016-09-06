<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ArrayConsumingViewHelperTrait;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Returns the last element of $haystack.
 */
class LastViewHelper extends AbstractViewHelper
{

    use ArrayConsumingViewHelperTrait;

    /**
     * Initialize arguments
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('haystack', 'mixed', 'Haystack in which to look for needle');
    }

    /**
     * Render method
     *
     * @return mixed|NULL
     */
    public function render()
    {
        $haystack = $this->arguments['haystack'];
        if (null === $haystack) {
            $haystack = $this->renderChildren();
        }
        $haystack = $this->arrayFromArrayOrTraversableOrCSV($haystack);

        return array_pop($haystack);
    }
}
