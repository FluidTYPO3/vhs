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
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

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
    static protected $pageService;

    /**
     * @param PageService $pageService
     * @return void
     */
    public static function setPageService(PageService $pageService)
    {
        self::$pageService = $pageService;
    }

    /**
     * Initialize arguments
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

        if (self::$pageService === null) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            self::$pageService = $objectManager->get(PageService::class);
        }

        $menu = self::$pageService->getMenu($pageUid, [], $includeHiddenInMenu, false, $includeAccessProtected);

        return (0 < count($menu));
    }
}
