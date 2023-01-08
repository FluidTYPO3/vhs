<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Doctrine\DBAL\Result;
use FluidTYPO3\Vhs\Traits\ArrayConsumingViewHelperTrait;
use FluidTYPO3\Vhs\Utility\CoreUtility;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Site\Site;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * ViewHelper for rendering TYPO3 menus in Fluid
 * Require the extension static_info_table.
 */
class LanguageMenuViewHelper extends AbstractTagBasedViewHelper
{
    use ArrayConsumingViewHelperTrait;

    /**
     * @var array
     */
    protected $languageMenu = [];

    /**
     * @var integer
     */
    protected $defaultLangUid = 0;

    /**
     * @var string
     */
    protected $tagName = 'ul';

    /**
     * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     */
    protected $cObj;

    /**
     * @var \TYPO3\CMS\Core\Site\Site|\TYPO3\CMS\Core\Site\Entity\Site
     */
    protected $site;

    /**
     * Initialize
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerArgument(
            'tagName',
            'string',
            'Tag name to use for enclosing container, list and flags (not finished) only',
            false,
            'ul'
        );
        $this->registerArgument(
            'tagNameChildren',
            'string',
            'Tag name to use for child nodes surrounding links, list and flags only',
            false,
            'li'
        );
        $this->registerArgument('defaultIsoFlag', 'string', 'ISO code of the default flag', false);
        $this->registerArgument('defaultLanguageLabel', 'string', 'Label for the default language', false);
        $this->registerArgument('order', 'mixed', 'Orders the languageIds after this list', false, '');
        $this->registerArgument('labelOverwrite', 'mixed', 'Overrides language labels');
        $this->registerArgument(
            'hideNotTranslated',
            'boolean',
            'Hides languageIDs which are not translated',
            false,
            false
        );
        $this->registerArgument(
            'layout',
            'string',
            'How to render links when using autorendering. Possible selections: name,flag - use fx "name" or ' .
            '"flag,name" or "name,flag"',
            false,
            'flag,name'
        );
        $this->registerArgument(
            'useCHash',
            'boolean',
            'Use cHash for typolink. Has no effect on TYPO3 v9.5+',
            false,
            true
        );
        $this->registerArgument('flagPath', 'string', 'Overwrites the path to the flag folder', false, '');
        $this->registerArgument('flagImageType', 'string', 'Sets type of flag image: png, gif, jpeg', false, 'svg');
        $this->registerArgument('linkCurrent', 'boolean', 'Sets flag to link current language or not', false, true);
        $this->registerArgument(
            'classCurrent',
            'string',
            'Sets the class, by which the current language will be marked',
            false,
            'current'
        );
        $this->registerArgument(
            'as',
            'string',
            'If used, stores the menu pages as an array in a variable named according to this value and renders ' .
            'the tag content - which means automatic rendering is disabled if this attribute is used',
            false,
            'languageMenu'
        );
        $this->registerArgument('pageUid', 'integer', 'Optional page uid to use.', false, 0);
        $this->registerArgument('configuration', 'array', 'Additional typoLink configuration', false, []);
        $this->registerArgument('excludeQueryVars', 'string', 'Comma-separate list of variables to exclude', false, '');
        $this->registerArgument(
            'languages',
            'mixed',
            'Array, CSV or Traversable containing UIDs of languages to render'
        );
    }

    /**
     * Render method
     *
     * @return string
     */
    public function render()
    {
        if (false === is_object($GLOBALS['TSFE']->sys_page)) {
            return '';
        }
        /** @var ContentObjectRenderer $contentObject */
        $contentObject = $GLOBALS['TSFE']->cObj;
        $this->cObj = $contentObject;
        $this->tagName = $this->arguments['tagName'];
        $this->tag->setTagName($this->tagName);

        if (class_exists(SiteFinder::class)) {
            $this->site = $this->getSite();
            $this->defaultLangUid = $this->site->getDefaultLanguage()->getLanguageId();
        }
        $this->languageMenu = $this->parseLanguageMenu();
        $this->renderingContext->getVariableProvider()->add($this->arguments['as'], $this->languageMenu);
        $content = $this->renderChildren();
        $content = is_scalar($content) ? (string) $content : '';
        $this->renderingContext->getVariableProvider()->remove($this->arguments['as']);
        if (0 === mb_strlen(trim($content))) {
            $content = $this->autoRender();
        }
        return $content;
    }

