<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageService;
use FluidTYPO3\Vhs\Utility\RequestResolver;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageInformation;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
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

    public static function setPageService(PageService $pageService): void
    {
        static::$pageService = $pageService;
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('pageUid', 'integer', 'Parent page to check');
        $this->registerArgument('includeHidden', 'boolean', 'DEPRECATED: Include hidden pages', false, false);
        $this->registerArgument('includeAccessProtected', 'boolean', 'Include access protected pages', false, false);
        $this->registerArgument('includeHiddenInMenu', 'boolean', 'Include pages hidden in menu', false, false);
    }

    public static function verdict(array $arguments, RenderingContextInterface $renderingContext): bool
    {
        /** @var int $pageUid */
        $pageUid = $arguments['pageUid'];
        $includeHiddenInMenu = (bool) $arguments['includeHiddenInMenu'];
        $includeAccessProtected = (bool) $arguments['includeAccessProtected'];

        if (empty($pageUid) || 0 === (int) $pageUid) {
            $request = RequestResolver::tryResolveRequestFromRenderingContext($renderingContext, false);
            if ($request === null) {
                return false;
            }
            $pageUid = self::resolveCurrentPageUid($request);
        }

        if (static::$pageService === null) {
            /** @var PageService $pageService */
            $pageService = GeneralUtility::makeInstance(PageService::class);
            static::$pageService = $pageService;
        }
        static::$pageService->setRequest(
            RequestResolver::tryResolveRequestFromRenderingContext($renderingContext, false)
        );

        $menu = static::$pageService->getMenu($pageUid, [], $includeHiddenInMenu, false, $includeAccessProtected);

        return (0 < count($menu));
    }

    private static function resolveCurrentPageUid(ServerRequestInterface $request): int
    {
        $pageInformation = $request->getAttribute('frontend.page.information');
        if ($pageInformation instanceof PageInformation) {
            return $pageInformation->getId();
        }

        $routing = $request->getAttribute('routing');
        if ($routing instanceof PageArguments) {
            return $routing->getPageId();
        }

        throw new \RuntimeException('Unable to resolve current page uid from frontend request.', 1774448253);
    }
}
