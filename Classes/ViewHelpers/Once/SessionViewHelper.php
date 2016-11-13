<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Once;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

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
     * @return string
     */
    public function render()
    {
        if ('' === session_id()) {
            session_start();
        }
        return parent::render();
    }

    /**
     * @param string $identifier
     * @return boolean
     */
    protected static function assertShouldSkip($identifier)
    {
        return isset($_SESSION[static::class][$identifier]);
    }

    /**
     * @param mixed $identifier
     * @param array $arguments
     * @return void
     */
    protected static function storeIdentifier($identifier, array $arguments)
    {
        if (!is_array($_SESSION[static::class])) {
            $_SESSION[static::class] = [];
        }
        $_SESSION[static::class][$identifier] = time();
    }

    /**
     * @return void
     */
    protected static function removeIfExpired($identifier, $ttl = self::DEFAULT_TTL)
    {
        $existsInSession = (boolean) (isset($_SESSION[static::class]) && isset($_SESSION[static::class][$identifier]));
        if (true === $existsInSession && time() - $ttl >= $_SESSION[static::class][$identifier]) {
            unset($_SESSION[static::class][$identifier]);
        }
    }
}
