<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Repeats rendering of children with a typical for loop: starting at
 * index $from it will loop until the index has reached $to.
 */
class ForViewHelper extends AbstractLoopViewHelper
{

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
     * @throws \RuntimeException
     * @return string
     */
    public function render()
    {
        $to = intval($this->arguments['to']);
        $from = intval($this->arguments['from']);
        $step = intval($this->arguments['step']);
        $iteration = $this->arguments['iteration'];
        $content = '';

        if (0 === $step) {
            throw new \RuntimeException('"step" may not be 0.', 1383267698);
        }
        if ($from < $to && 0 > $step) {
            throw new \RuntimeException('"step" must be greater than 0 if "from" is smaller than "to".', 1383268407);
        }
        if ($from > $to && 0 < $step) {
            throw new \RuntimeException('"step" must be smaller than 0 if "from" is greater than "to".', 1383268415);
        }

        if (true === $this->templateVariableContainer->exists($iteration)) {
            $backupVariable = $this->templateVariableContainer->get($iteration);
            $this->templateVariableContainer->remove($iteration);
        }

        if ($from === $to) {
            $content = $this->renderIteration($from, $from, $to, $step, $iteration);
        } elseif ($from < $to) {
            for ($i = $from; $i <= $to; $i += $step) {
                $content .= $this->renderIteration($i, $from, $to, $step, $iteration);
            }
        } else {
            for ($i = $from; $i >= $to; $i += $step) {
                $content .= $this->renderIteration($i, $from, $to, $step, $iteration);
            }
        }

        if (true === isset($backupVariable)) {
            $this->templateVariableContainer->add($iteration, $backupVariable);
        }

        return $content;
    }
}
