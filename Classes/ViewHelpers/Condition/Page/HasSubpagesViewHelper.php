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
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * ### Condition: Page has subpages
 *
 * A condition ViewHelper which renders the `then` child if
 * current page or page with provided UID has subpages. By default
 * disabled subpages are considered non existent which can be overridden
 * by setting $includeHidden to TRUE. To include pages that are hidden
 * in menus set $showHiddenInMenu to TRUE.
 */
class HasSubpagesViewHelper extends AbstractConditionViewHelper
{
    /**
     * @var PageService
     */
    protected static $pageService;

    /**
     * @param PageService $pageService
     * @return void
     */
    public static function setPageService(PageService $pageService)
    {
        static::$pageService = $pageService;
    }

    /**
     * Initialize arguments
     *
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('pageUid', 'integer', 'Parent page to check');
        $this->registerArgument('includeHidden', 'boolean', 'DEPRECATED: Include hidden pages', false, false);
        $this->registerArgument('includeAccessProtected', 'boolean', 'Include access protected pages', false, false);
        $this->registerArgument('includeHiddenInMenu', 'boolean', 'Include pages hidden in menu', false, false);
        $this->registerArgument('showHiddenInMenu', 'boolean', 'DEPRECATED: Use includeHiddenInMenu');
    }

    /**
     * @param array $arguments
     * @return boolean
     */
    protected static function evaluateCondition($arguments = null)
    {
        if (!is_array($arguments)) {
            return false;
        }
        $pageUid = $arguments['pageUid'];
        //TODO: remove fallback with removal of deprecated argument
        if (null !== $arguments['showHiddenInMenu']) {
            $includeHiddenInMenu = (boolean) $arguments['showHiddenInMenu'];
        } else {
            $includeHiddenInMenu = (boolean) $arguments['includeHiddenInMenu'];
        }
        $includeAccessProtected = (boolean) $arguments['includeAccessProtected'];

        if (null === $pageUid || true === empty($pageUid) || 0 === (integer) $pageUid) {
            $pageUid = $GLOBALS['TSFE']->id;
        }

        if (static::$pageService === null) {
            /** @var PageService $pageService */
            $pageService = GeneralUtility::makeInstance(PageService::class);
            static::$pageService = $pageService;
        }

        $menu = static::$pageService->getMenu($pageUid, [], $includeHiddenInMenu, false, $includeAccessProtected);

        return (0 < count($menu));
    }
}
