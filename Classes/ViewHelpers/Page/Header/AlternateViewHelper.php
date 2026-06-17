<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page\Header;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageService;
use FluidTYPO3\Vhs\Traits\PageRendererTrait;
use FluidTYPO3\Vhs\Utility\RequestResolver;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use FluidTYPO3\Vhs\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Frontend\Page\PageInformation;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

/**
 * Returns the all alternate urls.
 */
class AlternateViewHelper extends AbstractViewHelper
{
    use PageRendererTrait;

    protected PageService $pageService;

    protected TagBuilder $tagBuilder;

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
        $request = RequestResolver::tryResolveRequestFromRenderingContext($this->renderingContext, false);
        if (!$request instanceof ServerRequestInterface) {
            return '';
        }

        if (ApplicationType::fromRequest($request)->isBackend()) {
            return '';
        }

        /** @var array<int, string>|string $languages */
        $languages = $this->arguments['languages'];
        if ($languages instanceof \Traversable) {
            $languages = iterator_to_array($languages);
        } elseif (is_string($languages)) {
            $languages = GeneralUtility::trimExplode(',', $languages, true);
        } else {
            $languages = (array) $languages;
        }

        /** @var int $pageUid */
        $pageUid = $this->arguments['pageUid'];
        $pageUid = (int) $pageUid;
        if (0 === $pageUid) {
            $pageUid = $this->getCurrentPageUid($request);
        }

        /** @var bool $normalWhenNoLanguage */
        $normalWhenNoLanguage = $this->arguments['normalWhenNoLanguage'];
        $addQueryString = (bool) $this->arguments['addQueryString'];

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
        $usePageRenderer = !$this->isAllHeaderCodeDisabled($request);
        $output = '';

        foreach ($languages as $languageUid => $languageName) {
            if (!$this->pageService->hidePageForLanguageUid($pageUid, $languageUid, $normalWhenNoLanguage)) {
                $uri = $uriBuilder->setArguments(['L' => $languageUid])->build();
                $this->tagBuilder->addAttribute('href', $uri);
                $this->tagBuilder->addAttribute('hreflang', $languageName);

                $renderedTag = $this->tagBuilder->render();
                if ($usePageRenderer) {
                    if (method_exists($pageRenderer, 'addMetaTag')) {
                        $pageRenderer->addMetaTag($renderedTag);
                    } else {
                        $pageRenderer->addHeaderData($renderedTag);
                    }
                } else {
                    $output .= $renderedTag . LF;
                }
            }
        }

        if (!$usePageRenderer) {
            return trim($output);
        }

        return '';
    }

    private function getCurrentPageUid(ServerRequestInterface $request): int
    {
        $pageInformation = $request->getAttribute('frontend.page.information');
        if ($pageInformation instanceof PageInformation) {
            return $pageInformation->getId();
        }

        $routing = $request->getAttribute('routing');
        if ($routing instanceof PageArguments) {
            return $routing->getPageId();
        }

        throw new \RuntimeException('Unable to resolve current page uid from frontend request.', 1774448272);
    }

    private function isAllHeaderCodeDisabled(ServerRequestInterface $request): bool
    {
        $frontendTypoScript = $request->getAttribute('frontend.typoscript');
        if (!is_object($frontendTypoScript)
            || !method_exists($frontendTypoScript, 'hasConfig')
            || !method_exists($frontendTypoScript, 'getConfigArray')
            || !$frontendTypoScript->hasConfig()
        ) {
            return false;
        }

        $config = $frontendTypoScript->getConfigArray();
        return 1 === (int) ($config['disableAllHeaderCode'] ?? 0);
    }
}
