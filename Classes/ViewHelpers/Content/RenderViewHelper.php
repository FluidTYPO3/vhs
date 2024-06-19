<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Content;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use FluidTYPO3\Vhs\Utility\ContextUtility;

/**
 * ViewHelper used to render content elements in Fluid templates.
 *
 * ### Render a single content element by its UID
 *
 * Let's assume that the variable `settings.element.uid` contains the uid
 * of a content element.
 * It can be rendered as follows:
 *
 * ```
 * <v:content.render contentUids="{0: settings.element.uid}"/>
 * ```
 */
class RenderViewHelper extends AbstractContentViewHelper
{
    use TemplateVariableViewHelperTrait;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerAsArgument();
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

        $content = $this->getContentRecords();
        if (!$this->hasArgument('as')) {
            return implode(LF, $content);
        }

        return $this->renderChildrenWithVariableOrReturnInput($content);
    }
}
