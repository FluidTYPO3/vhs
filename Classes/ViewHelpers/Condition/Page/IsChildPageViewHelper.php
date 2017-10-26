<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;
use TYPO3\CMS\Frontend\Page\PageRepository;

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
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('pageUid', 'integer', 'value to check', false, null);
        $this->registerArgument('respectSiteRoot', 'boolean', 'value to check', false, false);
    }

    /**
     * @param array $arguments
     * @return bool
     */
    protected static function evaluateCondition($arguments = null)
    {
        $pageUid = $arguments['pageUid'];
        $respectSiteRoot = $arguments['respectSiteRoot'];

        if (null === $pageUid || true === empty($pageUid) || 0 === intval($pageUid)) {
            $pageUid = $GLOBALS['TSFE']->id;
        }
        $pageSelect = new PageRepository();
        $page = $pageSelect->getPage($pageUid);

        if ($respectSiteRoot && isset($page['is_siteroot']) && $page['is_siteroot']) {
            return false;
        }
        return true === isset($page['pid']) && 0 < $page['pid'];
    }
}
