<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Abstract class with basic functionality for loop view helpers.
 */
abstract class AbstractLoopViewHelper extends AbstractViewHelper
{
    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('iteration', 'string', 'Variable name to insert result into, suppresses output');
    }

    /**
     * @return string
     */
    protected static function renderIteration(
        int $i,
        int $from,
        int $to,
        int $step,
        ?string $iterationArgument,
        RenderingContextInterface $renderingContext,
        \Closure $renderChildrenClosure
    ) {
        if (!empty($iterationArgument)) {
            $variableProvider = $renderingContext->getVariableProvider();
            $cycle = (integer) (($i - $from) / $step) + 1;
            $iteration = [
                'index' => $i,
                'cycle' => $cycle,
                'isOdd' => 0 === $cycle % 2,
                'isEven' => 0 === $cycle % 2,
                'isFirst' => $i === $from,
                'isLast' => static::isLast($i, $from, $to, $step)
            ];
            $variableProvider->add($iterationArgument, $iteration);
            $content = $renderChildrenClosure();
            $variableProvider->remove($iterationArgument);
        } else {
            $content = $renderChildrenClosure();
        }

        return $content;
    }

    protected static function isLast(int $i, int $from, int $to, int $step): bool
    {
        if ($from === $to) {
            $isLast = true;
        } elseif ($from < $to) {
            $isLast = ($i + $step > $to);
        } else {
            $isLast = ($i + $step < $to);
        }

        return $isLast;
    }
}
