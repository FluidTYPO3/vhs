<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Base class for media related view helpers
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Media
 */
abstract class AbstractMediaViewHelper extends AbstractTagBasedViewHelper {

	/**
	 *
	 * @var string
	 */
	protected $mediaSource;

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		$this->registerArgument('src', 'mixed', 'Path to the media resource(s). Can contain single or multiple paths for videos/audio (either CSV, array or implementing Traversable).', TRUE);
		$this->registerArgument('relative', 'boolean', 'If FALSE media URIs are rendered absolute. URIs in backend mode are always absolute.', FALSE, TRUE);
	}

	/**
	 * Turns a relative source URI into an absolute URL
	 * if required
	 *
	 * @param string $src
	 * @return string
	 */
	public function preprocessSourceUri($src) {
		if (FALSE === empty($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhs.']['settings.']['prependPath'])) {
			$src = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhs.']['settings.']['prependPath'] . $src;
		} elseif ('BE' === TYPO3_MODE || FALSE === (boolean) $this->arguments['relative']) {
			$src = GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . $src;
		}
		return $src;
	}

	/**
	 * Returns an array of sources resolved from src argument
	 * which can be either an array, CSV or implement Traversable
	 * to be consumed by ViewHelpers handling multiple sources.
	 *
	 * @return array
	 */
	public function getSourcesFromArgument() {
		$src = $this->arguments['src'];
		if ($src instanceof \Traversable) {
			$src = iterator_to_array($src);
		} elseif (TRUE === is_string($src)) {
			$src = GeneralUtility::trimExplode(',', $src, TRUE);
		}
		return $src;
	}

}
