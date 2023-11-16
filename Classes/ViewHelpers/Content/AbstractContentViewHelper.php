<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Content;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\SlideViewHelperTrait;
use FluidTYPO3\Vhs\Utility\DoctrineQueryProxy;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Base class: Content ViewHelpers
 */
abstract class AbstractContentViewHelper extends AbstractViewHelper
{
    use SlideViewHelperTrait;

    /**
     * @var ContentObjectRenderer
     */
    protected $contentObject;

    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager): void
    {
        $this->configurationManager = $configurationManager;
        /** @var ContentObjectRenderer $contentObject */
        $contentObject = $this->configurationManager->getContentObject();
        $this->contentObject = $contentObject;
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('column', 'integer', 'Column position number (colPos) of the column to render');
        $this->registerArgument(
            'order',
            'string',
            'Optional sort field of content elements - RAND() supported. Note that when sliding is enabled, the ' .
            'sorting will be applied to records on a per-page basis and not to the total set of collected records.',
            false,
            'sorting'
        );
        $this->registerArgument('sortDirection', 'string', 'Optional sort direction of content elements', false, 'ASC');
        $this->registerArgument(
            'pageUid',
            'integer',
            'If set, selects only content from this page UID. Ignored when "contentUids" is specified.',
            false,
            0
        );
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

    protected function getContentRecords(): array
    {
        /** @var int $limit */
        $limit = $this->arguments['limit'];
        /** @var int $slide */
        $slide = $this->arguments['slide'];

        $pageUid = $this->getPageUid();

        if ((integer) $slide === 0) {
            $contentRecords = $this->getSlideRecordsFromPage($pageUid, $limit);
        } else {
            $contentRecords = $this->getSlideRecords($pageUid, $limit);
        }

        if ($this->arguments['render']) {
            $contentRecords = $this->getRenderedRecords($contentRecords);
        }

        return $contentRecords;
    }

    protected function getSlideRecordsFromPage(int $pageUid, ?int $limit): array
    {
        /** @var string $direction */
        $direction = $this->arguments['sortDirection'];
        /** @var string $order */
        $order = $this->arguments['order'];
        if (!empty($order)) {
            $sortDirection = strtoupper(trim($direction));
            if ('ASC' !== $sortDirection && 'DESC' !== $sortDirection) {
                $sortDirection = 'ASC';
            }
            $order = $order . ' ' . $sortDirection;
        }

        $contentUids = $this->arguments['contentUids'];
        if (is_array($contentUids) && !empty($contentUids)) {
            return $GLOBALS['TSFE']->cObj->getRecords(
                'tt_content',
                [
                    'uidInList' => implode(',', $contentUids),
                    'orderBy' => $order,
                    'max' => $limit,
                    // Note: pidInList must not use $pageUid which defaults to current PID. Use argument-passed pageUid!
                    // A value of zero here removes the "pid" from the condition generated by ContentObjectRenderer.
                    'pidInList' => (integer)$pageUid,
                    'includeRecordsWithoutDefaultTranslation' => !$this->arguments['hideUntranslated']
                ]
            );
        }

        $conditions = '1=1';
        if (is_numeric($this->arguments['column'])) {
            $conditions = sprintf('colPos = %d', (integer) $this->arguments['column']);
        }
        if ($this->arguments['sectionIndexOnly']) {
            $conditions .= ' AND sectionIndex = 1';
        }

        $rows = $GLOBALS['TSFE']->cObj->getRecords(
            'tt_content',
            [
                'where' => $conditions,
                'orderBy' => $order,
                'max' => $limit,
                'pidInList' => $pageUid,
                'includeRecordsWithoutDefaultTranslation' => !$this->arguments['hideUntranslated']
            ]
        );

        return $rows;
    }

    /**
     * Gets the configured, or the current page UID if
     * none is configured in arguments and no content_from_pid
     * value exists in the current page record's attributes.
     */
    protected function getPageUid(): int
    {
        /** @var array|null $contentUids */
        $contentUids = $this->arguments['contentUids'] ?? null;

        if (!empty($contentUids)) {
            return 0;
        }

        /** @var int $pageUid */
        $pageUid = $this->arguments['pageUid'];

        $pageUid = (integer) $pageUid;
        if (1 > $pageUid) {
            $pageUid = (integer) ($GLOBALS['TSFE']->page['content_from_pid'] ?? 0);
        }
        if (1 > $pageUid) {
            $pageUid = (integer) ($GLOBALS['TSFE']->id ?? 0);
        }
        return $pageUid;
    }

    /**
     * This function renders an array of tt_content record into an array of rendered content
     * it returns a list of elements rendered by typoscript RECORD function
     */
    protected function getRenderedRecords(array $rows): array
    {
        /** @var array $loadRegister */
        $loadRegister = $this->arguments['loadRegister'];
        if (!empty($loadRegister)) {
            $this->contentObject->cObjGetSingle('LOAD_REGISTER', $loadRegister);
        }
        $elements = [];
        foreach ($rows as $row) {
            $elements[] = static::renderRecord($row);
        }
        if (!empty($loadRegister)) {
            $this->contentObject->cObjGetSingle('RESTORE_REGISTER', []);
        }
        return $elements;
    }

    /**
     * This function renders a raw tt_content record into the corresponding
     * element by typoscript RENDER function. We keep track of already
     * rendered records to avoid rendering the same record twice inside the
     * same nested stack of content elements.
     */
    protected static function renderRecord(array $row): ?string
    {
        if (0 < ($GLOBALS['TSFE']->recordRegister['tt_content:' . $row['uid']] ?? 0)) {
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
        if (!empty($parent)) {
            if (isset($GLOBALS['TSFE']->recordRegister[$parent])) {
                ++$GLOBALS['TSFE']->recordRegister[$parent];
            } else {
                $GLOBALS['TSFE']->recordRegister[$parent] = 1;
            }
        }
        $html = $GLOBALS['TSFE']->cObj->cObjGetSingle('RECORDS', $conf);

        $GLOBALS['TSFE']->currentRecord = $parent;
        if (!empty($parent)) {
            --$GLOBALS['TSFE']->recordRegister[$parent];
        }
        return $html;
    }

    protected function executeSelectQuery(string $fields, string $condition, string $order, int $limit): array
    {
        $queryBuilder = (new ConnectionPool())->getConnectionForTable('tt_content')->createQueryBuilder();
        $queryBuilder->select($fields)->from('tt_content')->where($condition);
        if ($order) {
            $orderings = explode(' ', $order);
            $queryBuilder->orderBy($orderings[0], $orderings[1]);
        }
        if ($limit) {
            $queryBuilder->setMaxResults($limit);
        }
        $result = DoctrineQueryProxy::executeQueryOnQueryBuilder($queryBuilder);
        return DoctrineQueryProxy::fetchAllAssociative($result);
    }

    protected function generateSelectQuery(string $fields, string $condition): string
    {
        $queryBuilder = (new ConnectionPool())->getConnectionForTable('tt_content')->createQueryBuilder();
        $queryBuilder->select($fields)->from('tt_content')->where($condition);
        return $queryBuilder->getSQL();
    }
}