    /**
     * Automatically render a language menu
     *
     * @return string
     */
    protected function autoRender()
    {
        $content = $this->getLanguageMenu();
        $content = trim($content);
        if (false === empty($content)) {
            $this->tag->setContent($content);
            $content = $this->tag->render();
        }
        return $content;
    }

    /**
     * Get layout 0 (default): list
     *
     * @return    string
     */
    protected function getLanguageMenu()
    {
        $tagName = $this->arguments['tagNameChildren'];
        $html = [];
        $itemCount = count($this->languageMenu);
        foreach ($this->languageMenu as $index => $var) {
            $class = '';
            $classes = [];
            if (true === (boolean) $var['inactive']) {
                $classes[] = 'inactive';
            }
            if (true === (boolean) $var['current']) {
                $classes[] = $this->arguments['classCurrent'];
            }
            if (0 === $index) {
                $classes[] = 'first';
            } elseif (($itemCount - 1) === $index) {
                $classes[] = 'last';
            }
            if (0 < count($classes)) {
                $class = ' class="' . implode(' ', $classes) . '" ';
            }
            if (true === (boolean) $var['current'] && false === (boolean) $this->arguments['linkCurrent']) {
                $html[] = '<' . $tagName . $class . '>' . $this->getLayout($var) . '</' . $tagName . '>';
            } else {
                $html[] = '<' . $tagName . $class . '><a href="' . htmlspecialchars($var['url']) . '">' .
                    $this->getLayout($var) . '</a></' . $tagName . '>';
            }
        }
        return implode(LF, $html);
    }

    /**
     * Returns the flag source given the language ISO code
     *
     * @param string $iso
     * @param string $label
     * @return string
     */
    protected function getLanguageFlag($iso, $label)
    {
        if ('' !== $this->arguments['flagPath']) {
            $path = trim($this->arguments['flagPath']);
        } else {
            $path = CoreUtility::getLanguageFlagIconPath();
        }

        $imgType = trim($this->arguments['flagImageType']);
        $conf = [
            'file' => $path . strtoupper($iso) . '.' . $imgType,
            'altText' => $label,
            'titleText' => $label
        ];

        if (file_exists($conf['file'])) {
            $contentObjectDefinition = $this->cObj->getContentObject('IMAGE');
            if ($contentObjectDefinition === null) {
                return '';
            }
            return $this->cObj->render($contentObjectDefinition, $conf);
        }
        return '';
    }

    /**
     * Returns the flag source given a TYPO3 icon identifier
     *
     * @param string $identifier
     * @return string
     */
    protected function getLanguageFlagByIdentifier($identifier)
    {
        /** @var IconFactory $iconFactory */
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $icon = $iconFactory->getIcon($identifier, Icon::SIZE_SMALL);
        return $icon->render();
    }

    /**
     * Return the layout: flag & text, flags only or text only
     *
     * @param array $language
     * @return string
     */
    protected function getLayout(array $language)
    {
        $flagImage = false !== stripos($this->arguments['layout'], 'flag') ? $language['flagCode'] : '';
        $label = $language['label'];
        switch ($this->arguments['layout']) {
            case 'flag':
                $html = $flagImage;
                break;
            case 'name':
                $html = $label;
                break;
            case 'name,flag':
                $html = $label;
                if ('' !== $flagImage) {
                    $html .= '&nbsp;' . $flagImage;
                }
                break;
            case 'flag,name':
            default:
                if ('' !== $flagImage) {
                    $html = $flagImage . '&nbsp;' . $label;
                } else {
                    $html = $label;
                }
        }
        return $html;
    }

