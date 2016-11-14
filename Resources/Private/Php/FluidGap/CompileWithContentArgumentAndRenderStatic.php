<?php
namespace NamelessCoder\FluidGap\Traits;

use TYPO3\CMS\Fluid\Core\Compiler\TemplateCompiler;
use TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\AbstractNode;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;


/**
 * Class CompilableWithContentArgumentAndRenderStatic
 *
 * Provides default methods for rendering and compiling
 * any ViewHelper that conforms to the `renderStatic`
 * method pattern but has the added common use case that
 * an argument value must be checked and used instead of
 * the normal render children closure, if that named
 * argument is specified and not empty.
 */
trait CompileWithContentArgumentAndRenderStatic {

    /**
     * Name of variable that contains the value to use
     * instead of render children closure, if specified.
     * If no name is provided here, the first variable
     * registered in `initializeArguments` of the ViewHelper
     * will be used.
     *
     * Note: it is significantly better practice to define
     * this property in your ViewHelper class and so fix it
     * to one particular argument instead of resolving,
     * especially when your ViewHelper is called multiple
     * times within an uncompiled template!
     *
     * @var string
     */
    protected $contentArgumentName;

    /**
     * Default render method to render ViewHelper with
     * first defined optional argument as content.
     *
     * @return string Rendered string
     * @api
     */
    public function render() {
        $argumentName = $this->resolveContentArgumentName();
        $arguments = $this->arguments;
        if (!empty($argumentName) && isset($arguments[$argumentName])) {
            $renderChildrenClosure = function() use ($arguments, $argumentName) { return $arguments[$argumentName]; };
        } else {
            $renderChildrenClosure = call_user_func_array(array($this, 'buildRenderChildrenClosure'), array());
        }
        return static::renderStatic(
            $arguments,
            $renderChildrenClosure,
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
        $contentArgumentName = $this->resolveContentArgumentName();
        return sprintf(
            '%s::renderStatic(%s, isset(%s[\'%s\']) ? function() use (%s); return %s[\'%s\']; } : %s, $renderingContext)',
            static::class,
            $argumentsName,
            $argumentsName,
            $contentArgumentName,
            $argumentsName,
            $argumentsName,
            $contentArgumentName,
            $closureName
        );
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function resolveContentArgumentName() {
        if (empty($this->contentArgumentName)) {
            $registeredArguments = call_user_func_array(array($this, 'prepareArguments'), array());
            foreach ($registeredArguments as $registeredArgument) {
                if (!$registeredArgument->isRequired()) {
                    return $registeredArgument->getName();
                }
            }
            throw new Exception(
                'Attempting to compile %s failed. Chosen compile method requires that ViewHelper has ' .
                'at least one registered and optional argument'
            );
        }
        return $this->contentArgumentName;
    }

}
