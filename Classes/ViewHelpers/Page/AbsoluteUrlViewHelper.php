<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Returns a full, absolute URL to this page with all arguments
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Page
 */
class AbsoluteUrlViewHelper extends AbstractViewHelper {

	/**
	 * @return string
	 */
	public function render() {
		$url = GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL');
		if (0 !== strpos($url, GeneralUtility::getIndpEnv('TYPO3_SITE_URL'))) {
			$url = GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . $url;
		}
		return $url;
	}

}
