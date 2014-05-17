<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Render;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

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
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Render
 */
class CacheViewHelper extends AbstractRenderViewHelper {

	const ID_SEPARATOR = '-';

	/**
	 * @var \TYPO3\CMS\Core\Cache\Frontend\StringFrontend
	 */
	protected $cache;

	/**
	 * @return void
	 */
	public function initialize() {
		$this->cache = $GLOBALS['typo3CacheManager']->getCache('vhs_main');
	}

	/**
	 * Render
	 *
	 * @param mixed $identity Identifier for the cached content (usage preferred)
	 * @param mixed $content Value to be cached
	 * @return mixed
	 * @throws \RuntimeException
	 */
	public function render($identity, $content = NULL) {
		if (FALSE === ctype_alnum(preg_replace('/[\-_]/i', '', $identity))) {
			if (TRUE === $identity instanceof \TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface) {
				$identity = get_class($identity) . self::ID_SEPARATOR . $identity->getUid();
			} elseif (TRUE === method_exists($identity, '__toString')) {
				$identity = (string) $identity;
			} else {
				throw new \RuntimeException(
					'Parameter $identity for Render/CacheViewHelper was not a string or a string-convertible object',
					1352581782
				);
			}
		}
		if (TRUE === $this->has($identity)) {
			return $this->retrieve($identity);
		}
		if (NULL === $content) {
			$content = $this->renderChildren();
		}
		$this->store($content, $identity);
		return $content;
	}

	/**
	 * @param string $id
	 * @return boolean
	 */
	protected function has($id) {
		return (boolean) $this->cache->has(get_class($this) . self::ID_SEPARATOR . $id);
	}

	/**
	 * @param mixed $value
	 * @param string $id
	 * @return void
	 */
	protected function store($value, $id) {
		$this->cache->set(get_class($this) . self::ID_SEPARATOR . $id, $value);
	}

	/**
	 * @param string $id
	 * @return mixed
	 */
	protected function retrieve($id) {
		if ($this->cache->has(get_class($this) . self::ID_SEPARATOR . $id)) {
			return $this->cache->get(get_class($this) . self::ID_SEPARATOR . $id);
		}
		return NULL;
	}

}
