<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Uri;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\ViewHelpers\Media\Image\AbstractImageViewHelper;

/**
 * ### Uri: Image
 *
 * Returns the relative or absolute URI for the image resource
 * or it's derivate if differing dimesions are provided.
 */
class ImageViewHelper extends AbstractImageViewHelper
{

    /**
     * Render method
     *
     * @return string
     */
    public function render()
    {
        $this->preprocessImage();
        $src = static::preprocessSourceUri($this->mediaSource, $this->arguments);
        return $src;
    }
}
