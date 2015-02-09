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
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Once
 */
class SessionViewHelper extends AbstractOnceViewHelper {

	/**
	 * @return string
	 */
	public function render() {
		if ('' === session_id()) {
			session_start();
		}
		return parent::render();
	}

	/**
	 * @return void
	 */
	protected function storeIdentifier() {
		$identifier = $this->getIdentifier();
		$index = get_class($this);
		if (FALSE === is_array($_SESSION[$index])) {
			$_SESSION[$index] = array();
		}
		$_SESSION[$index][$identifier] = time();
	}

	/**
	 * @return boolean
	 */
	protected function assertShouldSkip() {
		$identifier = $this->getIdentifier();
		$index = get_class($this);
		return (boolean) (TRUE === isset($_SESSION[$index][$identifier]));
	}

	/**
	 * @return void
	 */
	protected function removeIfExpired() {
		$identifier = $this->getIdentifier();
		$index = get_class($this);
		$existsInSession = (boolean) (TRUE === isset($_SESSION[$index]) && TRUE === isset($_SESSION[$index][$identifier]));
		if (TRUE === $existsInSession && time() - $this->arguments['ttl'] >= $_SESSION[$index][$identifier]) {
			unset($_SESSION[$index][$identifier]);
		}
	}

}
