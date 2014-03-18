<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
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
 * ### Deprecation Notice
 *
 * Please switch to ``v:if``. This view helper will be removed in 2.0.
 *
 * ### Condition ViewHelper
 *
 * Extended condition ViewHelper. Works slightly different from
 * `f:if` by not rendering the child tag content if the "then"
 * condition is encountered - allowing this usage:
 *
 * ### Example
 *
 *     <v:if.condition>
 *         <v:if.condition.extend>
 *             ({a} == {b} && {c} != NULL) OR {a} == NULL
 *         </v:if.condition.extend>
 *         This text is completely ignored; only text in f:then is echoed
 *         <f:then>
 * 	          Output if TRUE
 * 	       </f:then>
 *     </v:if.condition>
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\If
 */
class Tx_Vhs_ViewHelpers_If_ConditionViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper implements Tx_Fluid_Core_ViewHelper_Facets_ChildNodeAccessInterface {

	/**
	 * An array of Tx_Fluid_Core_Parser_SyntaxTree_AbstractNode
	 * @var Tx_Fluid_Core_Parser_SyntaxTree_AbstractNode[]
	 */
	private $childNodes = array();

	/**
	 * Setter for ChildNodes - as defined in ChildNodeAccessInterface
	 *
	 * @param array $childNodes Child nodes of this syntax tree node
	 * @return void
	 */
	public function setChildNodes(array $childNodes) {
		$this->childNodes = $childNodes;
	}

	/**
	 * Returns value of "then" attribute.
	 * If then attribute is not set, iterates through child nodes and renders ThenViewHelper.
	 * If then attribute is not set and no ThenViewHelper and no ElseViewHelper is found, all child nodes are rendered
	 *
	 * @return string rendered ThenViewHelper or contents of <f:if> if no ThenViewHelper was found
	 * @api
	 */
	protected function renderThenChild() {
		if (FALSE === empty($this->arguments['then'])) {
			return $this->arguments['then'];
		}
		if (FALSE === empty($this->arguments['__thenClosure'])) {
			$thenClosure = $this->arguments['__thenClosure'];
			return $thenClosure();
		} elseif (FALSE === empty($this->arguments['__elseClosure']) || FALSE === empty($this->arguments['else'])) {
			return '';
		}
		$elseViewHelperEncountered = FALSE;
		foreach ($this->childNodes as $childNode) {
			if ($childNode instanceof Tx_Fluid_Core_Parser_SyntaxTree_ViewHelperNode
				&& $childNode->getViewHelperClassName() === $this->getThenViewHelperClassName()) {
				$data = $childNode->evaluate($this->fetchRenderingContext());
				return $data;
			}
			if ($childNode instanceof Tx_Fluid_Core_Parser_SyntaxTree_ViewHelperNode
				&& $childNode->getViewHelperClassName() === $this->getElseViewHelperClassName()) {
				$elseViewHelperEncountered = TRUE;
			}
		}

		if ($elseViewHelperEncountered) {
			return '';
		}
		return NULL;
	}

	/**
	 * Returns value of "else" attribute.
	 * If else attribute is not set, iterates through child nodes and renders ElseViewHelper.
	 * If else attribute is not set and no ElseViewHelper is found, an empty string will be returned.
	 *
	 * @return string rendered ElseViewHelper or an empty string if no ThenViewHelper was found
	 * @api
	 */
	protected function renderElseChild() {
		if (FALSE === empty($this->arguments['else'])) {
			return $this->arguments['else'];
		}
		if (FALSE === empty($this->arguments['__elseClosure'])) {
			$elseClosure = $this->arguments['__elseClosure'];
			return $elseClosure();
		}
		foreach ($this->childNodes as $childNode) {
			if ($childNode instanceof Tx_Fluid_Core_Parser_SyntaxTree_ViewHelperNode) {
				if ($childNode->getViewHelperClassName() === $this->getElseViewHelperClassName()) {
					return $childNode->evaluate($this->fetchRenderingContext());
				}
			}
		}

		return '';
	}

	/**
	 * renders <f:then> child if $condition is true, otherwise renders <f:else> child.
	 *
	 * @param boolean $condition View helper condition
	 * @return string the rendered string
	 * @api
	 */
	public function render($condition = NULL) {
		if ($condition === NULL) {
			// search for the v:condition.extend ViewHelper
			foreach ($this->childNodes as $childNode) {
				if ($childNode instanceof Tx_Fluid_Core_Parser_SyntaxTree_ViewHelperNode) {
					$viewHelperClassName = $childNode->getViewHelperClassName();
					if ($viewHelperClassName === 'Tx_Vhs_ViewHelpers_If_Condition_ExtendViewHelper') {
						$condition = $childNode->evaluate($this->fetchRenderingContext());
						break;
					}
				}
			}
		}
		if ($condition) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}

	/**
	 * @return Tx_Fluid_Core_Rendering_RenderingContextInterface
	 */
	protected function fetchRenderingContext() {
		if (TRUE === method_exists($this, 'getRenderingContext')) {
			return call_user_func_array(array($this, 'getRenderingContext'), array());
		}
		return $this->renderingContext;
	}

	/**
	 * @return boolean
	 */
	protected function assertCoreVersionIsAtLeastSixPointZero() {
		$version = explode('.', TYPO3_version);
		return ($version[0] >= 6);
	}

	/**
	 * @return string
	 */
	protected function getThenViewHelperClassName() {
		$viewHelperClassName = TRUE === $this->assertCoreVersionIsAtLeastSixPointZero() ?
			'TYPO3\\CMS\\Fluid\\ViewHelpers\\ThenViewHelper' : 'Tx_Fluid_ViewHelpers_ThenViewHelper';
		return $viewHelperClassName;
	}

	/**
	 * @return string
	 */
	protected function getElseViewHelperClassName() {
		$viewHelperClassName = TRUE === $this->assertCoreVersionIsAtLeastSixPointZero() ?
			'TYPO3\\CMS\\Fluid\\ViewHelpers\\ElseViewHelper' : 'Tx_Fluid_ViewHelpers_ElseViewHelper';
		return $viewHelperClassName;
	}
}
