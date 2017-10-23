<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource\Record;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\ResourceUtility;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException;
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
 *     {v:resource.record.fal(table: 'tx_users', field: 'photo', record: user)
 *      -> v:iterator.first()
 *      -> v:variable.set(name: 'image')}
 *     <f:if condition="{image}">
 *       <f:image treatIdAsReference="1" src="{image.id}" title="{image.title}" alt="{image.alternative}"/>
 *     </f:if>
 *
 * Use the `uid` attribute if you don't have a `record`.
 */
class FalViewHelper extends AbstractRecordResourceViewHelper
{

    /**
     * @var \TYPO3\CMS\Core\Resource\ResourceFactory
     */
    protected $resourceFactory;

    /**
     * @var \TYPO3\CMS\Core\Resource\FileRepository
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
        $this->resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
        $this->fileRepository = GeneralUtility::makeInstance(FileRepository::class);
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
     * Fetch a fileReference from the file repository
     *
     * @param string $table name of the table to get the file reference for
     * @param string $field name of the field referencing a file
     * @param integer $uid uid of the related record
     * @return array
     */
    protected function getFileReferences($table, $field, $uid)
    {
        $fileObjects = $this->fileRepository->findByRelation($table, $field, $uid);
        return $fileObjects;
    }

    /**
     * @param array $record
     * @return array
     */
    public function getResources($record)
    {
        $databaseConnection = $this->getDatabaseConnection();
        if (isset($record['t3ver_oid']) && (integer) $record['t3ver_oid'] !== 0) {
            $sqlRecordUid = $record['t3ver_oid'];
        } elseif (isset($record['_LOCALIZED_UID'])) {
            $sqlRecordUid = $record['_LOCALIZED_UID'];
        } else {
            $sqlRecordUid = $record[$this->idField];
        }

        $fileReferences = [];
        if (empty($GLOBALS['TSFE']->sys_page) === false) {
            $fileReferences = $this->getFileReferences($this->getTable(), $this->getField(), $sqlRecordUid);
        } else {
            if ($GLOBALS['BE_USER']->workspaceRec['uid']) {
                $versionWhere = 'AND sys_file_reference.deleted=0 AND (sys_file_reference.t3ver_wsid=0 OR ' .
                    'sys_file_reference.t3ver_wsid=' . $GLOBALS['BE_USER']->workspaceRec['uid'] .
                    ') AND sys_file_reference.pid<>-1';
            } else {
                $versionWhere = 'AND sys_file_reference.deleted=0 AND sys_file_reference.t3ver_state<=0 AND ' .
                    'sys_file_reference.pid<>-1 AND sys_file_reference.hidden=0';
            }
            $references = $databaseConnection->exec_SELECTgetRows(
                'uid',
                'sys_file_reference',
                'tablenames=' . $databaseConnection->fullQuoteStr($this->getTable(), 'sys_file_reference') .
                    ' AND uid_foreign=' . (int) $sqlRecordUid .
                    ' AND fieldname=' . $databaseConnection->fullQuoteStr($this->getField(), 'sys_file_reference')
                    . $versionWhere,
                '',
                'sorting_foreign',
                '',
                'uid'
            );
            if (empty($references) === false) {
                $referenceUids = array_keys($references);
                $fileReferences = [];
                if (empty($referenceUids) === false) {
                    foreach ($referenceUids as $referenceUid) {
                        try {
                            // Just passing the reference uid, the factory is doing workspace
                            // overlays automatically depending on the current environment
                            $fileReferences[] = $this->resourceFactory->getFileReferenceObject($referenceUid);
                        } catch (ResourceDoesNotExistException $exception) {
                            // No handling, just omit the invalid reference uid
                            continue;
                        }
                    }
                }
            }
        }
        $resources = [];
        foreach ($fileReferences as $file) {
            // Exclude workspace deleted files references
            if ($file->getProperty('t3ver_state') !== VersionState::DELETE_PLACEHOLDER) {
                try {
                    $resources[] = $this->getResource($file);
                } catch (\InvalidArgumentException $error) {
                    // Pokemon-style, catch-all and suppress. This exception type is thrown if a file gets removed.
                }
            }
        }
        return $resources;
    }

    /**
     * @return DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
