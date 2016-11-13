<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Once;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * Base class for "Render Once"-style ViewHelpers: session, cookie,
 * request, template variable set, ViewHelper variable set etc.
 */
abstract class AbstractOnceViewHelper extends AbstractConditionViewHelper
{

    const DEFAULT_TTL = 86400;

    /**
     * Standard storage - static variable meaning uniqueness of $identifier
     * across each Request, i.e. unique to each individual plugin/content.
     *
     * @var array
     */
    protected static $identifiers = [];

    /**
     * Initialize arguments
     *
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument(
            'identifier',
            'string',
            'Identity of this condition - if used in other places, the condition applies to the same identity in the ' .
            'storage (i.e. cookie name or session key)'
        );
        $this->registerArgument(
            'lockToDomain',
            'boolean',
            'If TRUE, locks this condition to a specific domain, i.e. the storage of $identity is associated with ' .
            'a domain. If same identity is also used without domain lock, it matches any domain locked condition',
            false,
            false
        );
        $this->registerArgument(
            'ttl',
            'integer',
            'Time-to-live for skip registration, number of seconds. After this expires the registration is unset',
            false,
            static::DEFAULT_TTL
        );
    }

    /**
     * @param array $arguments
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    protected static function getIdentifier(array $arguments, RenderingContextInterface $renderingContext)
    {
        return $arguments['identifier'];
    }

    /**
     * @param array|null $arguments
     * @return boolean
     */
    protected static function evaluateCondition($arguments = null)
    {
        $identifier = static::getIdentifier($arguments);
        $verdict = !static::assertShouldSkip($identifier);
        if ($verdict) {
            $GLOBALS['TSFE']->no_cache = 1;
            static::removeIfExpired($identifier, $arguments['ttl']);
        } else {
            static::storeIdentifier($identifier);
        }
        return $verdict;
    }

    /**
     * @param string $identifier
     * @return boolean
     */
    protected static function assertShouldSkip($identifier)
    {
        return isset(static::$identifiers[$identifier]);
    }

    /**
     * @param string $identifier
     * @param array $arguments
     * @return void
     */
    protected static function storeIdentifier($identifier, array $arguments)
    {
        static::$identifiers[$identifier] = time();
    }

    /**
     * @return void
     */
    protected static function removeIfExpired($identifier, $ttl = self::DEFAULT_TTL)
    {
        if (isset(static::$identifiers[$identifier]) && static::$identifiers[$identifier] <= time() - $ttl) {
            unset(static::$identifiers[$identifier]);
        }
    }
}
