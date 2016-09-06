<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media\Image;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Returns the width of the provided image file in pixels.
 */
class WidthViewHelper extends AbstractImageInfoViewHelper
{

    /**
     * @return int
     */
    public function render()
    {
        $info = $this->getInfo();
        return true === isset($info['width']) ? $info['width'] : 0;
    }
}
