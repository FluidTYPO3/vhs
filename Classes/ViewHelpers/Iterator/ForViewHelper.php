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
 * Repeats rendering of children with a typical for loop: starting at
 * index $from it will loop until the index has reached $to.
 */
class ForViewHelper extends AbstractLoopViewHelper
{
    public function initializeArguments(): void
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
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        /** @var int|string $to */
        $to = $arguments['to'];
        /** @var int|string $from */
        $from = $arguments['from'];
        /** @var int|string $step */
        $step = $arguments['step'];
        /** @var string|null $iteration */
        $iteration = $arguments['iteration'];
        $content = '';
        $variableProvider = $renderingContext->getVariableProvider();

        $to = (integer) $to;
        $from = (integer) $from;
        $step = (integer) $step;

        if (0 === $step) {
            throw new \RuntimeException('"step" may not be 0.', 1383267698);
        }
        if ($from < $to && 0 > $step) {
            throw new \RuntimeException('"step" must be greater than 0 if "from" is smaller than "to".', 1383268407);
        }
        if ($from > $to && 0 < $step) {
            throw new \RuntimeException('"step" must be smaller than 0 if "from" is greater than "to".', 1383268415);
        }

        if ($iteration !== null && $variableProvider->exists($iteration)) {
            $backupVariable = $variableProvider->get($iteration);
            $variableProvider->remove($iteration);
        }

        if ($from === $to) {
            $content = static::renderIteration(
                $from,
                $from,
                $to,
                $step,
                $iteration,
                $renderingContext,
                $renderChildrenClosure
            );
        } elseif ($from < $to) {
            for ($i = $from; $i <= $to; $i += $step) {
                $content .= static::renderIteration(
                    $i,
                    $from,
                    $to,
                    $step,
                    $iteration,
                    $renderingContext,
                    $renderChildrenClosure
                );
            }
        } else {
            for ($i = $from; $i >= $to; $i += $step) {
                $content .= static::renderIteration(
                    $i,
                    $from,
                    $to,
                    $step,
                    $iteration,
                    $renderingContext,
                    $renderChildrenClosure
                );
            }
        }

        if ($iteration !== null && isset($backupVariable)) {
            $variableProvider->add($iteration, $backupVariable);
        }

        return $content;
    }
}
