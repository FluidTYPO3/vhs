<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\CompileWithContentArgumentAndRenderStatic;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

/**
 * Returns the size of the provided file in bytes.
 */
class SizeViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('path', 'string', 'Path to the file to determine size for.');
    }

    /**
     * @return integer
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $path = $renderChildrenClosure();

        if (null === $path) {
            return 0;
        }

        $file = GeneralUtility::getFileAbsFileName($path);

        if (!file_exists($file) || is_dir($file)) {
            throw new Exception(
                'Cannot determine size of "' . $file . '". File does not exist or is a directory.',
                1356953963
            );
        }

        $size = filesize($file);

        if (false === $size) {
            throw new Exception('Cannot determine size of "' . $file . '".', 1356954032);
        }

        return $size;
    }
}