    /**
     * Sets all parameter for langMenu
     *
     * @return array
     */
    protected function parseLanguageMenu()
    {
        /** @var iterable $order */
        $order = $this->arguments['order'] ? GeneralUtility::trimExplode(',', $this->arguments['order']) : '';
        /** @var array $labelOverwrite */
        $labelOverwrite = $this->arguments['labelOverwrite'];
        if (!empty($labelOverwrite)) {
            $labelOverwrite = GeneralUtility::trimExplode(',', $this->arguments['labelOverwrite']);
        }

        // first gather languages into this array so we can reorder it later
        $tempArray = [];
        $limitLanguages = static::arrayFromArrayOrTraversableOrCSVStatic($this->arguments['languages'] ?? []);
        $limitLanguages = array_filter($limitLanguages);

        if (!class_exists(SiteFinder::class)) {
            // TYPO3 < 9 legacy
            $tempArray = $this->getLanguagesFromSysLanguage($limitLanguages);
        } else {
            // site configuration is available since TYPO3 9 and offers a consolidated and more
            // detailed view of the language configuration, so it is preferred
            $tempArray = $this->getLanguagesFromSiteConfiguration($limitLanguages);
        }

        // reorder languageMenu
        $languageMenu = [];
        if (false === empty($order)) {
            foreach ($order as $value) {
                if (isset($tempArray[$value])) {
                    $languageMenu[$value] = $tempArray[$value];
                }
            }
        } else {
            $languageMenu = $tempArray;
        }

        // overwrite of label
        if (false === empty($labelOverwrite)) {
            $i = 0;
            foreach ($languageMenu as $key => $value) {
                $languageMenu[$key]['label'] = $labelOverwrite[$i];
                $i++;
            }
        }

        // get the languages actually available on this page
        $languageUids = $this->getSystemLanguageUids();

        if (class_exists(LanguageAspect::class)) {
            /** @var Context $context */
            $context = GeneralUtility::makeInstance(Context::class);
            /** @var LanguageAspect $languageAspect */
            $languageAspect = $context->getAspect('language');
            $languageUid = $languageAspect->getId();
        } else {
            $languageUid = $GLOBALS['TSFE']->sys_language_uid;
        }

        foreach ($languageMenu as $key => $value) {
            $current = $languageUid === (integer) $key ? 1 : 0;
            $inactive = in_array($key, $languageUids) || (integer) $key === $this->defaultLangUid ? 0 : 1;
            $url = $this->getLanguageUrl($key);
            if (true === empty($url)) {
                $url = GeneralUtility::getIndpEnv('REQUEST_URI');
            }
            $languageMenu[$key]['current'] = $current;
            $languageMenu[$key]['inactive'] = $inactive;
            $languageMenu[$key]['url'] = $url;
            $languageMenu[$key]['flagSrc'] = $this->getLanguageFlag($value['flag'] ?? $value['iso'], $value['label']);
            // if the user has set a flag path, always use that over the TYPO3 icon factory so the user
            // has the option to use custom flag images based on the ISO code of the language.
            // if the user has not set a flag path, prefer the TYPO3 icon factory when an icon
            // identifier is available (i.e., when using the site-based language lookup) .
            if (isset($value['flagIdentifier']) && empty($this->arguments['flagPath'])) {
                $languageMenu[$key]['flagCode'] = $this->getLanguageFlagByIdentifier($value['flagIdentifier']);
            } else {
                $languageMenu[$key]['flagCode'] = $this->getLanguageFlag(
                    $value['flag'] ?? $value['iso'],
                    $value['label']
                );
            }
            if (true === (boolean) $this->arguments['hideNotTranslated'] && true === (boolean) $inactive) {
                unset($languageMenu[$key]);
            }
        }

        return $languageMenu;
    }

    /**
     * Get the list of languages from the sys_language table
     *
     * @param array $limitLanguages
     * @return array
     */
    protected function getLanguagesFromSysLanguage(array $limitLanguages)
    {
        // add default language
        $result[0] = [
            'label' => $this->arguments['defaultLanguageLabel'] ?? 'English',
            'flag' => $this->arguments['defaultIsoFlag'] ?? 'gb'
        ];

        $select = 'uid,title,flag';
        $from = 'sys_language';

        if (!empty($limitLanguages)) {
            $sysLanguage = $GLOBALS['TSFE']->cObj->getRecords(
                $from,
                ['selectFields' => $select, 'pidInList' => 'root', 'uidInList' => implode(',', $limitLanguages)]
            );
        } else {
            $sysLanguage = $GLOBALS['TSFE']->cObj->getRecords(
                $from,
                ['selectFields' => $select, 'pidInList' => 'root']
            );
        }

        foreach ($sysLanguage as $value) {
            $result[$value['uid']] = [
                'label' => $value['title'],
                'flag' => $value['flag'],
            ];
        }

        return $result;
    }

