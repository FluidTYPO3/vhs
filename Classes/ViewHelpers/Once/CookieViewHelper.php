<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Once;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Once: Cookie
 *
 * Displays nested content or "then" child once, then sets a
 * cookie with $ttl, optionally locked to domain name, which
 * makes the condition return FALSE as long as the cookie exists.
 *
 * "Once"-style ViewHelpers are purposed to only display their
 * nested content once per XYZ, where the XYZ depends on the
 * specific type of ViewHelper (session, cookie etc).
 *
 * In addition the ViewHelper is a ConditionViewHelper, which
 * means you can utilize the f:then and f:else child nodes as
 * well as the "then" and "else" arguments.
 */
class CookieViewHelper extends AbstractOnceViewHelper
{
    /**
     * @return void
     */
    protected function storeIdentifier()
    {
        $identifier = $this->getIdentifier();
        $domain = $this->arguments['lockToDomain'] ? $_SERVER['HTTP_HOST'] : null;
        setcookie($identifier, '1', time() + $this->arguments['ttl'], null, $domain);
    }

    /**
     * @return boolean
     */
    protected function assertShouldSkip()
    {
        $identifier = $this->getIdentifier();
        return (true === isset($_COOKIE[$identifier]));
    }

    /**
     * @return void
     */
    protected function removeIfExpired()
    {
        $identifier = $this->getIdentifier();
        $existsInCookie = (boolean) (true === isset($_COOKIE[$identifier]));
        if (true === $existsInCookie) {
            $this->removeCookie();
        }
    }

    /**
     * @return void
     */
    protected function removeCookie()
    {
        $identifier = $this->getIdentifier();
        unset($_SESSION[$identifier], $_COOKIE[$identifier]);
        setcookie($identifier, null, time() - 1);
    }
}
