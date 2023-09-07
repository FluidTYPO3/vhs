<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageService;
use FluidTYPO3\Vhs\Utility\ContextUtility;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Returns the current language from languages depending on l18n settings.
 */
class LanguageViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @var PageService
     */
    protected static $pageService;

    public function initializeArguments(): void
    {
        $this->registerArgument('languages', 'mixed', 'The languages (either CSV, array or implementing Traversable)');
        $this->registerArgument('pageUid', 'integer', 'The page uid to check', false, 0);
        $this->registerArgument(
            'normalWhenNoLanguage',
            'boolean',
            'If TRUE, a missing page overlay should be ignored',
            false,
            false
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
        if (ContextUtility::isBackend()) {
            return '';
        }

        /** @var array|string|\Traversable $languages */
        $languages = $arguments['languages'];
        if ($languages instanceof \Traversable) {
            $languages = iterator_to_array($languages);
        } elseif (is_string($languages)) {
            $languages = GeneralUtility::trimExplode(',', $languages, true);
        } else {
            $languages = (array) $languages;
        }

        /** @var int $pageUid */
        $pageUid = $arguments['pageUid'];
        $pageUid = (integer) $pageUid;
        /** @var bool $normalWhenNoLanguage */
        $normalWhenNoLanguage = $arguments['normalWhenNoLanguage'];

        if (0 === $pageUid) {
            $pageUid = $GLOBALS['TSFE']->id;
        }

        $pageService = static::getPageService();
        if (class_exists(LanguageAspect::class)) {
            /** @var Context $context */
            $context = GeneralUtility::makeInstance(Context::class);
            /** @var LanguageAspect $languageAspect */
            $languageAspect = $context->getAspect('language');
            $currentLanguageUid = $languageAspect->getId();
        } else {
            $currentLanguageUid = $GLOBALS['TSFE']->sys_language_uid;
        }
        $languageUid = 0;
        if (!$pageService->hidePageForLanguageUid($pageUid, $currentLanguageUid, $normalWhenNoLanguage)) {
            $languageUid = $currentLanguageUid;
        } elseif (0 !== $currentLanguageUid) {
            if ($pageService->hidePageForLanguageUid($pageUid, 0, $normalWhenNoLanguage)) {
                return '';
            }
        }

        if (!empty($languages[$languageUid])) {
            return $languages[$languageUid];
        }

        return $languageUid;
    }

    protected static function getPageService(): PageService
    {
        /** @var PageService $pageService */
        $pageService = GeneralUtility::makeInstance(PageService::class);
        return static::$pageService = $pageService;
    }
}