    /**
     * Get the list of languages from the site configuration
     *
     * @param array $limitLanguages
     * @return array
     */
    protected function getLanguagesFromSiteConfiguration(array $limitLanguages)
    {
        $site = $this->getSite();
        // get only languages set as visible in frontend
        $languages = $site->getLanguages();
        $defaultLanguage = $site->getDefaultLanguage();

        $result = [];
        foreach ($languages as $language) {
            if (!empty($limitLanguages) && !in_array($language->getLanguageId(), $limitLanguages)) {
                continue;
            }
            $label = $language->getNavigationTitle();
            $flag = $language->getFlagIdentifier();
            if ($language->getLanguageId() == $defaultLanguage->getLanguageId()) {
                // override label/flag of default language if given
                $label = $this->arguments['defaultLanguageLabel'] ?? $label;
                $flag = $this->arguments['defaultIsoFlag'] ?? $flag;
            }
            $result[$language->getLanguageId()] = [
                'label' => $label,
                'iso' => $language->getTwoLetterIsoCode(),
                'flagIdentifier' => $flag
            ];
        }

        return $result;
    }

    /**
     * Get link of language menu entry
     *
     * @param int|string $languageId
     * @return string
     */
    protected function getLanguageUrl($languageId)
    {
        $excludedVars = trim((string) $this->arguments['excludeQueryVars']);
        $config = [
            'parameter' => $this->getPageUid(),
            'returnLast' => 'url',
            'additionalParams' => '&L=' . $languageId,
            'addQueryString' => 1,
            'addQueryString.' => [
                'method' => 'GET',
                'exclude' => 'id,L,cHash' . ($excludedVars ? ',' . $excludedVars : '')
            ]
        ];
        if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '9.5', '<')) {
            $config['useCacheHash'] = $this->arguments['useCHash'];
        }
        if (true === is_array($this->arguments['configuration'])) {
            $config = $this->mergeArrays($config, $this->arguments['configuration']);
        }
        return $this->cObj->typoLink('', $config);
    }

    /**
     * Get page via pageUid argument or current id
     *
     * @return integer
     */
    protected function getPageUid()
    {
        $pageUid = (integer) $this->arguments['pageUid'];
        if (0 === $pageUid) {
            $pageUid = $GLOBALS['TSFE']->id;
        }

        return (integer) $pageUid;
    }

    /**
     * Find the site corresponding to the page that the menu is being rendered for
     *
     * @return Site|\TYPO3\CMS\Core\Site\Entity\Site
     */
    protected function getSite()
    {
        /** @var SiteFinder $siteFinder */
        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        return $siteFinder->getSiteByPageId($this->getPageUid());
    }

    /**
     * Fetches system languages available on the page depending on the TYPO3 version.
     *
     * @return int[]
     * @phpcsSuppress
     * @see https://docs.typo3.org/typo3cms/extensions/core/Changelog/9.0/Important-82445-MigratePagesLanguageOverlayIntoPages.html
     */
    protected function getSystemLanguageUids()
    {
        if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '9.0', '<')) {
            $table = 'pages_language_overlay';
            $parentField = 'pid';
        } else {
            $table = 'pages';
            $parentField = 'l10n_parent';
        }

        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $connection = $connectionPool->getConnectionForTable($table);
        $queryBuilder = $connection->createQueryBuilder();
        /** @var Result $result */
        $result = $queryBuilder->select('sys_language_uid')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq($parentField, $this->getPageUid())
            )
            ->execute();
        /** @var array $rows */
        $rows = $result->fetchAllAssociative();

        return array_column($rows, 'sys_language_uid');
    }
}
