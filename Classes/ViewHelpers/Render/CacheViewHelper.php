<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Render;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;

/**
 * ### Cache Rendering ViewHelper
 *
 * Caches the child content (any type supported as long as it
 * can be serialized). Because of the added overhead you should
 * only use this if what you are caching is complex enough that
 * it performs many DB request (for example when displaying an
 * object with many lazy properties which don't load until the
 * template asks for the property value). In short, applies to
 * just about the same use cases as any other cache - but remember
 * that Fluid is already a very efficient rendering engine so don't
 * just assume that using the ViewHelper will increase performance
 * or decrease memory usage.
 *
 * Works forcibly, i.e. can only re-render its content if the
 * cache is cleared. A CTRL+Refresh in the browser does nothing,
 * even if a BE user is logged in. Only use this ViewHelper around
 * content which you are absolutely sure it makes sense to cache
 * along with an identity - for example, if rendering an uncached
 * plugin which contains a Partial template that is in all aspects
 * just a solid-state HTML representation of something like a list
 * of current news.
 *
 * The cache behind this ViewHelper is the Extbase object cache,
 * which is cleared when you clear the page content cache.
 *
 * Do not use on form elements, it will invalidate the checksum.
 *
 * Do not use around ViewHelpers which add header data or which
 * interact with the PageRenderer or other "live" objects; this
 * includes many of the VHS ViewHelpers!
 */
class CacheViewHelper extends AbstractRenderViewHelper
{

    const ID_PREFIX = 'vhs-render-cache-viewhelper';

    const ID_SEPARATOR = '-';

    /**
     * @var \TYPO3\CMS\Core\Cache\Frontend\StringFrontend
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
            $cacheManager =   GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager');
        }
        $this->cache = $cacheManager->getCache('vhs_main');
    }

    /**
     * Render
     *
     * @param mixed $identity Identifier for the cached content (usage preferred)
     * @param mixed $content Value to be cached
     * @return mixed
     * @throws \RuntimeException
     */
    public function render($identity, $content = null)
    {
        if (false === ctype_alnum(preg_replace('/[\-_]/i', '', $identity))) {
            if (true === $identity instanceof DomainObjectInterface) {
                $identity = get_class($identity) . self::ID_SEPARATOR . $identity->getUid();
            } elseif (true === method_exists($identity, '__toString')) {
                $identity = (string) $identity;
            } else {
                throw new \RuntimeException(
                    'Parameter $identity for Render/CacheViewHelper was not a string or a string-convertible object',
                    1352581782
                );
            }
        }

        // Hash the cache-key to circumvent disallowed chars
        $identity = sha1($identity);

        if (true === $this->has($identity)) {
            return $this->retrieve($identity);
        }
        if (null === $content) {
            $content = $this->renderChildren();
        }
        $this->store($content, $identity);
        return $content;
    }

    /**
     * @param string $id
     * @return boolean
     */
    protected function has($id)
    {
        return (boolean) $this->cache->has(self::ID_PREFIX . self::ID_SEPARATOR . $id);
    }

    /**
     * @param mixed $value
     * @param string $id
     * @return void
     */
    protected function store($value, $id)
    {
        $this->cache->set(self::ID_PREFIX . self::ID_SEPARATOR . $id, $value);
    }

    /**
     * @param string $id
     * @return mixed
     */
    protected function retrieve($id)
    {
        if ($this->cache->has(self::ID_PREFIX . self::ID_SEPARATOR . $id)) {
            return $this->cache->get(self::ID_PREFIX . self::ID_SEPARATOR . $id);
        }
        return null;
    }
}
