<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\ViewHelperUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

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

    /**
     * Initialize
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('iteration', 'string', 'Variable name to insert result into, suppresses output');
    }

    /**
     * @param integer $i
     * @param integer $from
     * @param integer $to
     * @param integer $step
     * @param string $iterationArgument
     * @param RenderingContextInterface $renderingContext
     * @param \Closure $renderChildrenClosure
     * @return string
     */
    protected static function renderIteration(
        $i,
        $from,
        $to,
        $step,
        $iterationArgument,
        RenderingContextInterface $renderingContext,
        \Closure $renderChildrenClosure
    ) {
        if (false === empty($iterationArgument)) {
            $variableProvider = ViewHelperUtility::getVariableProviderFromRenderingContext($renderingContext);
            $cycle = (integer) (($i - $from) / $step) + 1;
            $iteration = [
                'index' => $i,
                'cycle' => $cycle,
                'isOdd' => 0 === $cycle % 2 ? false : true,
                'isEven' => 0 === $cycle % 2 ? true : false,
                'isFirst' => $i === $from ? true : false,
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

    /**
     * @param integer $i
     * @param integer $from
     * @param integer $to
     * @param integer $step
     * @return boolean
     */
    protected static function isLast($i, $from, $to, $step)
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
