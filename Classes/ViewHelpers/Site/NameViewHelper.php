<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Site;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### Site: Name
 *
 * Returns the site name as specified in TYPO3_CONF_VARS.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Site
 */
class NameViewHelper extends AbstractViewHelper {

	/**
	 * @return string
	 */
	public function render() {
		$name = $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'];
		return $name;
	}

}
