<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\TagViewHelperTrait;

/**
 * Base class for media related tag based view helpers which mostly
 * adds HTML5 tag attributes.
 */
abstract class AbstractMediaTagViewHelper extends AbstractMediaViewHelper
{

    use TagViewHelperTrait;

}
