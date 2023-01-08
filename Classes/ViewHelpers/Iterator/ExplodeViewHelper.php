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
 * Explode ViewHelper
 *
 * Explodes a string by $glue.
 */
class ExplodeViewHelper extends AbstractViewHelper
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
        $this->registerArgument('content', 'string', 'String to be exploded by glue');
        $this->registerArgument(
            'glue',
            'string',
            'String "glue" that separates values. If you need a constant (like PHP_EOL), use v:const to read it.',
            false,
            ','
        );
        $this->registerArgument(
            'limit',
            'int',
            'If limit is set and positive, the returned array will contain a maximum of limit elements with the last ' .
            'element containing the rest of string. If the limit parameter is negative, all components except the ' .
            'last-limit are returned. If the limit parameter is zero, then this is treated as 1.',
            false,
            PHP_INT_MAX
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
        $content = $arguments['content'];
        if (empty($arguments['as'])) {
            $content = $content ?? $renderChildrenClosure();
        }
        /** @var string&non-empty-string $glue */
        $glue = (string) $arguments['glue'];
        $limit = isset($arguments['limit']) ? $arguments['limit'] : PHP_INT_MAX;
        $output = explode($glue, (string) $content, $limit);
        return static::renderChildrenWithVariableOrReturnInputStatic(
            $output,
            $arguments['as'],
            $renderingContext,
            $renderChildrenClosure
        );
    }
}
