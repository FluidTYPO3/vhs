<?php
namespace FluidTYPO3\Vhs\Traits;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Class CompilableWithRenderStatic
 *
 * Provides default methods for rendering and compiling
 * any ViewHelper that conforms to the `renderStatic`
 * method pattern.
 */
trait CompileWithRenderStatic
{
    /**
     * Default render method - simply calls renderStatic() with a
     * prepared set of arguments.
     *
     * @return mixed Rendered result
     * @api
     */
    public function render(): mixed
    {
        if (!$this->renderingContext instanceof RenderingContextInterface) {
            throw new \RuntimeException('Unable to render ViewHelper without rendering context.', 1706067600);
        }

        return static::renderStatic(
            $this->arguments,
            $this->buildRenderChildrenClosure(),
            $this->renderingContext,
        );
    }

    /**
     * @return \Closure
     * TYPO3 13 / Fluid 4 compatibility: keep this abstract signature untyped
     * because Fluid 4's AbstractViewHelper method has no return type.
     */
    abstract protected function buildRenderChildrenClosure();
}
