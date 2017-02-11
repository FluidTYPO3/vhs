<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use FluidTYPO3\Vhs\Utility\ViewHelperUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Repeats rendering of children with a typical for loop: starting at
 * index $from it will loop until the index has reached $to.
 */
class ForViewHelper extends AbstractLoopViewHelper implements CompilableInterface
{
    use CompileWithRenderStatic;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('to', 'integer', 'Number that the index needs to reach before stopping', true);
        $this->registerArgument('from', 'integer', 'Starting number for the index', false, 0);
        $this->registerArgument(
            'step',
            'integer',
            'Stepping number that the index is increased by after each loop',
            false,
            1
        );
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
        $to = (integer) $arguments['to'];
        $from = (integer) $arguments['from'];
        $step = (integer) $arguments['step'];
        $iteration = $arguments['iteration'];
        $content = '';
        $variableProvider = ViewHelperUtility::getVariableProviderFromRenderingContext($renderingContext);

        if (0 === $step) {
            throw new \RuntimeException('"step" may not be 0.', 1383267698);
        }
        if ($from < $to && 0 > $step) {
            throw new \RuntimeException('"step" must be greater than 0 if "from" is smaller than "to".', 1383268407);
        }
        if ($from > $to && 0 < $step) {
            throw new \RuntimeException('"step" must be smaller than 0 if "from" is greater than "to".', 1383268415);
        }

        if (true === $variableProvider->exists($iteration)) {
            $backupVariable = $variableProvider->get($iteration);
            $variableProvider->remove($iteration);
        }

        if ($from === $to) {
            $content = static::renderIteration($from, $from, $to, $step, $iteration, $renderingContext, $renderChildrenClosure);
        } elseif ($from < $to) {
            for ($i = $from; $i <= $to; $i += $step) {
                $content .= static::renderIteration($i, $from, $to, $step, $iteration, $renderingContext, $renderChildrenClosure);
            }
        } else {
            for ($i = $from; $i >= $to; $i += $step) {
                $content .= static::renderIteration($i, $from, $to, $step, $iteration, $renderingContext, $renderChildrenClosure);
            }
        }

        if (true === isset($backupVariable)) {
            $variableProvider->add($iteration, $backupVariable);
        }

        return $content;
    }
}
