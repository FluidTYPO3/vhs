<?php
namespace FluidTYPO3\Vhs\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * ### Call ViewHelper
 *
 * Calls a method on an existing object. Usable as inline or tag.
 *
 * ### Examples
 *
 *     <!-- inline, useful as argument, for example in f:for -->
 *     {object -> v:call(method: 'toArray')}
 *     <!-- tag, useful to quickly output simple values -->
 *     <v:call object="{object}" method="unconventionalGetter" />
 *     <v:call method="unconventionalGetter">{object}</v:call>
 *     <!-- arguments for the method -->
 *     <v:call object="{object}" method="doSomethingWithArguments" arguments="{0: 'foo', 1: 'bar'}" />
 */
class CallViewHelper extends AbstractViewHelper implements CompilableInterface
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('object', 'object', 'Instance to call method on');
        $this->registerArgument('method', 'string', 'Name of method to call on instance', true);
        $this->registerArgument('arguments', 'array', 'Array of arguments if method requires arguments', false, []);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $object = $renderChildrenClosure();
        $method = $arguments['method'];
        $methodArguments = $arguments['arguments'];
        if (false === is_object($object)) {
            throw new \RuntimeException(
                'Using v:call requires an object either as "object" attribute, tag content or inline argument',
                1356849652
            );
        }
        if (false === method_exists($object, $method)) {
            throw new \RuntimeException(
                'Method "' . $method . '" does not exist on object of type ' . get_class($object),
                1356834755
            );
        }
        return call_user_func_array([$object, $method], $methodArguments);
    }
}
