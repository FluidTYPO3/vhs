<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * Returns an array of files found in the provided path.
 */
class FilesViewHelper extends AbstractViewHelper
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
        $this->registerArgument('path', 'string', 'Path to the folder containing the files to be listed.');
        $this->registerArgument(
            'extensionList',
            'string',
            'A comma seperated list of file extensions to pick up.',
            false,
            ''
        );
        $this->registerArgument(
            'prependPath',
            'boolean',
            'If set to TRUE the path will be prepended to file names.',
            false,
            false
        );
        $this->registerArgument(
            'order',
            'string',
            'If set to "mtime" sorts files by modification time or alphabetically otherwise.',
            false,
            ''
        );
        $this->registerArgument(
            'excludePattern',
            'string',
            'A comma seperated list of filenames to exclude, no wildcards.',
            false,
            ''
        );
    }

    /**
     * @return array|string
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $path = (string) $renderChildrenClosure();

        /** @var string $extensionList */
        $extensionList = $arguments['extensionList'];
        $prependPath = (bool) $arguments['prependPath'];
        /** @var string $order */
        $order = $arguments['order'];
        /** @var string $excludePattern */
        $excludePattern = $arguments['excludePattern'];

        $files = GeneralUtility::getFilesInDir($path, $extensionList, $prependPath, $order, $excludePattern);

        return $files;
    }
}
