<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Render;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;

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
class CacheViewHelper extends AbstractRenderViewHelper implements CompilableInterface
{
    use CompileWithContentArgumentAndRenderStatic;

    const ID_PREFIX = 'vhs-render-cache-viewhelper';

    const ID_SEPARATOR = '-';

    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('content', 'string', 'Content to be cached');
        $this->registerArgument('identity', 'string', 'Identity for cached entry', true);
        parent::initializeArguments();
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
        $identity = $arguments['identity'];
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

        if (true === static::has($identity)) {
            return static::retrieve($identity);
        }
        $content = $renderChildrenClosure();
        static::store($content, $identity);
        return $content;
    }

    /**
     * @param string $id
     * @return boolean
     */
    protected static function has($id)
    {
        return (boolean) static::getCache()->has(static::ID_PREFIX . static::ID_SEPARATOR . $id);
    }

    /**
     * @param mixed $value
     * @param string $id
     * @return void
     */
    protected static function store($value, $id)
    {
        static::getCache()->set(static::ID_PREFIX . static::ID_SEPARATOR . $id, $value);
    }

    /**
     * @param string $id
     * @return mixed
     */
    protected static function retrieve($id)
    {
        $cache = static::getCache();
        if ($cache->has(static::ID_PREFIX . static::ID_SEPARATOR . $id)) {
            return $cache->get(static::ID_PREFIX . static::ID_SEPARATOR . $id);
        }
        return null;
    }

    /**
     * @return mixed
     */
    protected static function getCache()
    {
        return GeneralUtility::makeInstance(CacheManager::class)->getCache('vhs_main');
    }
}
