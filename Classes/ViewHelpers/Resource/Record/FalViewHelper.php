<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource\Record;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Proxy\DoctrineQueryProxy;
use FluidTYPO3\Vhs\Proxy\FileRepositoryProxy;
use FluidTYPO3\Vhs\Proxy\ResourceFactoryProxy;
use FluidTYPO3\Vhs\Utility\ResourceUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Versioning\VersionState;

/**
 * Resolve FAL relations and return file records.
 *
 * ### Render a single image linked from a TCA record
 *
 * We assume that the table `tx_users` has a column `photo`, which is a FAL
 * relation field configured with
 * [`ExtensionManagementUtility::getFileFieldTCAConfig()`]
 * (https://docs.typo3.org/typo3cms/TCAReference/Reference/Columns/Inline/Index.html#file-abstraction-layer).
 * The template also has a `user` variable containing one of the table's
 * records.
 *
 * At first, fetch the record and store it in a variable.
 * Then use `<f:image>` to render it:
 *
 * ```
 * {v:resource.record.fal(table: 'tx_users', field: 'photo', record: user)
 *  -> v:iterator.first()
 *  -> v:variable.set(name: 'image')}
 * <f:if condition="{image}">
 *   <f:image treatIdAsReference="1" src="{image.id}" title="{image.title}" alt="{image.alternative}"/>
 * </f:if>
 * ```
 *
 * Use the `uid` attribute if you don't have a `record`.
 */
class FalViewHelper extends AbstractRecordResourceViewHelper
{
    /**
     * @var ResourceFactoryProxy
     */
    protected $resourceFactory;

    /**
     * @var FileRepositoryProxy
     */
    protected $fileRepository;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        /** @var ResourceFactoryProxy $resourceFactory */
        $resourceFactory = GeneralUtility::makeInstance(ResourceFactoryProxy::class);
        $this->resourceFactory = $resourceFactory;
        /** @var FileRepositoryProxy $fileRepository */
        $fileRepository = GeneralUtility::makeInstance(FileRepositoryProxy::class);
        $this->fileRepository = $fileRepository;
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument(
            'asObjects',
            'bool',
            'Can be set to TRUE to return objects instead of file information arrays.',
            false,
            false
        );
    }

    /**
     * @param FileReference $fileReference
     * @return array
     */
    public function getResource($fileReference)
    {
        $file = $fileReference->getOriginalFile();
        $fileReferenceProperties = $fileReference->getProperties();
        $fileProperties = ResourceUtility::getFileArray($file);
        ArrayUtility::mergeRecursiveWithOverrule($fileProperties, $fileReferenceProperties, true, true, false);
        return $fileProperties;
    }

    /**
     * @param array|integer $uidOrRecord Database row
     */
    protected function getFileReferences(string $table, string $field, $uidOrRecord): array
    {
        if (is_array($uidOrRecord)) {
            $record = $uidOrRecord;
        } else {
            $record = $this->getRecord($uidOrRecord);
            if (!is_array($record)) {
                return [];
            }
        }

        if (isset($record['t3ver_oid']) && (integer) $record['t3ver_oid'] !== 0) {
            $sqlRecordUid = $record['t3ver_oid'];
        } elseif (isset($record['_LOCALIZED_UID'])) {
            $sqlRecordUid = $record['_LOCALIZED_UID'];
        } elseif (isset($record['_PAGES_OVERLAY_UID'])) {
            $sqlRecordUid = $record['_PAGES_OVERLAY_UID'];
        } else {
            $sqlRecordUid = $record[$this->idField];
        }
        $fileObjects = $this->fileRepository->findByRelation($table, $field, $sqlRecordUid);
        return $fileObjects;
    }

    public function getResources(array $record): array
    {
        if (empty($record)) {
            return [];
        }
        if (!empty($GLOBALS['TSFE']->sys_page)) {
            $fileReferences = $this->getFileReferences($this->getTable(), $this->getField(), $record);
        } else {
            if (isset($record['t3ver_oid']) && (integer) $record['t3ver_oid'] !== 0) {
                $sqlRecordUid = $record['t3ver_oid'];
            } elseif (isset($record['_LOCALIZED_UID'])) {
                $sqlRecordUid = $record['_LOCALIZED_UID'];
            } elseif (isset($record['_PAGES_OVERLAY_UID'])) {
                $sqlRecordUid = $record['_PAGES_OVERLAY_UID'];
            } else {
                $sqlRecordUid = $record[$this->idField];
            }

            /** @var ConnectionPool $connectionPool */
            $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = $connectionPool->getQueryBuilderForTable('sys_file_reference');

            $queryBuilder->createNamedParameter($this->getTable(), Connection::PARAM_STR, ':tablenames');
            $queryBuilder->createNamedParameter($sqlRecordUid, Connection::PARAM_INT, ':uid_foreign');
            $queryBuilder->createNamedParameter($this->getField(), Connection::PARAM_STR, ':fieldname');

            $queryBuilder
                ->select('uid')
                ->from('sys_file_reference')
                ->where(
                    $queryBuilder->expr()->eq('tablenames', ':tablenames')
                )
                ->andWhere(
                    $queryBuilder->expr()->eq('uid_foreign', ':uid_foreign')
                )
                ->andWhere(
                    $queryBuilder->expr()->eq('fieldname', ':fieldname')
                );

            if ($GLOBALS['BE_USER']->workspaceRec['uid']) {
                $queryBuilder->createNamedParameter(
                    $GLOBALS['BE_USER']->workspaceRec['uid'],
                    Connection::PARAM_INT,
                    ':t3ver_wsid'
                );
                $queryBuilder
                    ->andWhere(
                        $queryBuilder->expr()->eq('deleted', 0)
                    )
                    ->andWhere(
                        $queryBuilder->expr()->eq('t3ver_wsid', 0)
                        . ' OR ' .
                        $queryBuilder->expr()->eq('t3ver_wsid', ':t3ver_wsid')
                    )
                    ->andWhere(
                        $queryBuilder->expr()->neq('pid', -1)
                    );
            } else {
                $queryBuilder
                    ->andWhere(
                        $queryBuilder->expr()->eq('deleted', 0)
                    )
                    ->andWhere(
                        $queryBuilder->expr()->lte('t3ver_state', 0)
                    )
                    ->andWhere(
                        $queryBuilder->expr()->neq('pid', -1)
                    )
                    ->andWhere(
                        $queryBuilder->expr()->eq('hidden', 0)
                    );
            }

            $queryBuilder->orderBy('sorting_foreign');

            // Execute
            $statement = DoctrineQueryProxy::executeQueryOnQueryBuilder($queryBuilder);
            /** @var array[] $references */
            $references = DoctrineQueryProxy::fetchAllAssociative($statement);

            $fileReferences = [];

            foreach ($references as $reference) {
                try {
                    // Just passing the reference uid, the factory is doing workspace
                    // overlays automatically depending on the current environment
                    $fileReferences[] = $this->resourceFactory->getFileReferenceObject($reference['uid'] ?? 0);
                } catch (ResourceDoesNotExistException $exception) {
                    // No handling, just omit the invalid reference uid
                    continue;
                }
            }
        }
        $resources = [];
        foreach ($fileReferences as $file) {
            // Exclude workspace deleted files references
            if ($file->getProperty('t3ver_state') !== VersionState::DELETE_PLACEHOLDER) {
                try {
                    $resources[] = $this->arguments['asObjects'] ? $file : $this->getResource($file);
                } catch (\InvalidArgumentException $error) {
                    // Pokemon-style, catch-all and suppress. This exception type is thrown if a file gets removed.
                }
            }
        }
        return $resources;
    }
}
