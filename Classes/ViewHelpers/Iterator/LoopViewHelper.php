<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Repeats rendering of children $count times while updating $iteration.
 */
class LoopViewHelper extends AbstractLoopViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument('count', 'integer', 'Number of times to render child content', true);
        $this->registerArgument('minimum', 'integer', 'Minimum number of loops before stopping', false, 0);
        $this->registerArgument('maximum', 'integer', 'Maxiumum number of loops before stopping', false, PHP_INT_MAX);
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $count = (integer) $arguments['count'];
        $minimum = (integer) $arguments['minimum'];
        $maximum = (integer) $arguments['maximum'];
        $iteration = $arguments['iteration'];
        $content = '';
        $variableProvider = $renderingContext->getVariableProvider();

        if ($count < $minimum) {
            $count = $minimum;
        } elseif ($count > $maximum) {
            $count = $maximum;
        }

        if ($variableProvider->exists($iteration)) {
            $backupVariable = $variableProvider->get($iteration);
            $variableProvider->remove($iteration);
        }

        for ($i = 0; $i < $count; $i++) {
            $content .= static::renderIteration(
                $i,
                0,
                $count,
                1,
                $iteration,
                $renderingContext,
                $renderChildrenClosure
            );
        }

        if (isset($backupVariable)) {
            $variableProvider->add($iteration, $backupVariable);
        }

        return $content;
    }

    protected static function isLast(int $i, int $from, int $to, int $step): bool
    {
        return ($i + $step >= $to);
    }
}
