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
 */
class PdfThumbnailViewHelper extends ImageViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('path', 'string', 'DEPRECATED: Use src instead');
        $this->registerArgument('minWidth', 'integer', 'DEPRECATED: Use minW instead');
        $this->registerArgument('minHeight', 'integer', 'DEPRECATED: Use minH instead');
        $this->registerArgument('maxWidth', 'integer', 'DEPRECATED: Use maxW instead');
        $this->registerArgument('maxHeight', 'integer', 'DEPRECATED: Use maxH instead');
        $this->registerArgument(
            'density',
            'integer',
            'Canvas resolution for rendering the PDF in dpi (higher means better quality)',
            false,
            100
        );
        $this->registerArgument(
            'background',
            'string',
            'Fill background of resulting image with this color (for transparent source files)'
        );
        $this->registerArgument(
            'rotate',
            'integer',
            'Number of degress to rotate resulting image by (caution: very slow if not multiple of 90)',
            false,
            0
        );
        $this->registerArgument(
            'page',
            'integer',
            'Optional page number to render as thumbnail for PDF documents with multiple pages',
            false,
            1
        );
        $this->registerArgument(
            'forceOverwrite',
            'boolean',
            'Forcibly overwrite existing converted PDF files',
            false,
            false
        );
    }

    /**
     * @return string
     */
    public function render()
    {
        $src = GeneralUtility::getFileAbsFileName($this->arguments['src']);
        if (false === file_exists($src)) {
            return null;
        }
        $density = $this->arguments['density'];
        $rotate = $this->arguments['rotate'];
        $page = (integer) $this->arguments['page'];
        $background = $this->arguments['background'];
        $forceOverwrite = (boolean) $this->arguments['forceOverwrite'];
        $filename = basename($src);
        $pageArgument = $page > 0 ? $page - 1 : 0;
        if (isset($GLOBALS['TYPO3_CONF_VARS']['GFX']['colorspace'])) {
            $colorspace = $GLOBALS['TYPO3_CONF_VARS']['GFX']['colorspace'];
        } else {
            $colorspace = 'RGB';
        }
        $path = GeneralUtility::getFileAbsFileName('typo3temp/vhs-pdf-' . $filename . '-page' . $page . '.png');
        if (false === file_exists($path) || true === $forceOverwrite) {
            $arguments = '-colorspace ' . $colorspace;
            if (0 < (integer) $density) {
                $arguments .= ' -density ' . $density;
            }
            if (0 !== (integer) $rotate) {
                $arguments .= ' -rotate ' . $rotate;
            }
            $arguments .= ' "' . $src . '"[' . $pageArgument . ']';
            if (null !== $background) {
                $arguments .= ' -background "' . $background . '" -flatten';
            }
            $arguments .= ' "' . $path . '"';
            $command = CommandUtility::imageMagickCommand('convert', $arguments);
            CommandUtility::exec($command);
        }
        $this->preprocessImage($path);
        return $this->renderTag();
    }
}
