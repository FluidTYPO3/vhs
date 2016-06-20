<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Cache\Frontend\StringFrontend;
use TYPO3\CMS\Core\Utility\CommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

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

    /**
     * @var boolean
     */
    protected $escapingInterceptorEnabled = false;

    /**
     * @var string
     */
    protected $markdownExecutablePath;

    /**
     * @var StringFrontend
     */
    protected $cache;

    /**
     * @return void
     */
    public function initialize()
    {
        if (isset($GLOBALS['typo3CacheManager'])) {
            $cacheManager = $GLOBALS['typo3CacheManager'];
        } else {
            $cacheManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager');
        }
        $this->cache = $cacheManager->getCache('vhs_markdown');
    }

    /**
     * @param string $text
     * @param boolean $trim
     * @param boolean $htmlentities
     * @throws Exception
     * @return string
     */
    public function render($text = null, $trim = true, $htmlentities = false)
    {
        if (null === $text) {
            $text = $this->renderChildren();
        }
        if (null === $text) {
            return null;
        }

        $cacheIdentifier = sha1($text);
        if (true === $this->cache->has($cacheIdentifier)) {
            return $this->cache->get($cacheIdentifier);
        }

        $this->markdownExecutablePath = CommandUtility::getCommand('markdown');
        if (false === is_executable($this->markdownExecutablePath)) {
            throw new Exception(
                'Use of Markdown requires the "markdown" shell utility to be installed and accessible; this binary ' .
                'could not be found in any of your configured paths available to this script',
                1350511561
            );
        }
        if (true === (boolean) $trim) {
            $text = trim($text);
        }
        if (true === (boolean) $htmlentities) {
            $text = htmlentities($text);
        }
        $transformed = $this->transform($text);
        $this->cache->set($cacheIdentifier, $transformed);
        return $transformed;
    }

    /**
     * @param string $text
     * @throws Exception
     * @return string
     */
    public function transform($text)
    {
        $descriptorspec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'a']
        ];

        $process = proc_open($this->markdownExecutablePath, $descriptorspec, $pipes, null, $GLOBALS['_ENV']);

        stream_set_blocking($pipes[0], 1);
        stream_set_blocking($pipes[1], 1);
        stream_set_blocking($pipes[2], 1);

        fwrite($pipes[0], $text);
        fclose($pipes[0]);

        $transformed = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $errors = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        if ('' !== trim($errors)) {
            throw new Exception(
                'There was an error while executing ' . $this->markdownExecutablePath . '. The return code was ' .
                $exitCode . ' and the message reads: ' . $errors,
                1350514144
            );
        }

        return $transformed;
    }
}
