<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Content;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ArgumentOverride;
use FluidTYPO3\Vhs\Utility\ContextUtility;

/**
 * ViewHelper used to render get content elements in Fluid templates
 *
 * Does not work in the TYPO3 backend.
 */
class GetViewHelper extends AbstractContentViewHelper
{
    use ArgumentOverride;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->overrideArgument('render', 'boolean', 'Return rendered result', false, false);
    }

    /**
     * Render method
     *
     * @return mixed
     */
    public function render()
    {
        if (ContextUtility::isBackend()) {
            return '';
        }
        $contentRecords = $this->getContentRecords();
        return $contentRecords;
    }
}
