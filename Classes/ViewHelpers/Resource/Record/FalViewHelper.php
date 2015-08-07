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
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

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
	 * @var |TYPO3\CMS\Core\Resource\FileRepository
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

	protected function getFileReferences($table, $field,$tt_content) {
		$uid = $tt_content; // content element uid
		$fileObjects = $this->fileRepository->findByRelation($table, $field, $uid);
		return $fileObjects;
	}

	/**
	 * @param array $record
	 * @return array
	 */
	public function getResources($record) {
		$this->contentObj = $this->configurationManager->getContentObject();
		$this->getDatabaseConnection = $GLOBALS['TYPO3_DB'];
		if (isset($record['t3ver_oid']) && 0 != $record['t3ver_oid']) {
			$sqlRecordUid = $record['t3ver_oid'];
		} else {
			$sqlRecordUid = $record[$this->idField];
		}


		if (!empty($GLOBALS['TSFE']->sys_page)) {
			$images = $this->getFileReferences($this->getTable(), $this->getField(), $sqlRecordUid);
		} else {
			if($GLOBALS['BE_USER']->workspaceRec['uid']) {
				$ws = $GLOBALS['BE_USER']->workspaceRec['uid'];
				$versionWhere = 'AND sys_file_reference.deleted=0 AND (sys_file_reference.t3ver_wsid=0 OR sys_file_reference.t3ver_wsid='.$ws.') AND sys_file_reference.pid<>-1';
			} else {
				$versionWhere = 'AND sys_file_reference.deleted=0 AND sys_file_reference.t3ver_state<=0 AND sys_file_reference.pid<>-1 AND sys_file_reference.hidden=0';
			}
			$references = $this->getDatabaseConnection->exec_SELECTgetRows(
					'uid',
					'sys_file_reference',
					'tablenames=' . $this->getDatabaseConnection->fullQuoteStr($this->getTable(), 'sys_file_reference') .
					' AND uid_foreign=' . (int)$sqlRecordUid .
					' AND fieldname=' . $this->getDatabaseConnection->fullQuoteStr($this->getField(), 'sys_file_reference')
					. $versionWhere,
					'',
					'sorting_foreign',
					'',
					'uid'
			);
			if (!empty($references)) {
				$referenceUids = array_keys($references);
			}
			if (!empty($referenceUids)) {
				foreach ($referenceUids as $referenceUid) {
					try {
						// Just passing the reference uid, the factory is doing workspace
						// overlays automatically depending on the current environment
						$itemList[] = $this->resourceFactory->getFileReferenceObject($referenceUid);
					} catch (ResourceDoesNotExistException $exception) {
						// No handling, just omit the invalid reference uid
					}
				}
			}
			$images = $itemList;
		}
		$resources = array();
		foreach($images as $file) {
			$resources[] = $this->getResource($file);
		}
		return $resources;
	}

}
