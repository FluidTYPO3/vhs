<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Content\Random;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * ViewHelper for fetching a random content element in Fluid page templates.
 */
class GetViewHelper extends RenderViewHelper
{

    /**
     * Initialize ViewHelper arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->overrideArgument('render', 'boolean', 'Returning variable as original table rows', false, false);
    }
}
