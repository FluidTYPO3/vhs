<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageService;
use FluidTYPO3\Vhs\Traits\PageRecordViewHelperTrait;
use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * ### Page: Link ViewHelper
 *
 * Viewhelper for rendering page links
 *
 * This viewhelper behaves identically to Fluid's link viewhelper
 * except for it fetches the title of the provided page UID and inserts
 * it as linktext if that is omitted. The link will not render at all
 * if the requested page is not translated in the current language.
 *
 * ```
 * Automatic linktext: <v:page.link pageUid="UID" />
 * Manual linktext:    <v:page.link pageUid="UID">linktext</v:page.link>
 * ```
 */
class LinkViewHelper extends AbstractTagBasedViewHelper
{

    use PageRecordViewHelperTrait;
    use TemplateVariableViewHelperTrait;

    /**
     * @var PageService
     */
    protected $pageService;

    /**
     * @param PageService $pageService
     * @return void
     */
    public function injectPageService(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    /**
     * @var string
     */
    protected $tagName = 'a';

    /**
     * Arguments initialization
     *
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerPageRecordArguments();
        $this->registerTagAttribute('target', 'string', 'Target of link', false);
        $this->registerTagAttribute(
            'rel',
            'string',
            'Specifies the relationship between the current document and the linked document',
            false
        );
        $this->registerArgument(
            'pageUid',
            'integer',
            'UID of the page to create the link and fetch the title for.',
            false,
            0
        );
        $this->registerArgument(
            'additionalParams',
            'array',
            'Query parameters to be attached to the resulting URI',
            false,
            []
        );
        $this->registerArgument('pageType', 'integer', 'Type of the target page. See typolink.parameter', false, 0);
        $this->registerArgument(
            'noCache',
            'boolean',
            'When TRUE disables caching for the target page. You should not need this.',
            false,
            false
        );
        $this->registerArgument(
            'noCacheHash',
            'boolean',
            'When TRUE supresses the cHash query parameter created by TypoLink. You should not need this. '
            . 'Has no effect on TYPO3v11 and above.',
            false,
            false
        );
        $this->registerArgument('section', 'string', 'The anchor to be added to the URI', false, '');
        $this->registerArgument('linkAccessRestrictedPages', 'boolean', 'DEPRECATED: Use showAccessProtected instead.');
        $this->registerArgument(
            'absolute',
            'boolean',
            'When TRUE, the URI of the rendered link is absolute',
            false,
            false
        );
        $this->registerArgument(
            'addQueryString',
            'boolean',
            'When TRUE, the current query parameters will be kept in the URI',
            false,
            false
        );
        $this->registerArgument(
            'argumentsToBeExcludedFromQueryString',
            'array',
            'Arguments to be removed from the URI. Only active if $addQueryString = TRUE',
            false,
            []
        );
        $this->registerArgument(
            'titleFields',
            'string',
            'CSV list of fields to use as link label - default is "nav_title,title", change to for example ' .
            '"tx_myext_somefield,subtitle,nav_title,title". The first field that contains text will be used. ' .
            'Field value resolved AFTER page field overlays.',
            false,
            'nav_title,title'
        );
        $this->registerArgument(
            'pageTitleAs',
            'string',
            'When rendering child content, supplies page title as variable.'
        );
    }

    /**
     * Render method
     * @return string|null
     */
    public function render()
    {
        // Check if link wizard link
        $pageUid = $this->arguments['pageUid'];
        $additionalParameters = (array) $this->arguments['additionalParams'];
        if (false === is_numeric($pageUid)) {
            /** @var LogManager $logManager */
            $logManager = GeneralUtility::makeInstance(LogManager::class);
            $logManager->getLogger(__CLASS__)->warning("pageUid must be numeric, got " . $pageUid);
            return null;
        }

        // Get page via pageUid argument or current id
        $pageUid = (integer) $pageUid;
        if (0 === $pageUid) {
            $pageUid = $GLOBALS['TSFE']->id;
        }

        $showAccessProtected = (boolean) $this->arguments['showAccessProtected'];

        //TODO: Remove handling of deprecated argument
        if ($this->hasArgument('linkAccessRestrictedPages')) {
            $showAccessProtected = (boolean) $this->arguments['linkAccessRestrictedPages'];
        }

        $page = $this->pageService->getPage($pageUid, $showAccessProtected);
        if (empty($page)) {
            return null;
        }

        $targetPage = $this->pageService->getShortcutTargetPage($page);
        if ($targetPage !== null) {
            if ($this->pageService->shouldUseShortcutTarget($this->arguments)) {
                $page = $targetPage;
            }
            if ($this->pageService->shouldUseShortcutUid($this->arguments)) {
                $pageUid = $targetPage['uid'];
            }
        }

        // Do not render the link, if the page should be hidden
        if (class_exists(LanguageAspect::class)) {
            /** @var Context $context */
            $context = GeneralUtility::makeInstance(Context::class);
            /** @var LanguageAspect $languageAspect */
            $languageAspect = $context->getAspect('language');
            $currentLanguageUid = $languageAspect->getId();
        } else {
            $currentLanguageUid = $GLOBALS['TSFE']->sys_language_uid;
        }

        $hidePage = $this->pageService->hidePageForLanguageUid($page, $currentLanguageUid);
        if (true === $hidePage) {
            return null;
        }

        // Get the title from the page or page overlay
        $title = $this->getTitleValue($page);

        // Check if we should assign page title to the template variable container
        $pageTitleAs = $this->arguments['pageTitleAs'];
        if (!empty($pageTitleAs)) {
            $variables = [$pageTitleAs => $title];
        } else {
            $variables = [];
        }

        // Render children to see if an alternative title content should be used
        $renderedTitle = $this->renderChildrenWithVariables($variables);
        if (!empty($renderedTitle)) {
            $title = $renderedTitle;
        }

        $class = [];
        if ($showAccessProtected && $this->pageService->isAccessProtected($page)) {
            $class[] = $this->arguments['classAccessProtected'];
        }
        if ($showAccessProtected && $this->pageService->isAccessGranted($page)) {
            $class[] = $this->arguments['classAccessGranted'];
        }
        $additionalCssClasses = implode(' ', $class);

        /** @var RenderingContext $renderingContext */
        $renderingContext = $this->renderingContext;

        /** @var UriBuilder $uriBuilder */
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        $uriBuilder->reset()
            ->setTargetPageUid($pageUid)
            ->setTargetPageType($this->arguments['pageType'])
            ->setNoCache($this->arguments['noCache'])
            ->setSection($this->arguments['section'])
            ->setArguments($additionalParameters)
            ->setCreateAbsoluteUri($this->arguments['absolute'])
            ->setAddQueryString($this->arguments['addQueryString'])
            ->setArgumentsToBeExcludedFromQueryString((array) $this->arguments['argumentsToBeExcludedFromQueryString'])
            ->setLinkAccessRestrictedPages($showAccessProtected);

        if (method_exists($uriBuilder, 'setUseCacheHash')) {
            $uriBuilder->setUseCacheHash($this->arguments['noCacheHash']);
        }

        $uri = $uriBuilder->build();
        $this->tag->addAttribute('href', $uri);
        $classes = trim($this->arguments['class'] . ' ' . $additionalCssClasses);
        if (!empty($classes)) {
            $this->tag->addAttribute('class', $classes);
        } else {
            $this->tag->removeAttribute('class');
        }
        $this->tag->setContent(is_scalar($title) ? (string) $title : '');
        return $this->tag->render();
    }

    /**
     * @param array $record
     * @return string
     */
    private function getTitleValue($record)
    {
        $titleFieldList = GeneralUtility::trimExplode(',', $this->arguments['titleFields']);
        foreach ($titleFieldList as $titleFieldName) {
            if (!empty($record[$titleFieldName])) {
                return $record[$titleFieldName];
            }
        }
        return '';
    }
}
