<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
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
abstract class Tx_Vhs_ViewHelpers_Resource_AbstractResourceViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractTagBasedViewHelper {

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		$this->registerArgument('identifier', 'mixed', 'The FAL sys_file record identifiers (either CSV, array or implementing Traversable).', FALSE, NULL);
	}

	/**
	 * Returns the files
	 *
	 * @param boolean $onlyProperties
	 * @param mixed $identifier
	 * @return array|NULL
	 */
	public function getFiles($onlyProperties = FALSE, $identifier = NULL) {
		if (NULL === $identifier) {
			$identifier = $this->arguments['identifier'];
		}

		if (TRUE === $identifier instanceof Traversable) {
			$identifier = iterator_to_array($identifier);
		} elseif (TRUE === is_string($identifier)) {
			$identifier = t3lib_div::trimExplode(',', $identifier, TRUE);
		} else {
			$identifier = (array) $identifier;
		}

		if (TRUE === empty($identifier)) {
			 return NULL;
		}

		$files = array();

		$resourceFactory = t3lib_div::makeInstance('TYPO3\\CMS\\Core\\Resource\\ResourceFactory');

		foreach ($identifier as $i) {
			try {
				$file = $resourceFactory->getFileObjectFromCombinedIdentifier($i);
				if (TRUE === $onlyProperties) {
					$file = $file->getProperties();
				}
				$files[] = $file;
			} catch (Exception $e) {
				continue;
			}
		}

		return $files;
	}

}
