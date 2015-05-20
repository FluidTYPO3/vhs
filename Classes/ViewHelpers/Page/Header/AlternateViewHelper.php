<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page\Header;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageSelectService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Returns the all alternate urls.
 *
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Page\Header
 */
class AlternateViewHelper extends AbstractViewHelper {

	/**
	 * @var PageSelectService
	 */
	protected $pageSelect;

	/**
	 * @var ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @var \TYPO3\CMS\Fluid\Core\ViewHelper\TagBuilder
	 */
	protected $tagBuilder;

	/**
	 * @param PageSelectService $pageSelectService
	 * @return void
	 */
	public function injectPageSelectService(PageSelectService $pageSelectService) {
		$this->pageSelect = $pageSelectService;
	}

	/**
	 * @param ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
		$this->tagBuilder = $this->objectManager->get('TYPO3\\CMS\\Fluid\\Core\\ViewHelper\\TagBuilder');
	}

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('languages', 'mixed', 'The languages (either CSV, array or implementing Traversable)', TRUE);
		$this->registerArgument('pageUid', 'integer', 'The page uid to check', FALSE, 0);
		$this->registerArgument('normalWhenNoLanguage', 'boolean', 'If TRUE, a missing page overlay should be ignored', FALSE, FALSE);
	}

	/**
	 * @return string
	 */
	public function render() {
		if ('BE' === TYPO3_MODE) {
			return '';
		}

		$languages = $this->arguments['languages'];
		if (TRUE === $languages instanceof \Traversable) {
			$languages = iterator_to_array($languages);
		} elseif (TRUE === is_string($languages)) {
			$languages = GeneralUtility::trimExplode(',', $languages, TRUE);
		} else {
			$languages = (array) $languages;
		}

		$pageUid = intval($this->arguments['pageUid']);
		$normalWhenNoLanguage = $this->arguments['normalWhenNoLanguage'];

		if (0 === $pageUid) {
			$pageUid = $GLOBALS['TSFE']->id;
		}

		/** @var UriBuilder $uriBuilder */
		$uriBuilder = $this->controllerContext->getUriBuilder();
		$uriBuilder = $uriBuilder->reset()
			->setTargetPageUid($pageUid)
			->setCreateAbsoluteUri(TRUE);

		$this->tagBuilder->reset();
		$this->tagBuilder->setTagName('link');
		$this->tagBuilder->addAttribute('rel', 'alternate');

		/** @var PageRenderer $pageRenderer */
		$pageRenderer = $GLOBALS['TSFE']->getPageRenderer();
		$usePageRenderer = (1 !== intval($GLOBALS['TSFE']->config['config']['disableAllHeaderCode']));
		$output = '';

		foreach ($languages as $languageUid => $languageName) {
			if (FALSE === $this->pageSelect->hidePageForLanguageUid($pageUid, $languageUid, $normalWhenNoLanguage)) {
				$uri = $uriBuilder->setArguments(array('L' => $languageUid))->build();
				$this->tagBuilder->addAttribute('href', $uri);
				$this->tagBuilder->addAttribute('hreflang', $languageName);

				$renderedTag = $this->tagBuilder->render();
				if (TRUE === $usePageRenderer) {
					$pageRenderer->addMetaTag($renderedTag);
				} else {
					$output .= $renderedTag . LF;
				}
			}
		}

		if (FALSE === $usePageRenderer) {
			return trim($output);
		}

		return '';
	}
}
