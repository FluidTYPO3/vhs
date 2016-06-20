<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Once;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

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
     * Standard render method. Implementers should override
     * the assertShouldSkip() method and/or the getIdentifier()
     * and storeIdentifier() methods as applies to each
     * implementers method of storing identifiers.
     *
     * @return string
     */
    public function render()
    {
        $this->removeIfExpired();
        $evaluation = $this->assertShouldSkip();
        if (false === $evaluation) {
            $content = $this->renderThenChild();
        } else {
            $content = $this->renderElseChild();
        }
        $this->storeIdentifier();
        return $content;
    }

    /**
     * @return string
     */
    protected function getIdentifier()
    {
        if (true === isset($this->arguments['identifier'])) {
            return $this->arguments['identifier'];
        }
        return get_class($this);
    }

    /**
     * @retrun void
     */
    protected function storeIdentifier()
    {
        $identifier = $this->getIdentifier();
        if (false === isset(self::$identifiers[$identifier])) {
            self::$identifiers[$identifier] = time();
        }
    }

    /**
     * @return void
     */
    protected function removeIfExpired()
    {
        $id = $this->getIdentifier();
        if (isset(self::$identifiers[$id]) && self::$identifiers[$id] <= time() - $this->arguments['ttl']) {
            unset(self::$identifiers[$id]);
        }
    }

    /**
     * @return boolean
     */
    protected function assertShouldSkip()
    {
        $identifier = $this->getIdentifier();
        return (true === isset(self::$identifiers[$identifier]));
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
        $GLOBALS['TSFE']->no_cache = 1;
        return parent::renderThenChild();
    }
}
