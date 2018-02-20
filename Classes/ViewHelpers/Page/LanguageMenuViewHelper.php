<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ArrayConsumingViewHelperTrait;
use FluidTYPO3\Vhs\Utility\CoreUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

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
        $this->registerArgument('defaultIsoFlag', 'string', 'ISO code of the default flag', false, 'gb');
        $this->registerArgument('defaultLanguageLabel', 'string', 'Label for the default language', false, 'English');
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
        $this->registerArgument('useCHash', 'boolean', 'Use cHash for typolink', false, true);
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
        $this->registerArgument('languages', 'mixed', 'Array, CSV or Traversable containing UIDs of languages to render');
    }

    /**
     * Render method
     *
     * @return string
     */
    public function render()
    {
        if (false === is_object($GLOBALS['TSFE']->sys_page)) {
            return null;
        }
        $this->cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $this->tagName = $this->arguments['tagName'];
        $this->tag->setTagName($this->tagName);

        $this->languageMenu = $this->parseLanguageMenu();
        $this->templateVariableContainer->add($this->arguments['as'], $this->languageMenu);
        $content = $this->renderChildren();
        $this->templateVariableContainer->remove($this->arguments['as']);
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
     * Returns the flag source
     *
     * @param string $iso
     * @return string
     */
    protected function getLanguageFlagSrc($iso)
    {
        if ('' !== $this->arguments['flagPath']) {
            $path = trim($this->arguments['flagPath']);
        } else {
            $path = CoreUtility::getLanguageFlagIconPath();
        }

        $imgType = trim($this->arguments['flagImageType']);
        return $path . strtoupper($iso) . '.' . $imgType;
    }

    /**
     * Return the layout: flag & text, flags only or text only
     *
     * @param array $language
     * @return string
     */
    protected function getLayout(array $language)
    {
        $flagImage = false !== stripos($this->arguments['layout'], 'flag') ? $this->getFlagImage($language) : '';
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
     * Render the flag image for autorenderer
     *
     * @param array $language
     * @return string
     */
    protected function getFlagImage(array $language)
    {
        $conf = [
            'file' => $language['flagSrc'],
            'altText' => $language['label'],
            'titleText' => $language['label']
        ];
        return $this->cObj->render($this->cObj->getContentObject('IMAGE'), $conf);
    }

    /**
     * Sets all parameter for langMenu
     *
     * @return array
     */
    protected function parseLanguageMenu()
    {
        $order = $this->arguments['order'] ? GeneralUtility::trimExplode(',', $this->arguments['order']) : '';
        $labelOverwrite = $this->arguments['labelOverwrite'];
        if (!empty($labelOverwrite)) {
            $labelOverwrite = GeneralUtility::trimExplode(',', $this->arguments['labelOverwrite']);
        }

        $languageMenu = [];
        $tempArray = [];

        $tempArray[0] = [
            'label' => $this->arguments['defaultLanguageLabel'],
            'flag' => $this->arguments['defaultIsoFlag']
        ];

        $select = 'uid,title,flag';
        $from = 'sys_language';
        $limitLanguages = static::arrayFromArrayOrTraversableOrCSVStatic($this->arguments['languages'] ?? []);
        $limitLanguages = array_filter($limitLanguages);

        if (!empty($limitLanguages)) {
            $sysLanguage = $GLOBALS['TSFE']->cObj->getRecords($from, ['selectFields' => $select, 'pidInList' => -1, 'uidInList' => implode(',', $limitLanguages)]);
        } else {
            $sysLanguage = $GLOBALS['TSFE']->cObj->getRecords($from, ['selectFields' => $select, 'pidInList' => 'root']);
        }

        foreach ($sysLanguage as $value) {
            $tempArray[$value['uid']] = [
                'label' => $value['title'],
                'flag' => $value['flag'],
            ];
        }

        // reorders languageMenu
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

        // Select all pages_language_overlay records on the current page. Each represents a possibility for a language.
        $table = 'pages_language_overlay';
        $sysLang = $GLOBALS['TSFE']->cObj->getRecords($table, ['selectFields' => 'sys_language_uid', 'pidInList' => $this->getPageUid(), 'languageField' => 0]);
        $languageUids = array_column($sysLang, 'sys_language_uid');

        foreach ($languageMenu as $key => $value) {
            $current = $GLOBALS['TSFE']->sys_language_uid === (integer) $key ? 1 : 0;
            $inactive = in_array($key, $languageUids) || (integer) $key === $this->defaultLangUid ? 0 : 1;
            $url = $this->getLanguageUrl($key);
            if (true === empty($url)) {
                $url = GeneralUtility::getIndpEnv('REQUEST_URI');
            }
            $languageMenu[$key]['current'] = $current;
            $languageMenu[$key]['inactive'] = $inactive;
            $languageMenu[$key]['url'] = $url;
            $languageMenu[$key]['flagSrc'] = $this->getLanguageFlagSrc($value['flag']);
            if (true === (boolean) $this->arguments['hideNotTranslated'] && true === (boolean) $inactive) {
                unset($languageMenu[$key]);
            }
        }

        return $languageMenu;
    }

    /**
     * Get link of language menu entry
     *
     * @param $uid
     * @return string
     */
    protected function getLanguageUrl($uid)
    {
        $excludedVars = trim((string) $this->arguments['excludeQueryVars']);
        $config = [
            'parameter' => $this->getPageUid(),
            'returnLast' => 'url',
            'additionalParams' => '&L=' . $uid,
            'useCacheHash' => $this->arguments['useCHash'],
            'addQueryString' => 1,
            'addQueryString.' => [
                'method' => 'GET',
                'exclude' => 'id,L,cHash' . ($excludedVars ? ',' . $excludedVars : '')
            ]
        ];
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
}
