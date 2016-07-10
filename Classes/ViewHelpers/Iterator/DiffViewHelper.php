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
 * Computes the difference of arrays.
 */
class DiffViewHelper extends AbstractViewHelper
{

    use ArrayConsumingViewHelperTrait;

    /**
     * Initialize
     *
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('a', 'mixed', 'First Array/Traversable/CSV');
        $this->registerArgument('b', 'mixed', 'Second Array/Traversable/CSV', true);
    }

    /**
     * @return array
     */
    public function render()
    {
        $a = $this->arguments['a'];
        if (null === $a) {
            $a = $this->renderChildren();
        }

        $a = $this->arrayFromArrayOrTraversableOrCSV($a);
        $b = $this->arrayFromArrayOrTraversableOrCSV($this->arguments['b']);

        return array_diff($a, $b);
    }
}
