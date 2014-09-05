<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

/**
 * Base class for resource related view helpers
 *
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Resource
 */
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use FluidTYPO3\Vhs\Utility\ResourceUtility;

abstract class AbstractResourceViewHelper extends AbstractTagBasedViewHelper {

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		$this->registerArgument('identifier', 'mixed', 'The FAL combined identifiers (either CSV, array or implementing Traversable).', FALSE, NULL);
		$this->registerArgument('categories', 'mixed', 'The sys_category records to select the resources from (either CSV, array or implementing Traversable).', FALSE, NULL);
		$this->registerArgument('treatIdAsUid', 'boolean', 'If TRUE, the identifier argument is treated as resource uids.', FALSE, FALSE);
		$this->registerArgument('treatIdAsReference', 'boolean', 'If TRUE, the identifier argument is treated as reference uids and will be resolved to resources via sys_file_reference.', FALSE, FALSE);
	}

	/**
	 * Returns the files
	 *
	 * @param boolean $onlyProperties
	 * @param mixed $identifier
	 * @return array|NULL
	 */
	public function getFiles($onlyProperties = FALSE, $identifier = NULL, $categories = NULL) {
		$identifier = $this->arrayForMixedArgument($identifier, 'identifier');
		$categories = $this->arrayForMixedArgument($categories, 'categories');
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
	 * Mixed argument with CSV, array, Traversable
	 *
	 * @param mixed $argument
	 * @param string $name
	 * @return array
	 */
	public function arrayForMixedArgument($argument, $name) {
		if (NULL === $argument) {
			$argument = $this->arguments[$name];
		}

		if (TRUE === $argument instanceof \Traversable) {
			$argument = iterator_to_array($argument);
		} elseif (TRUE === is_string($argument)) {
			$argument = GeneralUtility::trimExplode(',', $argument, TRUE);
		} else {
			$argument = (array) $argument;
		}

		return $argument;
	}

}
