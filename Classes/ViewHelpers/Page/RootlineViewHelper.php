<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageSelectService;
use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to get the rootline of a page
 *
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Page
 */
class RootlineViewHelper extends AbstractViewHelper {

	use TemplateVariableViewHelperTrait;

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
		$this->registerAsArgument();
		$this->registerArgument('pageUid', 'integer', 'Optional page uid to use.', FALSE, 0);
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
		return $this->renderChildrenWithVariableOrReturnInput($rootLineData);
	}

}
