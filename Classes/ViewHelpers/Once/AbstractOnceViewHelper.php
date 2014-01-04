<?php
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
 * Base class for "Render Once"-style ViewHelpers: session, cookie,
 * request, template variable set, ViewHelper variable set etc.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Once
 */
abstract class Tx_Vhs_ViewHelpers_Once_AbstractOnceViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper {

	/**
	 * Standard storage - static variable meaning uniqueness of $identifier
	 * across each Request, i.e. unique to each individual plugin/content.
	 *
	 * @var array
	 */
	protected static $identifiers = array();

	/**
	 * Initialize arguments
	 *
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('identifier', 'string', 'Identity of this condition - if used in other places, the condition applies to the same identity in the storage (i.e. cookie name or session key)');
		$this->registerArgument('lockToDomain', 'boolean', 'If TRUE, locks this condition to a specific domain, i.e. the storage of $identity is associated with a domain. If same identity is also used without domain lock, it matches any domain locked condition', FALSE, FALSE);
		$this->registerArgument('ttl', 'integer', 'Time-to-live for skip registration, number of seconds. After this expires the registration is unset', FALSE, 86400);
	}

	/**
	 * Standard render method. Implementers should override
	 * the assertShouldSkip() method and/or the getIdentifier()
	 * and storeIdentifier() methods as applies to each
	 * implementers method of storing identifiers.
	 *
	 * @return string
	 */
	public function render() {
		$this->removeIfExpired();
		$evaluation = $this->assertShouldSkip();
		if ($evaluation === FALSE) {
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
	protected function getIdentifier() {
		if (isset($this->arguments['identifier']) === TRUE) {
			return $this->arguments['identifier'];
		}
		return get_class($this);
	}

	/**
	 * @retrun void
	 */
	protected function storeIdentifier() {
		$identifier = $this->getIdentifier();
		if (isset(self::$identifiers[$identifier]) === FALSE) {
			self::$identifiers[$identifier] = time();
		}
	}

	/**
	 * @return void
	 */
	protected function removeIfExpired() {
		$identifier = $this->getIdentifier();
		if (isset(self::$identifiers[$identifier]) === TRUE && self::$identifiers[$identifier] <= time() - $this->arguments['ttl']) {
			unset(self::$identifiers[$identifier]);
		}
	}

	/**
	 * @return boolean
	 */
	protected function assertShouldSkip() {
		$identifier = $this->getIdentifier();
		return (isset(self::$identifiers[$identifier]) === TRUE);
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
	protected function renderThenChild() {
		$GLOBALS['TSFE']->no_cache = 1;
		return parent::renderThenChild();
	}

}
