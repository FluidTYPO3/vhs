<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageService;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Returns the current language from languages depending on l18n settings.
 */
class LanguageViewHelper extends AbstractViewHelper implements CompilableInterface
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

    /**
     * Initialize
     *
     * @return void
     */
    public function initializeArguments()
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
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        if ('BE' === TYPO3_MODE) {
            return '';
        }

        $languages = $arguments['languages'];
        if (true === $languages instanceof \Traversable) {
            $languages = iterator_to_array($languages);
        } elseif (true === is_string($languages)) {
            $languages = GeneralUtility::trimExplode(',', $languages, true);
        } else {
            $languages = (array) $languages;
        }

        $pageUid = intval($arguments['pageUid']);
        $normalWhenNoLanguage = $arguments['normalWhenNoLanguage'];

        if (0 === $pageUid) {
            $pageUid = $GLOBALS['TSFE']->id;
        }

        $pageService = static::getPageService();
        $currentLanguageUid = $GLOBALS['TSFE']->sys_language_uid;
        $languageUid = 0;
        if (false === $pageService->hidePageForLanguageUid($pageUid, $currentLanguageUid, $normalWhenNoLanguage)) {
            $languageUid = $currentLanguageUid;
        } elseif (0 !== $currentLanguageUid) {
            if (true === $pageService->hidePageForLanguageUid($pageUid, 0, $normalWhenNoLanguage)) {
                return '';
            }
        }

        if (false === empty($languages[$languageUid])) {
            return $languages[$languageUid];
        }

        return $languageUid;
    }

    /**
     * @return PageService
     */
    protected static function getPageService()
    {
        return static::$pageService = GeneralUtility::makeInstance(ObjectManager::class)->get(PageService::class);
    }
}
