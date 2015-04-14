<?php
namespace FluidTYPO3\Vhs\Traits;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Resource\Filter\FileExtensionFilter;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;
use FluidTYPO3\Vhs\Utility\ResourceUtility;


/**
 * Class ResourceViewHelperTrait
 *
 * Trait implemented by ViewHelpers that operate with
 * File and Folder Resources
 *
 * Contains the following main responsibilities:
 *
 * - get files of given identifiers or categories
 * - fetch files of folders, given by FAL identifier.
 *   With the ability to filter the files on given file extensions.
 */
trait ResourceViewHelperTrait {

	use ArrayConsumingViewHelperTrait;

	/**
	 *
	 * @var FileExtensionFilter
	 */
	protected $filter = NULL;

	/**
	 * Returns the files
	 *
	 * @param boolean $onlyProperties
	 * @param mixed $identifier
	 * @param mixed $categories
	 * @return array|NULL
	 */
	public function getFiles($onlyProperties = FALSE, $identifier = NULL, $categories = NULL) {
		$identifier = $this->convertValueOrGetArgumentAsArray($identifier, 'identifier');
		$categories = $this->convertValueOrGetArgumentAsArray($categories, 'categories');
		$treatIdAsUid = (boolean) $this->arguments['treatIdAsUid'];
		$treatIdAsReference = (boolean) $this->arguments['treatIdAsReference'];

		if (TRUE === $treatIdAsUid && TRUE === $treatIdAsReference) {
			throw new \RuntimeException('The arguments "treatIdAsUid" and "treatIdAsReference" may not both be TRUE.', 1384604695);
		}

		if (TRUE === empty($identifier) && TRUE === empty($categories)) {
			return NULL;
		}

		$files = array();
		$resourceFactory = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\ResourceFactory');

		if (FALSE === empty($categories)) {
			$sqlCategories = implode(',', $GLOBALS['TYPO3_DB']->fullQuoteArray($categories, 'sys_category_record_mm'));
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid_foreign', 'sys_category_record_mm', 'tablenames = \'sys_file\' AND uid_local IN (' . $sqlCategories . ')');

			$fileUids = array();
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$fileUids[] = intval($row['uid_foreign']);
			}
			$fileUids = array_unique($fileUids);

			if (TRUE === empty($identifier)) {
				foreach ($fileUids as $fileUid) {
					try {
						$file = $resourceFactory->getFileObject($fileUid);

						if (TRUE === $onlyProperties) {
							$file = ResourceUtility::getFileArray($file);
						}

						$files[] = $file;
					} catch (\Exception $e) {
						continue;
					}
				}

				return $files;
			}
		}

		foreach ($identifier as $i) {
			try {
				if (TRUE === $treatIdAsUid) {
					$file = $resourceFactory->getFileObject(intval($i));
				} elseif (TRUE === $treatIdAsReference) {
					$fileReference = $resourceFactory->getFileReferenceObject(intval($i));
					$file = $fileReference->getOriginalFile();
				} else {
					$file = $resourceFactory->getFileObjectFromCombinedIdentifier($i);
				}

				if (TRUE === isset($fileUids) && FALSE === in_array($file->getUid(), $fileUids)) {
					continue;
				}

				if (TRUE === $onlyProperties) {
					$file = ResourceUtility::getFileArray($file);
				}

				$files[] = $file;
			} catch (\Exception $e) {
				continue;
			}
		}

		return $files;
	}

	/**
	 * Returns the files of the folders
	 *
	 * @param boolean $onlyProperties
	 * @param mixed   $identifier
	 * @param mixed   $recursive
	 *
	 * @return array|NULL
	 */
	public function getFilesOfFolders($onlyProperties = FALSE, $identifier = NULL, $recursive = NULL) {
		$identifier = $this->convertValueOrGetArgumentAsArray($identifier, 'identifier');
		$recursive = NULL === $recursive ? $this->arguments['recursive'] : $recursive;
		$filterExtensions = trim((string) $this->arguments['filterExtensions']);
		$start = (integer) $this->arguments['start'];
		$numberOfItems = (integer) $this->arguments['numberOfItems'];

		if (TRUE === empty($identifier)) {
			return NULL;
		}

		$files = array();
		$resourceFactory = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\ResourceFactory');

		if (FALSE === empty($filterExtensions)) {
			$this->filter = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\Filter\\FileExtensionFilter');
			$this->filter->setAllowedFileExtensions($filterExtensions);
			$filter = array(
				array(
					$this->filter,
					'filterFileList',
				)
			);
		}

		foreach ($identifier as $i) {
			try {
				/** @var Folder $folder */
				$folder = $resourceFactory->retrieveFileOrFolderObject($i);
				if (NULL !== $this->filter) {
					$folder->setFileAndFolderNameFilters($filter);
				}

				if (TRUE === $onlyProperties) {
					$files = array_merge($files, self::getFilesOfFolderAsArray($folder, $start, $numberOfItems, $recursive));
				} else {
					$files = array_merge($files, $folder->getFiles($start, $numberOfItems, Folder::FILTER_MODE_USE_OWN_AND_STORAGE_FILTERS, $recursive));
				}
			} catch (\Exception $e) {
				continue;
			}
		}

		return $files;
	}

	/**
	 * Get all Files from the $folder and convert each file object to an array.
	 *
	 * @param Folder $folder
	 * @param int    $start
	 * @param int    $numberOfItems
	 * @param bool   $recursive
	 *
	 * @return array
	 */
	public static function getFilesOfFolderAsArray(Folder $folder, $start = 0, $numberOfItems = 0, $recursive = FALSE) {
		$array = array();
		$files = $folder->getFiles($start, $numberOfItems, Folder::FILTER_MODE_USE_OWN_AND_STORAGE_FILTERS, $recursive);
		foreach ($files as $file) {
			$array[] = ResourceUtility::getFileArray($file);
		}

		return $array;
	}
}
