<?php
namespace FluidTYPO3\Vhs\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### Const ViewHelper
 *
 * Renders the value of a PHP constant
 */
class ConstViewHelper extends AbstractViewHelper
{

    /**
     * @param string $name
     * @return mixed
     */
    public function render($name)
    {
        if ($name === null) {
            $name = $this->renderChildren();
        }
        return constant($name);
    }
}
