<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\ErrorUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

/**
 * Returns the first element of $haystack.
 */
class FirstViewHelper extends AbstractViewHelper
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
     * Initialize arguments
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('haystack', 'mixed', 'Haystack in which to look for needle');
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return null
     * @throws Exception
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $haystack = $arguments['haystack'] ?? $renderChildrenClosure();
        if (false === is_array($haystack) && false === $haystack instanceof \Iterator && null !== $haystack) {
            ErrorUtility::throwViewHelperException(
                'Invalid argument supplied to Iterator/FirstViewHelper - expected array, Iterator or NULL but got ' .
                gettype($haystack),
                1351958398
            );
        }
        if (null === $haystack) {
            return null;
        }
        foreach ($haystack as $needle) {
            return $needle;
        }
        return null;
    }
}
