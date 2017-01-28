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
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\TagBuilder;

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
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \TYPO3\CMS\Fluid\Core\ViewHelper\TagBuilder
     */
    protected $tagBuilder;

    /**
     * @param PageService $pageService
     */
    public function injectPageService(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    /**
     * @param ObjectManagerInterface $objectManager
     * @return void
     */
    public function injectObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
        $this->tagBuilder = $this->objectManager->get(TagBuilder::class);
    }

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
        if ('BE' === TYPO3_MODE) {
            return null;
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
        $uriBuilder = $this->controllerContext->getUriBuilder();
        $uriBuilder = $uriBuilder->reset()
            ->setTargetPageUid($pageUid)
            ->setCreateAbsoluteUri(true)
            ->setAddQueryString($addQueryString);

        $this->tagBuilder->reset();
        $this->tagBuilder->setTagName('link');
        $this->tagBuilder->addAttribute('rel', 'alternate');

        /** @var PageRenderer $pageRenderer */
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $usePageRenderer = (1 !== (integer) $GLOBALS['TSFE']->config['config']['disableAllHeaderCode']);
        $output = '';

        foreach ($languages as $languageUid => $languageName) {
            if (false === $this->pageService->hidePageForLanguageUid($pageUid, $languageUid, $normalWhenNoLanguage)) {
                $uri = $uriBuilder->setArguments(['L' => $languageUid])->build();
                $this->tagBuilder->addAttribute('href', $uri);
                $this->tagBuilder->addAttribute('hreflang', $languageName);

                $renderedTag = $this->tagBuilder->render();
                if (true === $usePageRenderer) {
                    $pageRenderer->addMetaTag($renderedTag);
                } else {
                    $output .= $renderedTag . LF;
                }
            }
        }

        if (false === $usePageRenderer) {
            return trim($output);
        }

        return null;
    }
}
