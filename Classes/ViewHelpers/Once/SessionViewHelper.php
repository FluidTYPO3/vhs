<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Once;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Once: Session
 *
 * Displays nested content or "then" child once per session.
 *
 * "Once"-style ViewHelpers are purposed to only display their
 * nested content once per XYZ, where the XYZ depends on the
 * specific type of ViewHelper (session, cookie etc).
 *
 * In addition the ViewHelper is a ConditionViewHelper, which
 * means you can utilize the f:then and f:else child nodes as
 * well as the "then" and "else" arguments.
 */
class SessionViewHelper extends AbstractOnceViewHelper
{
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
        if ('' === session_id()) {
            session_start();
        }
        return parent::renderStatic($arguments, $renderChildrenClosure, $renderingContext);
    }

    /**
     * @param array $arguments
     * @return void
     */
    protected static function storeIdentifier(array $arguments)
    {
        $identifier = static::getIdentifier($arguments);
        $index = static::class;
        if (false === is_array($_SESSION[$index])) {
            $_SESSION[$index] = [];
        }
        $_SESSION[$index][$identifier] = time();
    }

    /**
     * @param array $arguments
     * @return boolean
     */
    protected static function assertShouldSkip(array $arguments)
    {
        $identifier = static::getIdentifier($arguments);
        $index = static::class;
        return (boolean) (true === isset($_SESSION[$index][$identifier]));
    }

    /**
     * @param array $arguments
     * @return void
     */
    protected static function removeIfExpired(array $arguments)
    {
        $id = static::getIdentifier($arguments);
        $index = static::class;
        $existsInSession = (boolean) (true === isset($_SESSION[$index]) && true === isset($_SESSION[$index][$id]));
        if (true === $existsInSession && time() - $arguments['ttl'] >= $_SESSION[$index][$id]) {
            unset($_SESSION[$index][$id]);
        }
    }
}
