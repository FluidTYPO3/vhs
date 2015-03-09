<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Uri;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### Uri: Request
 *
 * Returns the Uri of the requested page (site_url + all the GET params)
 * \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL')
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Uri
 */
class RequestViewHelper extends AbstractViewHelper {

	/**
	 * @return string
	 */
	public function render() {
		$url = GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL');
		return $url;
	}

}
