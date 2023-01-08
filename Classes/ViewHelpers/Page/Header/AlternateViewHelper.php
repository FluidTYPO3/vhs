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
use FluidTYPO3\Vhs\Utility\ContextUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
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
     * @var \TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder
     */
    protected $tagBuilder;

    /**
     * @param PageService $pageService
     * @return void
     */
    public function injectPageService(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    public function __construct()
    {
        /** @var TagBuilder $tagBuilder */
        $tagBuilder = GeneralUtility::makeInstance(TagBuilder::class);
        $this->tagBuilder = $tagBuilder;
    }


    /**
     * @return void
     */
    public function initializeArguments()
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

    /**
     * @return string
     */
    public function render()
    {
        if (ContextUtility::isBackend()) {
            return '';
        }

        $languages = $this->arguments['languages'];
        if (true === $languages instanceof \Traversable) {
            $languages = iterator_to_array($languages);
        } elseif (true === is_string($languages)) {
            $languages = GeneralUtility::trimExplode(',', $languages, true);
        } else {
            $languages = (array) $languages;
        }

        $pageUid = (integer) $this->arguments['pageUid'];
        if (0 === $pageUid) {
            $pageUid = $GLOBALS['TSFE']->id;
        }

        $normalWhenNoLanguage = $this->arguments['normalWhenNoLanguage'];
        $addQueryString = $this->arguments['addQueryString'];

        /** @var UriBuilder $uriBuilder */
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        $uriBuilder = $uriBuilder->reset()
            ->setTargetPageUid($pageUid)
            ->setCreateAbsoluteUri(true)
            ->setAddQueryString($addQueryString);

        $this->tagBuilder->reset();
        $this->tagBuilder->setTagName('link');
        $this->tagBuilder->addAttribute('rel', 'alternate');

        /** @var PageRenderer $pageRenderer */
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $usePageRenderer = (1 !== (integer) ($GLOBALS['TSFE']->config['config']['disableAllHeaderCode'] ?? 0));
        $output = '';

        foreach ($languages as $languageUid => $languageName) {
            if (false === $this->pageService->hidePageForLanguageUid($pageUid, $languageUid, $normalWhenNoLanguage)) {
                $uri = $uriBuilder->setArguments(['L' => $languageUid])->build();
                $this->tagBuilder->addAttribute('href', $uri);
                $this->tagBuilder->addAttribute('hreflang', $languageName);

                $renderedTag = $this->tagBuilder->render();
                if (true === $usePageRenderer) {
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

        if (false === $usePageRenderer) {
            return trim($output);
        }

        return '';
    }
}
