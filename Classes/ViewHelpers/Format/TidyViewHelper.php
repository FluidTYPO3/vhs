<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * Tidy-processes a string (HTML source), applying proper
 * indentation.
 */
class TidyViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('content', 'string', 'Content to tidy');
        $this->registerArgument('encoding', 'string', 'Encoding of string', false, 'utf8');
    }

    /**
     * @return string|\tidy
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $content = $renderChildrenClosure();
        /** @var string $encoding */
        $encoding = $arguments['encoding'];
        if (class_exists('tidy')) {
            $tidy = tidy_parse_string($content, [], $encoding);
            if ($tidy === false) {
                return $content;
            }
            $tidy->cleanRepair();
            return $tidy->root()->value;
        }
        throw new \RuntimeException(
            'TidyViewHelper requires the PHP extension "tidy" which is not installed or not loaded.',
            1352059753
        );
    }
}
