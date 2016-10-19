<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\DefaultRenderMethodViewHelperTrait;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Hashes a string.
 */
class HashViewHelper extends AbstractViewHelper
{

    use DefaultRenderMethodViewHelperTrait;

    /**
     * Initialize
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument(
            'algorithm',
            'string',
            'Hashing algorithm to use (see http://php.net/manual/en/function.hash-algos.php for details)',
            false,
            'sha256'
        );
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $content = $renderChildrenClosure();
        $content = hash($arguments['algorithm'], $content, false);
        return $content;
    }
}
