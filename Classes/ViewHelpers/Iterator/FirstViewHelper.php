<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

/**
 * Returns the first element of $haystack.
 */
class FirstViewHelper extends AbstractViewHelper
{

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
     * Render method
     *
     * @throws Exception
     * @return mixed|NULL
     */
    public function render()
    {
        $haystack = $this->arguments['haystack'];
        if (null === $haystack) {
            $haystack = $this->renderChildren();
        }
        if (false === is_array($haystack) && false === $haystack instanceof \Iterator && null !== $haystack) {
            throw new Exception(
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
