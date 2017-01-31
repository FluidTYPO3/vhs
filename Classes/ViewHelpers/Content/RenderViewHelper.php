<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Content;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;

/**
 * ViewHelper used to render content elements in Fluid templates.
 *
 * ### Render a single content element by its UID
 *
 * Let's assume that the variable `settings.element.uid` contains the uid
 * of a content element.
 * It can be rendered as follows:
 *
 *     <v:content.render contentUids="{0: settings.element.uid}"/>
 */
class RenderViewHelper extends AbstractContentViewHelper
{

    use TemplateVariableViewHelperTrait;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerAsArgument();
    }

    /**
     * Render method
     *
     * @return string
     */
    public function render()
    {
        if ('BE' === TYPO3_MODE) {
            return '';
        }

        $content = $this->getContentRecords();
        if (false === $this->hasArgument('as')) {
            $content = implode(LF, $content);
        }

        return $this->renderChildrenWithVariableOrReturnInput($content);
    }
}
