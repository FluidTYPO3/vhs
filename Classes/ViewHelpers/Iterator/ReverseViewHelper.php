<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Claus Due <claus@wildside.dk>, Wildside A/S
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
 ***************************************************************/

/**
 * ### Iterator Reversal ViewHelper
 *
 * Reverses the order of every member of an Iterator/Array,
 * preserving the original keys.
 *
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class Tx_Vhs_ViewHelpers_Iterator_ReverseViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Initialize arguments
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('as', 'string', 'Which variable to update in the TemplateVariableContainer. If left out, returns reversed data instead of updating the variable (i.e. reference or copy)');
	}

	/**
	 * "Render" method - sorts a target list-type target. Either $array or
	 * $objectStorage must be specified. If both are, ObjectStorage takes precedence.
	 *
	 * Returns the same type as $subject. Ignores NULL values which would be
	 * OK to use in an f:for (empty loop as result)
	 *
	 * @param array|Iterator $subject An array or Iterator implementation to sort
	 * @throws Exception
	 * @return mixed
	 */
	public function render($subject = NULL) {
		$as = $this->arguments['as'];
		if ($subject === NULL && !$as) {
				// this case enables inline usage if the "as" argument
				// is not provided. If "as" is provided, the tag content
				// (which is where inline arguments are taken from) is
				// expected to contain the rendering rather than the variable.
			$subject = $this->renderChildren();
		}
		$array = NULL;
		if (is_array($subject) === TRUE) {
			$array = $subject;
		} else {
			if ($subject instanceof Iterator) {
				/** @var Iterator $subject */
				$array = iterator_to_array($subject, TRUE);
			} elseif ($subject instanceof Tx_Extbase_Persistence_QueryResultInterface) {
				/** @var Tx_Extbase_Persistence_QueryResultInterface $subject */
				$array = $subject->toArray();
			} elseif ($subject !== NULL) {
					// a NULL value is respected and ignored, but any
					// unrecognized value other than this is considered a
					// fatal error.
				throw new Exception('Invalid variable type passed to Iterator/ReverseViewHelper. Expected any of Array, QueryResult, ' .
					' ObjectStorage or Iterator implementation but got ' . gettype($subject), 1351958941);
			}
		}
		$array = array_reverse($array, TRUE);
		if (NULL !== $as) {
			$variables = array($as => $array);
			$content = Tx_Vhs_Utility_ViewHelperUtility::renderChildrenWithVariables($this, $this->templateVariableContainer, $variables);
			return $content;
		}
		return $array;
	}

	/**
	 * Sort an array
	 *
	 * @param array $array
	 * @return array
	 */
	protected function sortArray($array) {
		$sorted = array();
		foreach ($array as $index => $object) {
			if ($this->arguments['sortBy']) {
				$index = $this->getSortValue($object);
			}
			while (isset($sorted[$index])) {
				$index .= '1';
			}
			$sorted[$index] = $object;
		}
		if ($this->arguments['order'] === 'ASC') {
			ksort($sorted, constant($this->arguments['sortFlags']));
		} elseif ($this->arguments['order'] === 'RAND') {
			$sortedKeys = array_keys($sorted);
			shuffle($sortedKeys);
			$backup = $sorted;
			$sorted = array();
			foreach ($sortedKeys as $sortedKey) {
				$sorted[$sortedKey] = $backup[$sortedKey];
			}
		} elseif ($this->arguments['order'] === 'SHUFFLE') {
			shuffle($sorted);
		} else {
			krsort($sorted, constant($this->arguments['sortFlags']));
		}
		return $sorted;
	}

	/**
	 * Sort a Tx_Extbase_Persistence_ObjectStorage instance
	 *
	 * @param Tx_Extbase_Persistence_ObjectStorage $storage
	 * @return Tx_Extbase_Persistence_ObjectStorage
	 */
	protected function sortObjectStorage($storage) {
		/** @var Tx_Extbase_Object_ObjectManager $objectManager */
		$objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		/** @var Tx_Extbase_Persistence_ObjectStorage $temp */
		$temp = $objectManager->get('Tx_Extbase_Persistence_ObjectStorage');
		foreach ($storage as $item) {
			$temp->attach($item);
		}
		$sorted = array();
		foreach ($storage as $index => $item) {
			if ($this->arguments['sortBy']) {
				$index = $this->getSortValue($item);
			}
			while (isset($sorted[$index])) {
				$index .= '1';
			}
			$sorted[$index] = $item;
		}
		if ($this->arguments['order'] === 'ASC') {
			ksort($sorted, constant($this->arguments['sortFlags']));
		} elseif ($this->arguments['order'] === 'RAND') {
			$sortedKeys = array_keys($sorted);
			shuffle($sortedKeys);
			$backup = $sorted;
			$sorted = array();
			foreach ($sortedKeys as $sortedKey) {
				$sorted[$sortedKey] = $backup[$sortedKey];
			}
		} elseif ($this->arguments['order'] === 'SHUFFLE') {
			shuffle($sorted);
		} else {
			krsort($sorted, constant($this->arguments['sortFlags']));
		}
		$storage = $objectManager->get('Tx_Extbase_Persistence_ObjectStorage');
		foreach ($sorted as $item) {
			$storage->attach($item);
		}
		return $storage;
	}

	/**
	 * Gets the value to use as sorting value from $object
	 *
	 * @param mixed $object
	 * @return mixed
	 */
	protected function getSortValue($object) {
		$field = $this->arguments['sortBy'];
		$value = Tx_Extbase_Reflection_ObjectAccess::getProperty($object, $field);
		if ($value instanceof DateTime) {
			$value = $value->format('U');
		} elseif ($value instanceof Tx_Extbase_Persistence_ObjectStorage) {
			$value = $value->count();
		} elseif (is_array($value)) {
			$value = count($value);
		}
		return $value;
	}
}
