<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageService;
use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to access data of the current page record
 *
 * @author Björn Fromme <fromeme@dreipunktnull.com>, dreipunktnull
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Page
 */
class InfoViewHelper extends AbstractViewHelper {

	use TemplateVariableViewHelperTrait;

	/**
	 * @var PageService
	 */
	protected $pageService;

	/**
	 * @param PageService $pageService
	 */
	public function injectPageService(PageService $pageService) {
		$this->pageService = $pageService;
	}

	public function initializeArguments() {
		$this->registerAsArgument();
		$this->registerArgument('pageUid', 'integer', 'If specified, this UID will be used to fetch page data instead of using the current page.', FALSE, 0);
		$this->registerArgument('field', 'string', 'If specified, only this field will be returned/assigned instead of the complete page record.', FALSE, NULL);
	}

	/**
	 * @return mixed
	 */
	public function render() {
		$pageUid = (integer) $this->arguments['pageUid'];
		if (0 === $pageUid) {
			$pageUid = $GLOBALS['TSFE']->id;
		}
		$page = $this->pageService->getPage($pageUid);
		$field = $this->arguments['field'];
		$content = NULL;
		if (TRUE === empty($field)) {
			$content = $page;
		} elseif (TRUE === isset($page[$field])) {
			$content = $page[$field];
		}

		return $this->renderChildrenWithVariableOrReturnInput($content);
	}

}
