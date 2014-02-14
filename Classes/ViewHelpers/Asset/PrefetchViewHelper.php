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
 * ************************************************************* */

/**
 * ### Asset DNS Prefetching ViewHelper
 *
 * Enables the special `<link rel="dns-prefetch" />` tag
 * which instructs the browser to start prefetching DNS
 * records for every domain listed in the `domains` attribute
 * of this ViewHelper. Prefetching starts as soon as the browser
 * becomes aware of the tag - to optimise even further, you may
 * wish to control the output buffer's size to deliver your site
 * HTML in chunks, the first chunk being the one containing this
 * ViewHelper.
 *
 * Note that the web server daemon may send headers which prevent
 * this prefetching and that these headers can be added in many
 * ways. If prefetching does not work, you will need to inspect
 * the HTTP headers returned from the actual environment. Or you
 * may prefer to simply add `force="TRUE"` to this tag - but
 * beware that this will affect the entire document's behaviour,
 * not just for this particular set of domain prefetches. Once
 * force-enabled this setting cannot be disabled (unless done so
 * by manually adding an additional meta header tag as examplified
 * by the `build()` method.
 *
 * ### Example usage:
 *
 *     <v:asset.prefetch domains="fedext.net,ajax.google.com" />
 *
 * See: https://developer.mozilla.org/en-US/docs/Controlling_DNS_prefetching
 *
 * @package Vhs
 * @subpackage ViewHelpers
 */
class Tx_Vhs_ViewHelpers_Asset_PrefetchViewHelper extends Tx_Vhs_ViewHelpers_Asset_AbstractAssetViewHelper {

	/**
	 * @var string
	 */
	protected $type = 'link';

	/**
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('domains', 'mixed', 'Domain DNS names to prefetch. By default will add all sys_domain record DNS names', TRUE);
		$this->registerArgument('protocol', 'string', 'Optional value of protocol as inserted in the resulting HREF value. If you experience problems with a non-protocol link, try enforcing http/https here', FALSE, NULL);
		$this->registerArgument('protocolSeparator', 'string', 'If you do not enforce a particular protocol and wish to remove the double slashes from the hostname (your browser may not understand this!), set this attribute to an empty value (not-zero)', FALSE, '//');
		$this->registerArgument('force', 'boolean', 'If TRUE, adds an additional meta header tag which forces prefetching to be enabled even if otherwise requested by the http daemon', FALSE, FALSE);
	}


	/**
	 * @return void
	 */
	public function render() {
		$this->arguments['standalone'] = TRUE;
		$this->arguments['allowMoveToFooter'] = FALSE;
		$this->tagBuilder->forceClosingTag(FALSE);
		$this->tagBuilder->addAttribute('rel', 'dns-prefetch');
		$this->tagBuilder->addAttribute('href', '');
		$this->tagBuilder->setTagName('link');
		$this->finalize();
	}

	/**
	 * @return string
	 */
	public function build() {
		$domains = $this->arguments['domains'];
		if (FALSE === is_array($domains)) {
			$domains = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $domains, TRUE);
		}
		$headerCode = '';
		if (TRUE === (boolean) $this->arguments['force']) {
			$headerCode .= '<meta http-equiv="x-dns-prefetch-control" content="off">' . LF;
		}
		foreach ($domains as $domain) {
			$this->tagBuilder->removeAttribute('href');
			$this->tagBuilder->addAttribute('href', $this->arguments['protocol'] . $this->arguments['protocolSeparator'] . $domain);
			$headerCode .= $this->tagBuilder->render() . LF;
		}
		return $headerCode;
	}

}