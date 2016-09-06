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
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to get the rootline of a page.
 */
class RootlineViewHelper extends AbstractViewHelper
{

    use TemplateVariableViewHelperTrait;

    /**
     * @var PageService
     */
    protected $pageService;

    /**
     * @param PageService $pageService
     */
    public function injectPageService(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    /**
     * @api
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerAsArgument();
        $this->registerArgument('pageUid', 'integer', 'Optional page uid to use.', false, 0);
    }

    /**
     * @return mixed
     */
    public function render()
    {
        $pageUid = (integer) $this->arguments['pageUid'];
        if (0 === $pageUid) {
            $pageUid = $GLOBALS['TSFE']->id;
        }
        $rootLineData = $this->pageService->getRootLine($pageUid);

        return $this->renderChildrenWithVariableOrReturnInput($rootLineData);
    }
}
