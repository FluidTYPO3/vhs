<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\CompileWithContentArgumentAndRenderStatic;
use FluidTYPO3\Vhs\Utility\ErrorUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Utility\CommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Markdown Transformation ViewHelper
 *
 * Requires an installed "markdown" utility, the specific
 * implementation is less important since Markdown has no
 * configuration options. However, the utility or shell
 * scipt must:
 *
 * - accept input from STDIN
 * - output to STDOUT
 * - place errors in STDERR
 * - be executable according to `open_basedir` and others
 * - exist within (one or more of) TYPO3's configured executable paths
 *
 * In other words, *NIX standard behavior must be used.
 *
 * See: http://daringfireball.net/projects/markdown/
 */
class MarkdownViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('text', 'string', 'Markdown to convert to HTML');
        $this->registerArgument('trim', 'boolean', 'Trim content before converting', false, true);
        $this->registerArgument('htmlentities', 'boolean', 'If true, escapes converted HTML', false, false);
    }

    /**
     * @return mixed|null|string
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $trim = (boolean) $arguments['trim'];
        $htmlentities = (boolean) $arguments['htmlentities'];
        $text = $renderChildrenClosure();
        if (null === $text) {
            return null;
        }

        $cacheIdentifier = sha1($text);
        $fromCache = static::getCache()->get($cacheIdentifier);
        if (!empty($fromCache)) {
            return $fromCache;
        }

        /** @var string $markdownExecutablePath */
        $markdownExecutablePath = CommandUtility::getCommand('markdown');
        if (!is_executable($markdownExecutablePath)) {
            ErrorUtility::throwViewHelperException(
                'Use of Markdown requires the "markdown" shell utility to be installed and accessible; this binary ' .
                'could not be found in any of your configured paths available to this script',
                1350511561
            );
        }
        if ($trim) {
            $text = trim($text);
        }
        if ($htmlentities) {
            $text = htmlentities($text);
        }
        $transformed = static::transform($text, $markdownExecutablePath);
        static::getCache()->set($cacheIdentifier, $transformed);
        return $transformed;
    }

    public static function transform(string $text, string $markdownExecutablePath): string
    {
        $descriptorspec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'a']
        ];

        $process = proc_open($markdownExecutablePath, $descriptorspec, $pipes, null, $GLOBALS['_ENV']);
        if ($process === false) {
            return $text;
        }

        stream_set_blocking($pipes[0], true);
        stream_set_blocking($pipes[1], true);
        stream_set_blocking($pipes[2], true);

        fwrite($pipes[0], $text);
        fclose($pipes[0]);

        $transformed = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $errors = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        if ('' !== trim((string) $errors)) {
            ErrorUtility::throwViewHelperException(
                'There was an error while executing ' . $markdownExecutablePath . '. The return code was ' .
                $exitCode . ' and the message reads: ' . $errors,
                1350514144
            );
        }

        return (string) $transformed;
    }

    protected static function getCache(): FrontendInterface
    {
        static $cache;
        if (!isset($cache)) {
            $cacheManager = $GLOBALS['typo3CacheManager'] ?? GeneralUtility::makeInstance(CacheManager::class);
            $cache = $cacheManager->getCache('vhs_markdown');
        }
        return $cache;
    }
}
