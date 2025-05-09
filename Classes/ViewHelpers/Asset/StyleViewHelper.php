<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Asset;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ArgumentOverride;

/**
 * ### Basic Style ViewHelper
 *
 * Allows inserting a `<link>` or `<style>` Asset. Settings
 * specify where to insert the Asset and how to treat it.
 */
class StyleViewHelper extends AbstractAssetViewHelper
{
    use ArgumentOverride;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->overrideArgument(
            'movable',
            'boolean',
            'If TRUE, allows this Asset to be included in the document footer rather than the header. ' .
            'Should never be allowed for CSS.',
            false,
            false
        );
    }

    protected string $type = 'css';
}
