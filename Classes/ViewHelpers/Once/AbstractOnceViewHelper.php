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
     * @var RenderingContextInterface
     */
    protected static $currentRenderingContext;

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
            86400
        );
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
        static::$currentRenderingContext = $renderingContext;
        return parent::renderStatic($arguments, $renderChildrenClosure, $renderingContext);
    }

    /**
     * @param array|null $arguments
     * @return boolean
     */
    protected static function evaluateCondition($arguments = null)
    {
        static::removeIfExpired($arguments);
        return static::assertShouldSkip($arguments) === false;
    }

    /**
     * @param array $arguments
     * @return string
     */
    protected static function getIdentifier(array $arguments)
    {
        if (true === isset($arguments['identifier'])) {
            return $arguments['identifier'];
        }
        return static::class;
    }

    /**
     * @param array $arguments
     * @retrun void
     */
    protected static function storeIdentifier(array $arguments)
    {
        $identifier = static::getIdentifier($arguments);
        if (false === isset(self::$identifiers[$identifier])) {
            self::$identifiers[$identifier] = time();
        }
    }

    /**
     * @param array $arguments
     * @return void
     */
    protected static function removeIfExpired(array $arguments)
    {
        $id = static::getIdentifier($arguments);
        if (isset(self::$identifiers[$id]) && self::$identifiers[$id] <= time() - $arguments['ttl']) {
            unset(self::$identifiers[$id]);
        }
    }

    /**
     * @param array $arguments
     * @return boolean
     */
    protected static function assertShouldSkip(array $arguments)
    {
        $identifier = static::getIdentifier($arguments);
        return (true === isset(self::$identifiers[$identifier]));
    }

    /**
     * @param array $arguments
     * @param boolean $hasEvaluated
     * @return string
     */
    protected static function renderStaticThenChild($arguments, &$hasEvaluated)
    {
        if (TYPO3_MODE === 'FE') {
            $GLOBALS['TSFE']->no_cache = 1;
        }
        return parent::renderStaticThenChild($arguments, $hasEvaluated);
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
     * @return string rendered ThenViewHelper or contents of <f:if> if no ThenViewHelper was found
     * @api
     */
    protected function renderThenChild()
    {
        if (TYPO3_MODE === 'FE') {
            $GLOBALS['TSFE']->no_cache = 1;
        }
        return parent::renderThenChild();
    }
}
