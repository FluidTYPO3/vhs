<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Content;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\ContextUtility;

/**
 * ViewHelper used to render get content elements in Fluid templates
 */
class GetViewHelper extends AbstractContentViewHelper
{
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
