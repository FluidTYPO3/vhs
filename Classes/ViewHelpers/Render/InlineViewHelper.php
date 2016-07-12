<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Render;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * ### Render: Inline
 *
 * Render as string containing Fluid as if it were
 * part of the template currently being rendered.
 *
 * Environment (template variables etc.) is cloned
 * but not re-merged after rendering, which means that
 * any and all changes in variables that happen while
 * rendering this inline code will be destroyed after
 * sub-rendering is finished.
 */
class InlineViewHelper extends AbstractRenderViewHelper
{

    /**
     * Initialize arguments
     *
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument(
            'namespaces',
            'array',
            'Optional additional/overridden namespaces, ["ns" => "MyVendor\\MyExt\\ViewHelpers"]',
            false,
            []
        );
    }

    /**
     * Renders an outside string as if it were Fluid code,
     * using additional (or overridden) namespaces if so
     * desired.
     *
     * @param string $content
     * @return string
     */
    public function render($content = null)
    {
        if (null === $content) {
            $content = $this->renderChildren();
        }
        $namespaces = $this->getPreparedNamespaces();
        $namespaceHeader = implode(LF, $namespaces);
        foreach ($namespaces as $namespace) {
            $content = str_replace($namespace, '', $content);
        }
        $view = $this->getPreparedClonedView();
        $view->setTemplateSource($namespaceHeader . $content);
        return $this->renderView($view);
    }
}
