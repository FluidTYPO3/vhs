<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Once;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

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

    protected static function storeIdentifier(array $arguments): void
    {
        $identifier = static::getIdentifier($arguments);
        $index = self::class;
        if (!is_array($_SESSION[$index] ?? false)) {
            $_SESSION[$index] = [];
        }
        $_SESSION[$index][$identifier] = time();
    }

    protected static function assertShouldSkip(array $arguments): bool
    {
        $identifier = static::getIdentifier($arguments);
        $index = self::class;
        return isset($_SESSION[$index][$identifier]);
    }

    protected static function removeIfExpired(array $arguments): void
    {
        $id = static::getIdentifier($arguments);
        $index = self::class;
        $existsInSession = isset($_SESSION[$index], $_SESSION[$index][$id]);
        if ($existsInSession && time() - $arguments['ttl'] >= $_SESSION[$index][$id]) {
            unset($_SESSION[$index][$id]);
        }
    }
}
