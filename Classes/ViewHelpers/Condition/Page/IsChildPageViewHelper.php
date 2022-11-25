<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * ### Condition: Page is child page
 *
 * Condition ViewHelper which renders the `then` child if current
 * page or page with provided UID is a child of some other page in
 * the page tree. If $respectSiteRoot is set to TRUE root pages are
 * never considered child pages even if they are.
 */
class IsChildPageViewHelper extends AbstractConditionViewHelper
{
    /**
     * Initialize arguments
     *
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('pageUid', 'integer', 'value to check');
        $this->registerArgument('respectSiteRoot', 'boolean', 'value to check', false, false);
    }

    /**
     * @param array $arguments
     * @return bool
     */
    public static function verdict(array $arguments, RenderingContextInterface $renderingContext)
    {
        $pageUid = $arguments['pageUid'];
        $respectSiteRoot = $arguments['respectSiteRoot'];

        if (null === $pageUid || empty($pageUid) || 0 === intval($pageUid)) {
            $pageUid = $GLOBALS['TSFE']->id;
        }
        /** @var PageService $pageService */
        $pageService = GeneralUtility::makeInstance(PageService::class);
        $page = $pageService->getPageRepository()->getPage($pageUid);

        if ($respectSiteRoot && ($page['is_siteroot'] ?? false)) {
            return false;
        }
        return ($page['pid'] ?? 0) > 0;
    }
}
