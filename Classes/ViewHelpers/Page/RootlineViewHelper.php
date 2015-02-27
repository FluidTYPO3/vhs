<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageSelectService;
use FluidTYPO3\Vhs\Utility\ViewHelperUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to get the rootline of a page
 *
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Page
 */
class RootlineViewHelper extends AbstractViewHelper {

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
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('pageUid', 'integer', 'Optional page uid to use.', FALSE, 0);
		$this->registerArgument('as', 'string', 'If specified, a template variable with this name containing the requested data will be inserted instead of returning it.', FALSE, NULL);
	}

	/**
	 * @return mixed
	 */
	public function render() {
		$pageUid = $this->arguments['pageUid'];
		if (0 === $pageUid) {
			$pageUid = $GLOBALS['TSFE']->id;
		}

		$rootLineData = $this->pageSelect->getRootLine($pageUid);

		$as = $this->arguments['as'];
		if (TRUE === empty($as)) {
			return $rootLineData;
		}

		$variables = array($as => $rootLineData);
		$output = ViewHelperUtility::renderChildrenWithVariables($this, $this->templateVariableContainer, $variables);
		return $output;
	}

}
