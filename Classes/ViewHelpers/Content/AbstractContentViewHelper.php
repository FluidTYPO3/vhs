<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Content;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\SlideViewHelperTrait;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Base class: Content ViewHelpers
 */
abstract class AbstractContentViewHelper extends AbstractViewHelper
{

    use SlideViewHelperTrait;

    /**
     * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     */
    protected $contentObject;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;


    /**
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
     * @return void
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
        $this->contentObject = $configurationManager->getContentObject();
    }

    /**
     * Initialize
     */
    public function initializeArguments()
    {
        $this->registerArgument('column', 'integer', 'Name of the column to render', false, 0);
        $this->registerArgument(
            'order',
            'string',
            'Optional sort field of content elements - RAND() supported. Note that when sliding is enabled, the ' .
            'sorting will be applied to records on a per-page basis and not to the total set of collected records.',
            false,
            'sorting'
        );
        $this->registerArgument('sortDirection', 'string', 'Optional sort direction of content elements', false, 'ASC');
        $this->registerArgument('pageUid', 'integer', 'If set, selects only content from this page UID', false, 0);
        $this->registerArgument(
            'contentUids',
            'array',
            'If used, replaces all conditions with an "uid IN (1,2,3)" style condition using the UID values from ' .
            'this array'
        );
        $this->registerArgument(
            'sectionIndexOnly',
            'boolean',
            'If TRUE, only renders/gets content that is marked as "include in section index"',
            false,
            false
        );
        $this->registerArgument('loadRegister', 'array', 'List of LOAD_REGISTER variable');
        $this->registerArgument('render', 'boolean', 'Render result', false, true);
        $this->registerArgument(
            'hideUntranslated',
            'boolean',
            'If FALSE, will NOT include elements which have NOT been translated, if current language is NOT the ' .
            'default language. Default is to show untranslated elements but never display the original if there is ' .
            'a translated version',
            false,
            false
        );
        $this->registerSlideArguments();
    }

    /**
     * Get content records based on column and pid
     *
     * @return array
     */
    protected function getContentRecords()
    {
        $limit = $this->arguments['limit'];

        $pageUid = $this->getPageUid();

        $contentRecords = $this->getSlideRecords($pageUid, $limit);

        if (true === (boolean) $this->arguments['render']) {
            $contentRecords = $this->getRenderedRecords($contentRecords);
        }

        return $contentRecords;
    }

    /**
     * @param integer $pageUid
     * @param integer $limit
     * @return array[]
     */
    protected function getSlideRecordsFromPage($pageUid, $limit)
    {
        $column = (integer) $this->arguments['column'];
        $order = $this->arguments['order'];
        if (false === empty($order)) {
            $sortDirection = strtoupper(trim($this->arguments['sortDirection']));
            if ('ASC' !== $sortDirection && 'DESC' !== $sortDirection) {
                $sortDirection = 'ASC';
            }
            $order = $order . ' ' . $sortDirection;
        }
        $contentUids = $this->arguments['contentUids'];
        if (true === is_array($contentUids)) {
            $conditions = 'uid IN (' . implode(',', $contentUids) . ')';
        } else {
            $hideUntranslated = (boolean) $this->arguments['hideUntranslated'];
            $currentLanguage = $GLOBALS['TSFE']->sys_language_content;
            $languageCondition = '(sys_language_uid IN (-1,' . $currentLanguage . ')';
            if (0 < $currentLanguage) {
                if (true === $hideUntranslated) {
                    $languageCondition .= ' AND l18n_parent > 0';
                }
                $nestedQuery = $GLOBALS['TYPO3_DB']->SELECTquery('l18n_parent', 'tt_content', 'sys_language_uid = ' .
                    $currentLanguage . $GLOBALS['TSFE']->cObj->enableFields('tt_content'));
                $languageCondition .= ' AND uid NOT IN (' . $nestedQuery . ')';
            }
            $languageCondition .= ')';
            $conditions = "pid = '" . (integer) $pageUid . "' AND colPos = '" . (integer) $column . "'" .
                $GLOBALS['TSFE']->cObj->enableFields('tt_content') . ' AND ' . $languageCondition;
        }
        if (true === (boolean) $this->arguments['sectionIndexOnly']) {
            $conditions .= ' AND sectionIndex = 1';
        }
        $conditions .= ' AND t3ver_state <= 1';

        $rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'tt_content', $conditions, '', $order, $limit);
        return $rows;
    }

    /**
     * Gets the configured, or the current page UID if
     * none is configured in arguments and no content_from_pid
     * value exists in the current page record's attributes.
     *
     * @return integer
     */
    protected function getPageUid()
    {
        $pageUid = (integer) $this->arguments['pageUid'];
        if (1 > $pageUid) {
            $pageUid = (integer) $GLOBALS['TSFE']->page['content_from_pid'];
        }
        if (1 > $pageUid) {
            $pageUid = (integer) $GLOBALS['TSFE']->id;
        }
        return $pageUid;
    }

    /**
     * This function renders an array of tt_content record into an array of rendered content
     * it returns a list of elements rendered by typoscript RECORD function
     *
     * @param array $rows database rows of records (each item is a tt_content table record)
     * @return array
     */
    protected function getRenderedRecords(array $rows)
    {
        if (false === empty($this->arguments['loadRegister'])) {
            $this->contentObject->cObjGetSingle('LOAD_REGISTER', $this->arguments['loadRegister']);
        }
        $elements = [];
        foreach ($rows as $row) {
            array_push($elements, $this->renderRecord($row));
        }
        if (false === empty($this->arguments['loadRegister'])) {
            $this->contentObject->cObjGetSingle('RESTORE_REGISTER', '');
        }
        return $elements;
    }

    /**
     * This function renders a raw tt_content record into the corresponding
     * element by typoscript RENDER function. We keep track of already
     * rendered records to avoid rendering the same record twice inside the
     * same nested stack of content elements.
     *
     * @param array $row
     * @return string|NULL
     */
    protected function renderRecord(array $row)
    {
        if (0 < $GLOBALS['TSFE']->recordRegister['tt_content:' . $row['uid']]) {
            return null;
        }
        $conf = [
            'tables' => 'tt_content',
            'source' => $row['uid'],
            'dontCheckPid' => 1
        ];
        $parent = $GLOBALS['TSFE']->currentRecord;
        // If the currentRecord is set, we register, that this record has invoked this function.
        // It's should not be allowed to do this again then!!
        if (false === empty($parent)) {
            ++$GLOBALS['TSFE']->recordRegister[$parent];
        }
        $html = $GLOBALS['TSFE']->cObj->cObjGetSingle('RECORDS', $conf);

        $GLOBALS['TSFE']->currentRecord = $parent;
        if (false === empty($parent)) {
            --$GLOBALS['TSFE']->recordRegister[$parent];
        }
        return $html;
    }
}
