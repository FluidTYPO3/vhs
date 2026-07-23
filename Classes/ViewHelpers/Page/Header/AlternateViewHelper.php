<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page\Header;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Core\ViewHelper\AbstractViewHelper;
use FluidTYPO3\Vhs\Service\PageService;
use FluidTYPO3\Vhs\Traits\PageRendererTrait;
use FluidTYPO3\Vhs\Utility\ContextUtility;
use FluidTYPO3\Vhs\Utility\RequestResolver;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

/**
 * Returns the all alternate urls.
 */
class AlternateViewHelper extends AbstractViewHelper
{
    use PageRendererTrait;

    /**
     * @var PageService
     */
    protected $pageService;

    /**
     * @var TagBuilder
     */
    protected $tagBuilder;

    public function injectPageService(PageService $pageService): void
    {
        $this->pageService = $pageService;
    }

    public function __construct()
    {
        /** @var TagBuilder $tagBuilder */
        $tagBuilder = GeneralUtility::makeInstance(TagBuilder::class);
        $this->tagBuilder = $tagBuilder;
    }

    public function initializeArguments(): void
    {
        $this->registerArgument(
            'languages',
            'mixed',
            'The languages (either CSV, array or implementing Traversable)',
            true
        );
        $this->registerArgument('pageUid', 'integer', 'The page uid to check', false, 0);
        $this->registerArgument(
            'normalWhenNoLanguage',
            'boolean',
            'If TRUE, a missing page overlay should be ignored',
            false,
            false
        );
        $this->registerArgument(
            'addQueryString',
            'boolean',
            'If TRUE, the current query parameters will be kept in the URI',
            false,
            false
        );
    }

    public function render(): string
    {
        if (ContextUtility::isBackend()) {
            return '';
        }

        /** @var int|null|string $pageUid */
        $pageUid = $this->arguments['pageUid'];

        /** @var array<int, string>|string $languages */
        $languages = $this->arguments['languages'];
        if ($languages instanceof \Traversable) {
            $languages = iterator_to_array($languages);
        } elseif (is_string($languages)) {
            $languages = GeneralUtility::trimExplode(',', $languages, true);
        } else {
            $languages = (array) $languages;
        }

        $pageUid = (int) ($pageUid ?: RequestResolver::getPageUid());

        /** @var bool $normalWhenNoLanguage */
        $normalWhenNoLanguage = $this->arguments['normalWhenNoLanguage'];
        $addQueryString = (bool) $this->arguments['addQueryString'];

        $request = RequestResolver::resolveRequestFromRenderingContext($this->renderingContext);
        if (!$request instanceof Request) {
            $request = new Request($request);
        }

        /** @var UriBuilder $uriBuilder */
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $uriBuilder->setRequest($request);

        $uriBuilder = $uriBuilder->reset()
            ->setTargetPageUid($pageUid)
            ->setCreateAbsoluteUri(true)
            ->setAddQueryString($addQueryString);

        $this->tagBuilder->reset();
        $this->tagBuilder->setTagName('link');
        $this->tagBuilder->addAttribute('rel', 'alternate');

        /** @var PageRenderer $pageRenderer */
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);

        foreach ($languages as $languageUid => $languageName) {
            if ($this->pageService->hidePageForLanguageUid($pageUid, $languageUid, $normalWhenNoLanguage)) {
                continue;
            }

            $uri = $uriBuilder->setArguments(['L' => $languageUid])->build();
            $this->tagBuilder->addAttribute('href', $uri);
            $this->tagBuilder->addAttribute('hreflang', $languageName);

            $renderedTag = $this->tagBuilder->render();
            $pageRenderer->addHeaderData($renderedTag);
        }

        return '';
    }
}
