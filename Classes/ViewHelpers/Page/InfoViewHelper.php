<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageService;
use FluidTYPO3\Vhs\Traits\CompileWithRenderStatic;
use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use FluidTYPO3\Vhs\Core\ViewHelper\AbstractViewHelper;
use FluidTYPO3\Vhs\Utility\RequestResolver;

/**
 * ViewHelper to access data of the current page record.
 *
 * Does not work in the TYPO3 backend.
 */
class InfoViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;
    use TemplateVariableViewHelperTrait;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerAsArgument();
        $this->registerArgument(
            'pageUid',
            'integer',
            'If specified, this UID will be used to fetch page data instead of using the current page.',
            false,
            0
        );
        $this->registerArgument(
            'field',
            'string',
            'If specified, only this field will be returned/assigned instead of the complete page record.'
        );
    }

    /**
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        /** @var PageService $pageService */
        $pageService = GeneralUtility::makeInstance(PageService::class);
        /** @var int $pageUid */
        $pageUid = (int) ($arguments['pageUid'] ?? 0);
        $request = null;
        try {
            $request = RequestResolver::resolveRequestFromRenderingContext($renderingContext);
            $pageService->setRequest($request);
        } catch (\UnexpectedValueException) {
        }
        $pageRepository = $pageService->getPageRepository();
        if (0 === $pageUid) {
            if ($request instanceof \Psr\Http\Message\ServerRequestInterface
                && $request->getAttribute('routing') instanceof PageArguments
            ) {
                $pageUid = (int) $request->getAttribute('routing')->getPageId();
            } else {
                try {
                    $rootLine = $pageService->getRootLine();
                } catch (\UnexpectedValueException) {
                    $rootLine = [];
                }
                $page = $rootLine[0] ?? [];
                $pageUid = (int) ($page['uid'] ?? 0);
            }
        }
        $page = $pageRepository->getPage_noCheck((int) $pageUid);
        /** @var string|null $field */
        $field = $arguments['field'];
        $content = null;
        if (empty($field)) {
            $content = $page;
        } elseif (is_array($page) && isset($page[$field])) {
            $content = $page[$field];
        }

        /** @var string|null $as */
        $as = $arguments['as'];
        return static::renderChildrenWithVariableOrReturnInputStatic(
            $content,
            $as,
            $renderingContext,
            $renderChildrenClosure
        );
    }
}
