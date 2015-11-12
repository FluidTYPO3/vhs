<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource\Record;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\ResourceUtility;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException;

/**
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Resource\Record
 */
class FalViewHelper extends AbstractRecordResourceViewHelper {

	/**
	 * @var \TYPO3\CMS\Core\Resource\ResourceFactory
	 */
	protected $resourceFactory;

	/**
	 * @var \TYPO3\CMS\Core\Resource\FileRepository
	 */
	protected $fileRepository;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->resourceFactory = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\ResourceFactory');
		$this->fileRepository = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\FileRepository');
	}

	/**
	 * @param mixed $identity
	 * @return mixed
	 */
	public function getResource($fileReference) {
		$file = $fileReference->getOriginalFile();
		$fileReferenceProperties = $fileReference->getProperties();
		$fileProperties = ResourceUtility::getFileArray($file);
		ArrayUtility::mergeRecursiveWithOverrule($fileProperties, $fileReferenceProperties, TRUE, FALSE, FALSE);
		return $fileProperties;
	}

	/**
	 * Fetch a fileRefernce from the file repository
	 *
	 * @param $table name of the table to get the file reference for
	 * @param $field name of the field referencing a file
	 * @param $uid uid of the related record
	 * @return array
	 */
	protected function getFileReferences($table, $field, $uid) {
		$fileObjects = $this->fileRepository->findByRelation($table, $field, $uid);
		return $fileObjects;
	}

	/**
	 * @param array $record
	 * @return array
	 */
	public function getResources($record) {
		/** @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $contentObj */
		$contentObjectRenderer = $this->configurationManager->getContentObject();

		/** @var \TYPO3\CMS\Core\Database\DatabaseConnection $databaseConnection */
		$databaseConnection = $this->getDatabaseConntection();

		if (isset($record['t3ver_oid']) && (integer) $record['t3ver_oid'] !== 0) {
			$sqlRecordUid = $record['t3ver_oid'];
		} else {
			$sqlRecordUid = $record[$this->idField];
		}

		if (FALSE === empty($GLOBALS['TSFE']->sys_page)) {
			$images = $this->getFileReferences($this->getTable(), $this->getField(), $sqlRecordUid);
		} else {
			if ($GLOBALS['BE_USER']->workspaceRec['uid']) {
				$versionWhere = 'AND sys_file_reference.deleted=0 AND (sys_file_reference.t3ver_wsid=0 OR sys_file_reference.t3ver_wsid=' . $GLOBALS['BE_USER']->workspaceRec['uid'] . ') AND sys_file_reference.pid<>-1';
			} else {
				$versionWhere = 'AND sys_file_reference.deleted=0 AND sys_file_reference.t3ver_state<=0 AND sys_file_reference.pid<>-1 AND sys_file_reference.hidden=0';
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
			if (FALSE === empty($references)) {
				$referenceUids = array_keys($references);
			}
			$images = array();
			if (FALSE === empty($referenceUids)) {
				foreach ($referenceUids as $referenceUid) {
					try {
						// Just passing the reference uid, the factory is doing workspace
						// overlays automatically depending on the current environment
						$images[] = $this->resourceFactory->getFileReferenceObject($referenceUid);
					} catch (ResourceDoesNotExistException $exception) {
						// No handling, just omit the invalid reference uid
						continue;
					}
				}
			}
		}
		$resources = array();
		foreach ($images as $file) {
			$resources[] = $this->getResource($file);
		}
		return $resources;
	}

	/**
	 * @return DatabaseConnection
	 */
	protected function getDatabaseConntection() {
		return $GLOBALS['TYPO3_DB'];
	}

}
