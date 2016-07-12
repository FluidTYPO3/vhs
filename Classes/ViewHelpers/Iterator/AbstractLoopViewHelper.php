<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Abstract class with basic functionality for loop view helpers.
 */
abstract class AbstractLoopViewHelper extends AbstractViewHelper
{

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
     * @return string
     */
    protected function renderIteration($i, $from, $to, $step, $iterationArgument)
    {
        if (false === empty($iterationArgument)) {
            $cycle = intval(($i - $from) / $step) + 1;
            $iteration = [
                'index' => $i,
                'cycle' => $cycle,
                'isOdd' => (0 === $cycle % 2 ? false : true),
                'isEven' => (0 === $cycle % 2 ? true : false),
                'isFirst' => ($i === $from ? true : false),
                'isLast' => $this->isLast($i, $from, $to, $step)
            ];
            $this->templateVariableContainer->add($iterationArgument, $iteration);
            $content = $this->renderChildren();
            $this->templateVariableContainer->remove($iterationArgument);
        } else {
            $content = $this->renderChildren();
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
    protected function isLast($i, $from, $to, $step)
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
