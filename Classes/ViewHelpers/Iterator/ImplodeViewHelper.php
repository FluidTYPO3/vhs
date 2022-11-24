<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ArrayConsumingViewHelperTrait;
use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Implode ViewHelper
 *
 * Implodes an array or array-convertible object by $glue.
 */
class ImplodeViewHelper extends AbstractViewHelper
{
    use TemplateVariableViewHelperTrait;
    use ArrayConsumingViewHelperTrait;

    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * Initialize
     *
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('content', 'array', 'Array or array-convertible object to be imploded by glue');
        $this->registerArgument(
            'glue',
            'string',
            'String used as glue in the string to be exploded. To read a constant (like PHP_EOL) use v:const.',
            false,
            ','
        );
        $this->registerAsArgument();
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
        $content = !empty($arguments['as'])
            ? $arguments['content']
            : ($arguments['content'] ?? $renderChildrenClosure());
        $glue = $arguments['glue'];
        $output = implode($glue, $content);
        return static::renderChildrenWithVariableOrReturnInputStatic(
            $output,
            $arguments['as'],
            $renderingContext,
            $renderChildrenClosure
        );
    }
}
