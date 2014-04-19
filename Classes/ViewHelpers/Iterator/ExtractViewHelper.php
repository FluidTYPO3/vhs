<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Andreas Lappe <nd@kaeufli.ch>, kaeufli.ch
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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### Iterator / Extract VieWHelper
 *
 * Loop through the iterator and extract a key, optionally join the
 * results if more than one value is found.
 *
 * #### Extract values from an array by key
 *
 * The extbase version of indexed_search returns an array of the
 * previous search, which cannot easily be shown in the input field
 * of the result page. This can be solved.
 *
 * #### Input from extbase version of indexed_search">
 *
 *     array(
 *           0 => array(
 *             'sword' => 'firstWord',
 *             'oper' => 'AND'
 *         ),
 *         1 => array(
 *             'sword' => 'secondWord',
 *             'oper' => 'AND'
 *         ),
 *         3 => array(
 *             'sword' => 'thirdWord',
 *             'oper' => 'AND'
 *         )
 *     )
 *
 * Show the previous search words in the search form of the
 * result page:
 *
 * #### Example
 *     <f:form.textfield name="search[sword]"
 *         value="{v:iterator.extract(key:'sword', content: searchWords) -> v:iterator.implode(glue: ' ')}"
 *         class="tx-indexedsearch-searchbox-sword" />
 *
 * #### Get the names of several users
 *
 * Provided we have a bunch of FrontendUsers and we need to show
 * their firstname combined into a string:
 *
 *     <h2>Welcome
 *     <v:iterator.implode glue=", "><v:iterator.extract key="firstname" content="frontendUsers" /></v:iterator.implode>
 *     <!-- alternative: -->
 *     {frontendUsers -> v:iterator.extract(key: 'firstname') -> v:iterator.implode(glue: ', ')}
 *     </h2>
 *
 * #### Output
 *
 *     <h2>Welcome Peter, Paul, Marry</h2>
 *
 * #### Complex example
 *
 *     {anArray->v:iterator.extract(path: 'childProperty.secondNestedChildObject')->v:iterator.sort(direction: 'DESC', sortBy: 'propertyOnSecondChild')->v:iterator.slice(length: 10)->v:iterator.extract(key: 'uid')}
 *
 * @author Andreas Lappe <nd@kaeufli.ch>
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class ExtractViewHelper extends AbstractViewHelper {

	/**
	 * @param string $key
	 * @param \Traversable $content
	 * @param boolean $recursive
	 * @return array
	 */
	public function render($key, $content = NULL, $recursive = TRUE) {
		if (NULL === $content) {
			$content = $this->renderChildren();
		}
		try {
			if (TRUE === (boolean) $recursive) {
				$result = $this->recursivelyExtractKey($content, $key);
			} else {
				$result = $this->extractByKey($content, $key);
			}
		} catch (\Exception $error) {
			GeneralUtility::sysLog($error->getMessage(), 'vhs', GeneralUtility::SYSLOG_SEVERITY_WARNING);
			$result = array();
		}

		return $result;
	}

	/**
	 * Extract by key
	 *
	 * @param \Traversable $iterator
	 * @param string $key
	 * @return mixed NULL or whatever we found at $key
	 * @throws \Exception
	 */
	public function extractByKey($iterator, $key) {
		if (FALSE === is_array($iterator) && FALSE === $iterator instanceof \Traversable) {
			throw new \Exception('Traversable object or array expected but received ' . gettype($iterator), 1361532490);
		}

		$result = ObjectAccess::getPropertyPath($iterator, $key);

		return $result;
	}

	/**
	 * Recursively extract the key
	 *
	 * @param \Traversable $iterator
	 * @param string $key
	 * @return string
	 * @throws \Exception
	 */
	public function recursivelyExtractKey($iterator, $key) {
		$content = array();

		foreach ($iterator as $k => $v) {
			// Lets see if we find something directly:
			$result = ObjectAccess::getPropertyPath($v, $key);
			if (NULL !== $result) {
				$content[] = $result;
			} elseif (TRUE === is_array($v) || TRUE === $v instanceof \Traversable) {
				$content[] = $this->recursivelyExtractKey($v, $key);
			}
		}

		$content = $this->flattenArray($content);

		return $content;
	}

	/**
	 * Flatten the result structure, to iterate it cleanly in fluid
	 *
	 * @param array $content
	 * @param array $flattened
	 * @return array
	 */
	public function flattenArray(array $content, $flattened = NULL) {
		foreach ($content as $sub) {
			if (TRUE === is_array($sub)) {
				$flattened = $this->flattenArray($sub, $flattened);
			} else {
				$flattened[] = $sub;
			}
		}

		return $flattened;
	}

}
