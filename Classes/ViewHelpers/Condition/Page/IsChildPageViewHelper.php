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
 * ### Condition: Page is child page
 *
 * Condition ViewHelper which renders the `then` child if current
 * page or page with provided UID is a child of some other page in
 * the page tree. If $respectSiteRoot is set to TRUE root pages are
 * never considered child pages even if they are.
 */
class IsChildPageViewHelper extends AbstractConditionViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('pageUid', 'integer', 'value to check');
        $this->registerArgument('respectSiteRoot', 'boolean', 'value to check', false, false);
    }

    /**
     * @return bool
     */
    public static function verdict(array $arguments, RenderingContextInterface $renderingContext): bool
    {
        /** @var int $pageUid */
        $pageUid = $arguments['pageUid'];
        $respectSiteRoot = (bool) $arguments['respectSiteRoot'];

        if (empty($pageUid)) {
            $request = RequestResolver::tryResolveRequestFromRenderingContext($renderingContext, false);
            if ($request === null) {
                return false;
            }
            $pageUid = self::resolveCurrentPageUid($request);
        }
        /** @var PageService $pageService */
        $pageService = GeneralUtility::makeInstance(PageService::class);
        $page = $pageService->getPageRepository()->getPage($pageUid);

        if ($respectSiteRoot && ($page['is_siteroot'] ?? false)) {
            return false;
        }
        return ($page['pid'] ?? 0) > 0;
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

        throw new \RuntimeException('Unable to resolve current page uid from frontend request.', 1774448255);
    }
}
