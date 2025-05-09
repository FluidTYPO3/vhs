<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Once;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\ContextUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * Base class for "Render Once"-style ViewHelpers: session, cookie,
 * request, template variable set, ViewHelper variable set etc.
 */
abstract class AbstractOnceViewHelper extends AbstractConditionViewHelper
{
    /**
     * Standard storage - static variable meaning uniqueness of $identifier
     * across each Request, i.e. unique to each individual plugin/content.
     *
     * @var array
     */
    protected static $identifiers = [];

    /**
     * Always-current but statically assigned instance of rendering context
     * which applied at the exact time that the ViewHelper was asked to
     * evaluate whether or not to render content.
     *
     * @var RenderingContextInterface&RenderingContext
     */
    protected static $currentRenderingContext;

    public function initializeArguments(): void
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
            86400
        );
    }

    /**
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        /** @var RenderingContext $renderingContext */
        static::$currentRenderingContext = $renderingContext;
        return parent::renderStatic($arguments, $renderChildrenClosure, $renderingContext);
    }

    public static function verdict(array $arguments, RenderingContextInterface $renderingContext): bool
    {
        static::removeIfExpired($arguments);
        $shouldSkip = static::assertShouldSkip($arguments) === false;
        static::storeIdentifier($arguments);
        return $shouldSkip;
    }

    protected static function getIdentifier(array $arguments): string
    {
        return $arguments['identifier'] ?? static::class;
    }

    protected static function storeIdentifier(array $arguments): void
    {
        $identifier = static::getIdentifier($arguments);
        if (!isset(static::$identifiers[$identifier])) {
            static::$identifiers[$identifier] = time();
        }
    }

    protected static function removeIfExpired(array $arguments): void
    {
        $id = static::getIdentifier($arguments);
        if (isset(static::$identifiers[$id]) && static::$identifiers[$id] <= time() - $arguments['ttl']) {
            unset(static::$identifiers[$id]);
        }
    }

    protected static function assertShouldSkip(array $arguments): bool
    {
        $identifier = static::getIdentifier($arguments);
        return isset(static::$identifiers[$identifier]);
    }

    /**
     * Override: forcibly disables page caching - a TRUE condition
     * in this ViewHelper means page content would be depending on
     * the current visitor's session/cookie/auth etc.
     *
     * Returns value of "then" attribute.
     * If then attribute is not set, iterates through child nodes and renders ThenViewHelper.
     * If then attribute is not set and no ThenViewHelper and no ElseViewHelper is found, all child nodes are rendered
     *
     * @return mixed rendered ThenViewHelper or contents of <f:if> if no ThenViewHelper was found
     */
    protected function renderThenChild()
    {
        if (ContextUtility::isFrontend()) {
            $GLOBALS['TSFE']->no_cache = 1;
        }
        return parent::renderThenChild();
    }
}
