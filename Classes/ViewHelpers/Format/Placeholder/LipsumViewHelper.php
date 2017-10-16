<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format\Placeholder;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Lipsum ViewHelper
 *
 * Renders Lorem Ipsum text according to provided arguments.
 */
class LipsumViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('lipsum', 'string', 'Optional, custom lipsum source');
        $this->registerArgument('paragraphs', 'integer', 'Number of paragraphs to output');
        $this->registerArgument('wordsPerParagraph', 'integer', 'Number of words per paragraph');
        $this->registerArgument(
            'skew',
            'integer',
            'Amount in number of words to vary the number of words per paragraph'
        );
        $this->registerArgument(
            'html',
            'boolean',
            'If TRUE, renders output as HTML paragraph tags in the same way an RTE would'
        );
        $this->registerArgument(
            'parseFuncTSPath',
            'string',
            'If you want another parseFunc for HTML processing, enter the TS path here'
        );
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed|string
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $lipsum = $arguments['lipsum'];
        if (mb_strlen($lipsum) === 0) {
            $lipsum = static::getDefaultLoremIpsum();
        }
        if ((mb_strlen($lipsum) < 255 && !preg_match('/[^a-z0-9_\.\:\/]/i', $lipsum)) || 0 === mb_strpos($lipsum, 'EXT:')) {
            // argument is most likely a file reference.
            $sourceFile = GeneralUtility::getFileAbsFileName($lipsum);
            if (file_exists($sourceFile)) {
                $lipsum = file_get_contents($sourceFile);
            } else {
                return 'Vhs LipsumViewHelper was asked to load Lorem Ipsum from a file which does not exist. ' .
                    'The file was: ' . $sourceFile;
                $lipsum = static::getDefaultLoremIpsum();
            }
        }
        $lipsum = preg_replace('/[\\r\\n]{1,}/i', "\n", $lipsum);
        $paragraphs = explode("\n", $lipsum);
        $paragraphs = array_slice($paragraphs, 0, intval($arguments['paragraphs']));
        foreach ($paragraphs as $index => $paragraph) {
            $length = $arguments['wordsPerParagraph']
                + rand(0 - intval($arguments['skew']), intval($arguments['skew']));
            $words = explode(' ', $paragraph);
            $paragraphs[$index] = implode(' ', array_slice($words, 0, $length));
        }

        $lipsum = implode("\n", $paragraphs);
        if ($arguments['html']) {
            $tsParserPath = $arguments['parseFuncTSPath'] ? '< ' . $arguments['parseFuncTSPath'] : null;
            $lipsum = static::getContentObject()->parseFunc($lipsum, [], $tsParserPath);
        }
        return $lipsum;
    }

    /**
     * Get the default Lorem Ipsum. The compressed block (which is
     * of course cleaned thoroughly to avoid any injection) contains
     * 20 full paragraphs of Lorem Ipsum in standard latin. No bells
     * and whistles there.
     *
     * @return string
     */
    protected static function getDefaultLoremIpsum()
    {
        static $safeLipsum;

        if (isset($safeLipsum)) {
            return $safeLipsum;
        }
        // Note: this MAY look suspicious but it really is just a whole lot of Lipsum
        // in a compressed state. Just to make sure that you trust the block, we run
        // strip_tags and htmlentities on the string before it is returned. This is not
        // done on custom Lipsum - but it is done here at no risk, since we know the
        // Lipsum to contain zero HTML and zero special characters, 100% ASCII. Source
        // of the Lipsum text is http://www.lipsum.com set at 20 paragraphs, compressed
        // through a small shell script.
        $lipsum = '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';
        $uncompressed = gzuncompress(base64_decode($lipsum));
        $safeLipsum = htmlentities(strip_tags($uncompressed));
        return $safeLipsum;
    }

    /**
     * @return ContentObjectRenderer
     */
    protected static function getContentObject()
    {
        return GeneralUtility::makeInstance(ObjectManager::class)->get(ConfigurationManagerInterface::class)->contentObject;
    }
}
