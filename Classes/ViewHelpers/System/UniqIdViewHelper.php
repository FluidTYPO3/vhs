<?php
namespace FluidTYPO3\Vhs\ViewHelpers\System;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### System: Unique ID
 *
 * Returns a unique ID based on PHP's uniqid-function.
 *
 * Comes in useful when handling/generating html-element-IDs
 * for usage with JavaScript.
 */
class UniqIdViewHelper extends AbstractViewHelper implements CompilableInterface
{
    use CompileWithRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument(
            'prefix',
            'string',
            'An optional prefix for making sure it\'s unique across environments',
            false,
            ''
        );
        $this->registerArgument(
            'moreEntropy',
            'boolean',
            'Add some pseudo random strings. Refer to uniqid()\'s Reference.',
            false,
            false
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
        $uniqueId = uniqid($arguments['prefix'], $arguments['moreEntropy']);
        return $uniqueId;
    }
}
