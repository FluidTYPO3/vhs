<?php
namespace NamelessCoder\FluidGap\Traits;
use TYPO3\CMS\Fluid\Core\Compiler\TemplateCompiler;
use TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\AbstractNode;


/**
 * Class CompilableWithRenderStatic
 *
 * Provides default methods for rendering and compiling
 * any ViewHelper that conforms to the `renderStatic`
 * method pattern.
 */
trait CompileWithRenderStatic {

    /**
     * Default render method - simply calls renderStatic() with a
     * prepared set of arguments.
     *
     * @return string Rendered string
     * @api
     */
    public function render() {
        return static::renderStatic(
            $this->arguments,
            call_user_func_array(array($this, 'buildRenderChildrenClosure'), array()),
            $this->renderingContext
        );
    }

    /**
     * @param string $argumentsName
     * @param string $closureName
     * @param string $initializationPhpCode
     * @param AbstractNode $node
     * @param TemplateCompiler $compiler
     * @return string
     */
    public function compile(
        $argumentsName,
        $closureName,
        &$initializationPhpCode,
        AbstractNode $node,
        TemplateCompiler $compiler
    ) {
        return sprintf(
            '%s::renderStatic(%s, %s, $renderingContext)',
            static::class,
            $argumentsName,
            $closureName
        );
    }

}
