<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page\Header;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
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
use FluidTYPO3\Vhs\Service\PageSelectService;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Returns the current canonical url in a link tag.
 *
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Page\Header
 */
class CanonicalViewHelper extends AbstractTagBasedViewHelper {

	/**
	 * @var PageSelectService
	 */
	protected $pageSelect;

	/**
	 * @param PageSelectService $pageSelectService
	 * @return void
	 */
	public function injectPageSelectService(PageSelectService $pageSelectService) {
		$this->pageSelect = $pageSelectService;
	}

	/**
	 * @var string
	 */
	protected $tagName = 'link';

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerUniversalTagAttributes();
		$this->registerArgument('pageUid', 'integer', 'The page uid to check', FALSE, 0);
		$this->registerArgument('normalWhenNoLanguage', 'boolean', 'If TRUE, a missing page overlay should be ignored', FALSE, FALSE);
	}

	/**
	 * @return string
	 */
	public function render() {
		if ('BE' === TYPO3_MODE) {
			return;
		}

		$pageUid = $this->arguments['pageUid'];
		$normalWhenNoLanguage = $this->arguments['normalWhenNoLanguage'];

		if (0 === $pageUid) {
			$pageUid = $GLOBALS['TSFE']->id;
		}

		$currentLanguageUid = $GLOBALS['TSFE']->sys_language_uid;
		$languageUid = 0;
		if (FALSE === $this->pageSelect->hidePageForLanguageUid($pageUid, $currentLanguageUid, $normalWhenNoLanguage)) {
			$languageUid = $currentLanguageUid;
		} else if (0 !== $currentLanguageUid) {
			if (TRUE === $this->pageSelect->hidePageForLanguageUid($pageUid, 0, $normalWhenNoLanguage)) {
				return;
			}
		}

		$uriBuilder = $this->controllerContext->getUriBuilder();
		$uri = $uriBuilder->reset()
			->setTargetPageUid($pageUid)
			->setCreateAbsoluteUri(TRUE)
			->setArguments(array('L' => $languageUid))
			->build();

		$this->tag->addAttribute('rel', 'canonical');
		$this->tag->addAttribute('href', $uri);

		$renderedTag = $this->tag->render();

		if (1 === intval($GLOBALS['TSFE']->config['config']['disableAllHeaderCode'])) {
			return $renderedTag;
		}

		$GLOBALS['TSFE']->getPageRenderer()->addMetaTag($renderedTag);
	}

}
