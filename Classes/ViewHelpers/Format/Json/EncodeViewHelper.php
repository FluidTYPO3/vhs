<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
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
 * Returns a string containing the JSON representation of the argument
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Format\Json
 */
class Tx_Vhs_ViewHelpers_Format_Json_EncodeViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var array
	 */
	protected $encounteredClasses = array();

	/**
	 * @param mixed $value Array or Traversable
	 * @param boolean $useTraversableKeys If TRUE, preserves keys from Traversables converted to arrays. Not recommended for ObjectStorages!
	 * @param boolean $preventRecursion If FALSE, allows recursion to occur which could potentially be fatal to the output unless managed
	 * @param mixed $recursionMarker Any value - string, integer, boolean, object or NULL - inserted instead of recursive instances of objects
	 * @throws Tx_Fluid_Core_ViewHelper_Exception
	 * @return string
	 */
	public function render($value = NULL, $useTraversableKeys = FALSE, $preventRecursion = TRUE, $recursionMarker = NULL) {
		if (NULL === $value) {
			$value = $this->renderChildren();
			if (NULL === $value) {
				return '{}';
			}
		}

		if (TRUE === $value instanceof Traversable) {
				// Note: also converts Extbase ObjectStorage to Tx_Extkey_Domain_Model_ObjectType[] which are later each converted
			$value = iterator_to_array($value, $useTraversableKeys);
		} elseif (TRUE === $value instanceof Tx_Extbase_DomainObject_DomainObjectInterface) {
				// Convert to associative array,
			$value = $this->recursiveDomainObjectToArray($value, $preventRecursion, $recursionMarker);
		}

		if (TRUE === is_array($value)) {
			$value = $this->recursiveArrayOfDomainObjectsToArray($value, $preventRecursion, $recursionMarker);
		}

		$json = json_encode($value);

		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new Tx_Fluid_Core_ViewHelper_Exception('The provided argument cannot be converted into JSON.', 1358440181); 
		}

		return $json;
	}

	/**
	 * Convert an array of possible DomainObject instances. The argument
	 * $possibleDomainObjects could also an associative array representation
	 * of another DomainObject - which means each value could potentially
	 * be another DomainObject, an ObjectStorage of DomainObjects or a simple
	 * value type. The type is checked and another recursive call is used to
	 * convert any nested objects.
	 *
	 * @param Tx_Extbase_DomainObject_DomainObjectInterface[] $domainObjects
	 * @param boolean $preventRecursion
	 * @param mixed $recursionMarker
	 * @return array
	 */
	protected function recursiveArrayOfDomainObjectsToArray(array $domainObjects, $preventRecursion, $recursionMarker) {
		foreach ($domainObjects as $key => $possibleDomainObject) {
			if (TRUE === $possibleDomainObject instanceof Tx_Extbase_DomainObject_DomainObjectInterface) {
				$domainObjects[$key] = $this->recursiveDomainObjectToArray($possibleDomainObject, $preventRecursion, $recursionMarker);
			} elseif (TRUE === $possibleDomainObject instanceof Traversable) {
				$traversableAsArray = iterator_to_array($possibleDomainObject);
				$domainObjects[$key] = $this->recursiveArrayOfDomainObjectsToArray($traversableAsArray, $preventRecursion, $recursionMarker);
			}
		}
		return $domainObjects;
	}

	/**
	 * Convert a single DomainObject instance first to an array, then pass
	 * that array through recursive DomainObject detection. This will convert
	 * any 1:1, 1:n, n:1 and m:n relations.
	 *
	 * @param Tx_Extbase_DomainObject_DomainObjectInterface $domainObject
	 * @param boolean $preventRecursion
	 * @param mixed $recursionMarker
	 * @return array
	 */
	protected function recursiveDomainObjectToArray(Tx_Extbase_DomainObject_DomainObjectInterface $domainObject, $preventRecursion, $recursionMarker) {
		$hash = spl_object_hash($domainObject);
		if (TRUE === ($preventRecursion && in_array($hash, $this->encounteredClasses))) {
			return $recursionMarker;
		}
		$converted = Tx_Extbase_Reflection_ObjectAccess::getGettableProperties($domainObject);
		array_push($this->encounteredClasses, $hash);
		$converted = $this->recursiveArrayOfDomainObjectsToArray($converted, $preventRecursion, $recursionMarker);
		return $converted;
	}

}