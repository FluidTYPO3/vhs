<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ConditionViewHelperTrait;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * ### Condition: Page is child page
 *
 * Condition ViewHelper which renders the `then` child if current
 * page or page with provided UID is a child of some other page in
 * the page tree. If $respectSiteRoot is set to TRUE root pages are
 * never considered child pages even if they are.
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 */
class IsChildPageViewHelper extends AbstractConditionViewHelper
{

    use ConditionViewHelperTrait;

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
     * This method decides if the condition is TRUE or FALSE. It can be overriden in extending viewhelpers to adjust functionality.
     *
     * @param array $arguments ViewHelper arguments to evaluate the condition for this ViewHelper, allows for flexiblity in overriding this method.
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

        if (true === (boolean) $respectSiteRoot && true === isset($page['is_siteroot']) && true === (boolean) $page['is_siteroot']) {
            return false;
        }
        return true === isset($page['pid']) && 0 < $page['pid'];
    }
}
