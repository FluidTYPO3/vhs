<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Andreas Lappe <nd@kaeufli.ch>, kaeufli.ch
 *  
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
 * Loop through the iterator and extract a key, optionally join the
 * results if more than one value is found.
 *
 * = Extract values from an array by key =
 *
 * The extbase version of indexed_search returns an array of the
 * previous search, which cannot easily be shown in the input field
 * of the result page. This can be solved.
 *
 * <code title="Input from extbase version of indexed_search">
 * array(
 *	 0 => array(
 *		 'sword' => 'firstWord',
 *		 'oper' => 'AND'
 *	 ),
 *	 1 => array(
 *		 'sword' => 'secondWord',
 *		 'oper' => 'AND'
 *	 ),
 *	 3 => array(
 *		 'sword' => 'thirdWord',
 *		 'oper' => 'AND'
 *	 )
 * )
 * </code>
 *
 * Show the previous search words in the search form of the
 * result page:
 *
 * <code title="Example">
 * <f:form.textfield name="search[sword]" value="{v:iterator.extract(key:'sword', content: searchWords, glue: ' '}" class="tx-indexedsearch-searchbox-sword" />
 * </code>
 *
 * = Get the names of several users =
 *
 * Provided we have a bunch of FrontendUsers and we need to show
 * their firstname combined into a string:
 *
 * <code title="get the names of several users">
 * <h2>Welcome 
 * <v:iterator.extract key="firstname" content="frontendUsers" glue=", " />
 * </h2>
 * </code>
 *
 * <output>
 * <h2>Welcome Peter, Paul, Marry</h2>
 * </output>
 *
 * = Complex example=
 *
 * <code title="really get dirty with an array">
 * {anArray->v:iterator.extract(path: 'childProperty.secondNestedChildObject')->v:iterator.sort(direction: 'DESC', sortBy: 'propertyOnSecondChild')->v:iterator.slice(length: 10)->v:iterator.extract(key: 'uid')}
 * </code>
 *
 * @author Andreas Lappe <nd@kaeufli.ch>
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class Tx_Vhs_ViewHelpers_Iterator_ExtractViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @param Traversable $content
	 * @param string $key
	 * @param mixed $glue NULL or string like ', '
	 * @param boolean $recursive
	 * @return mixed array or string
	 */
	public function render($content = NULL, $key = NULL, $glue = NULL, $recursive = TRUE) {
		if ($content === NULL ) {
			$content = $this->renderChildren();
		}
		if ($key === NULL) {
			$key = $this->arguments('key');
		}

		try {
			if ($recursive === TRUE) {
				$result = $this->recursivelyExtractKey($content, $key, $glue);
			} else {
				$result = $this->extractByKey($content, $key);
			}
		} catch(Exception $e) {
			// TODO react somehow better than ignoring the Exception
		}

		return $result;
	}

	/**
	 * Extract by key
	 *
	 * @param Traversable $iterator
	 * @param string $key
	 * @return mixed NULL or whatever we found at $key
	 */
	public function extractByKey($iterator, $key) {
		if (! is_array($iterator) && ! $iterator instanceof Traversable) {
			throw new Exception('Traversable object or array expected…');
		}

		$result = Tx_Extbase_Reflection_ObjectAccess::getPropertyPath($iterator, $key);

		return $result;
	}

	/**
	 * Recursively extract the key
	 *
	 * @param Traversable $iterator
	 * @param string $key
	 * @param mixed $glue NULL or string
	 * @return string
	 */
	public function recursivelyExtractKey($iterator, $key, $glue) {
		$content = array();

		foreach ($iterator as $k => $v) {
			// Lets see if we find something directly:
			$result = Tx_Extbase_Reflection_ObjectAccess::getPropertyPath($v, $key);
			if ($result !== NULL) {
				$content[] = $result;
			} else if (is_array($v) || $v instanceof Traversable) {
				$content[] = $this->recursivelyExtractKey($v, $key, $glue);
			}
		}

		if ($glue !== NULL) {
			$content = implode($glue, $content);
		} else {
			$content = $this->flattenArray($content);
		}

		return $content;
	}

	/**
	 * Flatten the result structure, to iterate it cleanly in fluid
	 *
	 * @param array $content
	 * @return array
	 */
	public function flattenArray(array $content, $flattened = NULL) {
		foreach ($content as $sub) {
			if (is_array($sub)) {
				$flattened = $this->flattenArray($sub, $flattened);
			} else {
				$flattened[] = $sub;
			}
		}

		return $flattened;
	}
}
?>