<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Repeats rendering of children $count times while updating $iteration.
 */
class LoopViewHelper extends AbstractLoopViewHelper
{

    /**
     * Initialize
     *
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('count', 'integer', 'Number of times to render child content', true);
        $this->registerArgument('minimum', 'integer', 'Minimum number of loops before stopping', false, 0);
        $this->registerArgument('maximum', 'integer', 'Maxiumum number of loops before stopping', false, PHP_INT_MAX);
    }

    /**
     * @return string
     */
    public function render()
    {
        $count = intval($this->arguments['count']);
        $minimum = intval($this->arguments['minimum']);
        $maximum = intval($this->arguments['maximum']);
        $iteration = $this->arguments['iteration'];
        $content = '';

        if ($count < $minimum) {
            $count = $minimum;
        } elseif ($count > $maximum) {
            $count = $maximum;
        }

        if (true === $this->templateVariableContainer->exists($iteration)) {
            $backupVariable = $this->templateVariableContainer->get($iteration);
            $this->templateVariableContainer->remove($iteration);
        }

        for ($i = 0; $i < $count; $i++) {
            $content .= $this->renderIteration($i, 0, $count, 1, $iteration);
        }

        if (true === isset($backupVariable)) {
            $this->templateVariableContainer->add($iteration, $backupVariable);
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
        return ($i + $step >= $to);
    }
}
