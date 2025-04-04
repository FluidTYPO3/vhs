<?php
namespace FluidTYPO3\Vhs\Traits;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\Compiler\TemplateCompiler;
use TYPO3Fluid\Fluid\Core\Exception;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;

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
trait CompileWithContentArgumentAndRenderStatic
{
    /**
     * Name of variable that contains the value to use
     * instead of render children closure, if specified.
     * If no name is provided here, the first variable
     * registered in `initializeArguments` of the ViewHelper
     * will be used.
     *
     * Note: it is significantly better practice defining
     * this property in your ViewHelper class and so fix it
     * to one particular argument instead of resolving,
     * especially when your ViewHelper is called multiple
     * times within an uncompiled template!
     *
     * This property cannot be directly set in consuming
     * ViewHelper, instead set the property in ViewHelper
     * constructor '__construct()', for example with
     * $this->contentArgumentName = 'explicitlyToUseArgumentName';
     *
     * Another possible way would be to override the method
     * 'resolveContentArgumentName()' and return the name.
     *
     * public function resolveContentArgumentName()
     * {
     *     return 'explicitlyToUseArgumentName';
     * }
     *
     * Note: Setting this through 'initializeArguments()' will
     *       not work as expected, and other methods should be
     *       avoided to override this.
     *
     * Following test ViewHelpers are tested and demonstrates
     * that the setting posibillities works.
     *
     * @var string
     */
    protected $contentArgumentName;

    /**
     * Default render method to render ViewHelper with
     * first defined optional argument as content.
     *
     * @return mixed Rendered result
     * @api
     */
    public function render()
    {
        return static::renderStatic(
            $this->arguments,
            $this->buildRenderChildrenClosure(),
            $this->renderingContext,
        );
    }

    /**
     * @param string $argumentsName
     * @param string $closureName
     * @param string $initializationPhpCode
     * @param ViewHelperNode $node
     * @param TemplateCompiler $compiler
     * @return string
     */
    public function compile(
        $argumentsName,
        $closureName,
        &$initializationPhpCode,
        ViewHelperNode $node,
        TemplateCompiler $compiler
    ) {
        $initialization = '';
        $execution = sprintf(
            '%s::renderStatic(%s, %s, $renderingContext)',
            get_class($this),
            $argumentsName,
            $closureName,
        );

        $contentArgumentName = $this->resolveContentArgumentName();
        $initializationPhpCode .= sprintf(
            '%s = (%s[\'%s\'] !== null) ? function() use (%s) { return %s[\'%s\']; } : %s;',
            $closureName,
            $argumentsName,
            $contentArgumentName,
            $argumentsName,
            $argumentsName,
            $contentArgumentName,
            $closureName
        );

        $initializationPhpCode .= $initialization;
        return $execution;
    }

    /**
     * Helper which is mostly needed when calling renderStatic() from within
     * render().
     *
     * No public API yet.
     *
     * @return \Closure
     */
    protected function buildRenderChildrenClosure()
    {
        $argumentName = $this->resolveContentArgumentName();
        $arguments = $this->arguments;
        if (!empty($argumentName) && isset($arguments[$argumentName])) {
            $renderChildrenClosure = function () use ($arguments, $argumentName) {
                return $arguments[$argumentName];
            };
        } else {
            $self = clone $this;
            $renderChildrenClosure = function () use ($self) {
                return $self->renderChildren();
            };
        }
        return $renderChildrenClosure;
    }

    /**
     * Helper method which triggers the rendering of everything between the
     * opening and the closing tag.
     *
     * @return mixed The finally rendered child nodes.
     * @api
     */
    public function renderChildren()
    {
        if ($this->renderChildrenClosure !== null) {
            $closure = $this->renderChildrenClosure;
            return $closure();
        }
        return $this->viewHelperNode->evaluateChildNodes($this->renderingContext);
    }

    /**
     * @return string
     */
    public function resolveContentArgumentName()
    {
        if (empty($this->contentArgumentName)) {
            $registeredArguments = $this->prepareArguments();
            foreach ($registeredArguments as $registeredArgument) {
                if (!$registeredArgument->isRequired()) {
                    $this->contentArgumentName = $registeredArgument->getName();
                    return $this->contentArgumentName;
                }
            }
            throw new Exception(
                sprintf('Attempting to compile %s failed. Chosen compile method requires that ViewHelper has ' .
                    'at least one registered and optional argument', __CLASS__)
            );
        }
        return $this->contentArgumentName;
    }

    public function getContentArgumentName(): ?string
    {
        return $this->resolveContentArgumentName();
    }
}
