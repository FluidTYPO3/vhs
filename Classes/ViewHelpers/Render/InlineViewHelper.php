<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Render;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;

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
class InlineViewHelper extends AbstractRenderViewHelper implements CompilableInterface
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * Initialize arguments
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('content', 'string', 'Template code to render as Fluid (usually from a variable)');
        $this->registerArgument(
            'namespaces',
            'array',
            'Optional additional/overridden namespaces, ["ns" => "MyVendor\\MyExt\\ViewHelpers"]',
            false,
            []
        );
        parent::initializeArguments();
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $content = $renderChildrenClosure();
        $namespaces = static::getPreparedNamespaces($arguments);
        $namespaceHeader = implode(LF, $namespaces);
        foreach ($namespaces as $namespace) {
            $content = str_replace($namespace, '', $content);
        }
        $view = static::getPreparedClonedView($renderingContext);
        $view->setTemplateSource($namespaceHeader . $content);
        return static::renderView($view, $arguments);
    }
}
