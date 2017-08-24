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
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Explode ViewHelper
 *
 * Explodes a string by $glue.
 */
class ExplodeViewHelper extends AbstractViewHelper implements CompilableInterface
{
    use CompileWithRenderStatic;
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
     * @var string
     */
    protected static $method = 'explode';

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
            'String used as glue in the string to be exploded. Use glue value of "constant:NAMEOFCONSTANT" ' .
            '(fx "constant:LF" for linefeed as glue)',
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
        $content = isset($arguments['content']) ? $arguments['content'] : $renderChildrenClosure();
        $glue = static::resolveGlue($arguments);
        $output = call_user_func_array(static::$method, [$glue, $content]);
        return static::renderChildrenWithVariableOrReturnInputStatic(
            $output,
            $arguments['as'],
            $renderingContext,
            $renderChildrenClosure
        );
    }

    /**
     * Detects the proper glue string to use for implode/explode operation
     *
     * @param array $arguments
     * @return string
     */
    protected static function resolveGlue(array $arguments)
    {
        $glue = $arguments['glue'];
        if (false !== mb_strpos($glue, ':') && 1 < mb_strlen($glue)) {
            // glue contains a special type identifier, resolve the actual glue
            list ($type, $value) = explode(':', $glue);
            switch ($type) {
                case 'constant':
                    $glue = constant($value);
                    break;
                default:
                    $glue = $value;
            }
        }
        return $glue;
    }
}
