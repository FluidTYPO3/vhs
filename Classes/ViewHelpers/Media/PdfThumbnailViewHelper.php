<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\CommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Converts the provided PDF file into a PNG thumbnail and renders
 * the according image tag using Fluid's standard image ViewHelper
 * thus implementing its arguments. For PDF documents with multiple
 * pages the first page is rendered by default unless specified.
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 */
class PdfThumbnailViewHelper extends ImageViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('path', 'string', 'Path to PDF source file');
        $this->registerArgument('minWidth', 'integer', 'Minimum width of resulting thumbnail image', false, null);
        $this->registerArgument('minHeight', 'integer', 'Minimum height of resulting thumbnail image', false, null);
        $this->registerArgument('maxWidth', 'integer', 'Maximum width of resulting thumbnail image', false, null);
        $this->registerArgument('maxHeight', 'integer', 'Maximum height of resulting thumbnail image', false, null);
        $this->registerArgument('density', 'integer', 'Canvas resolution for rendering the PDF in dpi (higher means better quality)', false, 100);
        $this->registerArgument('background', 'string', 'Fill background of resulting image with this color (for transparent source files)', false, null);
        $this->registerArgument('rotate', 'integer', 'Number of degress to rotate resulting image by (caution: very slow if not multiple of 90)', false, 0);
        $this->registerArgument('page', 'integer', 'Optional page number to render as thumbnail for PDF documents with multiple pages', false, 1);
        $this->registerArgument('forceOverwrite', 'boolean', 'Forcibly overwrite existing converted PDF files', false, false);
    }

    /**
     * @return string
     */
    public function render()
    {
        $path = GeneralUtility::getFileAbsFileName($this->arguments['path']);
        if (false === file_exists($path)) {
            return null;
        }
        $density = $this->arguments['density'];
        $rotate = $this->arguments['rotate'];
        $page = intval($this->arguments['page']);
        $background = $this->arguments['background'];
        $forceOverwrite = (boolean) $this->arguments['forceOverwrite'];
        $width = $this->arguments['width'];
        $height = $this->arguments['height'];
        $minWidth = $this->arguments['minWidth'];
        $minHeight = $this->arguments['minHeight'];
        $maxWidth = $this->arguments['maxWidth'];
        $maxHeight = $this->arguments['maxHeight'];
        $filename = basename($path);
        $pageArgument = $page > 0 ? $page - 1 : 0;
        $colorspace = true === isset($GLOBALS['TYPO3_CONF_VARS']['GFX']['colorspace']) ? $GLOBALS['TYPO3_CONF_VARS']['GFX']['colorspace'] : 'RGB';
        $destination = GeneralUtility::getFileAbsFileName('typo3temp/vhs-pdf-' . $filename . '-page' . $page . '.png');
        if (false === file_exists($destination) || true === $forceOverwrite) {
            $arguments = '-colorspace ' . $colorspace;
            if (0 < intval($density)) {
                $arguments .= ' -density ' . $density;
            }
            if (0 !== intval($rotate)) {
                $arguments .= ' -rotate ' . $rotate;
            }
            $arguments .= ' "' . $path . '"[' . $pageArgument . ']';
            if (null !== $background) {
                $arguments .= ' -background "' . $background . '" -flatten';
            }
            $arguments .= ' "' . $destination . '"';
            $command = CommandUtility::imageMagickCommand('convert', $arguments);
            CommandUtility::exec($command);
        }
        $image = substr($destination, strlen(PATH_site));
        return parent::render($image, $width, $height, $minWidth, $minHeight, $maxWidth, $maxHeight);
    }
}
