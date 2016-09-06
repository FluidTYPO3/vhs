<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ArrayConsumingViewHelperTrait;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

/**
 * Base class: Math ViewHelpers operating on one number or an
 * array of numbers.
 */
abstract class AbstractSingleMathViewHelper extends AbstractViewHelper
{

    use ArrayConsumingViewHelperTrait;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('a', 'mixed', 'First number for calculation');
        $this->registerArgument(
            'fail',
            'boolean',
            'If TRUE, throws an Exception if argument "a" is not specified and no child content or inline argument ' .
            'is found. Usually okay to use a NULL value (as integer zero).',
            false,
            false
        );
    }

    /**
     * @param mixed $subject
     * @return boolean
     */
    protected function assertIsArrayOrIterator($subject)
    {
        return (boolean) (true === is_array($subject) || true === $subject instanceof \Iterator);
    }

    /**
     * @return mixed
     * @throw Exception
     */
    public function render()
    {
        $a = $this->getInlineArgument();
        return $this->calculate($a);
    }

    /**
     * @throws Exception
     * @return mixed
     */
    protected function getInlineArgument()
    {
        $a = $this->renderChildren();
        if (null === $a && true === isset($this->arguments['a'])) {
            $a = $this->arguments['a'];
        }
        if (null === $a && true === (boolean) $this->arguments['fail']) {
            throw new Exception('Required argument "a" was not supplied', 1237823699);
        }
        return $a;
    }

    /**
     * @return mixed
     *
     * @param mixed $a
     * @param mixed|null $b
     *
     * @throws \TYPO3\CMS\Fluid\Core\ViewHelper\Exception
     */
    protected function calculate($a, $b = null)
    {
        $aIsIterable = $this->assertIsArrayOrIterator($a);
        if (true === $aIsIterable) {
            $a = $this->arrayFromArrayOrTraversableOrCSV($a);
            foreach ($a as $index => $value) {
                $a[$index] = $this->calculateAction($value);
            }
            return $a;
        }
        return $this->calculateAction($a);
    }
}
