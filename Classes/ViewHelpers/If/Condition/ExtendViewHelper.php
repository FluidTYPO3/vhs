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
 * ### Condition: Extended
 *
 * Uses advanced ChildNodeAccessor approach to enable
 * very complex conditions supporting AND/OR and parenthesis
 * syntax elements as well as array and object comparison.
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
<<<<<<< HEAD
 * @deprecated
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
=======
 * @author Claus Due <claus@namelesscoder.net>
>>>>>>> [TASK] Happy new year!
 * @package Vhs
 * @subpackage ViewHelpers\If\Condition
 */
class Tx_Vhs_ViewHelpers_If_Condition_ExtendViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper implements Tx_Fluid_Core_ViewHelper_Facets_ChildNodeAccessInterface {

	/**
	 * @var array
	 */
	protected $comparators = array('<', '>', '==', '>=', '<=', '!=');

	/**
	 * @var array
	 */
	protected $operators = array('AND', 'OR', '&&', '||');

	/**
	 * @var array
	 */
	protected $stack = array();

	/**
	 * Render method
	 *
	 * @return boolean
	 */
	public function render() {
		$compoundedStack = $this->compoundStack($this->stack);
		$evaluation = $this->evaluateStack($compoundedStack);
		return Tx_Fluid_Core_Parser_SyntaxTree_BooleanNode::convertToBoolean($evaluation);
	}

	/**
	 * @param array $stack
	 * @return boolean
	 */
	protected function compoundStack($stack) {
		// compound referenced sub-stacks
		foreach ($stack as $stackId => $stackItem) {
			foreach ($stackItem as $stackItemIndex => $item) {
				if (is_string($item) && strlen($item) === 23) {
					$stack[$stackId][$stackItemIndex] = $stack[$item];
					unset($stack[$item]);
				}

			}
		}
		// compound unnecessarily nested sub-stacks
		foreach ($stack as $stackId => $stackItem) {
			if (is_array($stackItem) === TRUE && count($stackItem) === 1) {
				$stack[$stackId] = $stackItem;
			}
		}
		return $stack;
	}

	/**
	 * @param array $stack
	 * @return boolean
	 */
	protected function evaluateStack($stack) {
		$evaluation = FALSE;
		$leftSide = $comparator = $rightSide = $conjunction = NULL;
		if (count($stack) === 0) {
			return FALSE;
		} elseif (count($stack) === 3) {
			list ($leftSide, $comparator, $rightSide) = array_values($stack);
			if (is_array($leftSide) === FALSE && is_array($comparator) === FALSE && is_array($rightSide) === FALSE) {
				return Tx_Fluid_Core_Parser_SyntaxTree_BooleanNode::evaluateComparator($comparator, $leftSide, $rightSide);
			} else {
				$leftSide = $comparator = $rightSide = NULL;
			}
		}
		foreach ($stack as $subject) {
			if (is_array($subject) === TRUE) {
				$partialEvaluation = $this->evaluateStack($subject);
				if ($conjunction === NULL) {
					$evaluation = $partialEvaluation;
				} else {
					$evaluation = $this->compareConjunction($evaluation, $conjunction, $partialEvaluation);
					$conjunction = NULL;
				}
			} elseif ($leftSide && $comparator && $rightSide) {
				$partialEvaluation = Tx_Fluid_Core_Parser_SyntaxTree_BooleanNode::evaluateComparator($comparator, $leftSide, $rightSide);
				if ($conjunction === NULL) {
					$evaluation = $partialEvaluation;
				} else {
					$evaluation = $this->compareConjunction($evaluation, $conjunction, $partialEvaluation);
					$conjunction = NULL;
				}
			} elseif ($leftSide && $comparator && $rightSide && $conjunction === NULL) {
				$conjunction = $subject;
				$leftSide = $comparator = $rightSide = NULL;
			} elseif ($leftSide === NULL) {
				$leftSide = $subject;
			} elseif ($comparator === NULL) {
				$comparator = $subject;
			} elseif ($rightSide === NULL) {
				$rightSide = $subject;
			}
		}
		return $evaluation;
	}

	/**
	 * @param mixed $a
	 * @param string $conjunction
	 * @param mixed $b
	 * @return boolean
	 */
	protected function compareConjunction($a, $conjunction, $b) {
		switch ($conjunction) {
			case 'AND':
			case 'and':
			case '&&':
				return ($this->extractValue($a) && $this->extractValue($b));
			case 'OR':
			case 'or':
			case '||':
				return ($this->extractValue($a) || $this->extractValue($b));
			default:
				break;
		}
		return FALSE;
	}

	/**
	 * @param mixed $subject
	 * @return mixed
	 */
	protected function extractValue($subject) {
		if ($subject instanceof Tx_Fluid_Core_Parser_SyntaxTree_ViewHelperNode) {
			$subject = $subject->evaluate($this->renderingContext);
		}
		return $subject;
	}

	/**
	 * Sets the direct child nodes of the current syntax tree node.
	 *
	 * Will loop through every child node, extracting information about
	 * nested evaluations. Places each node in the according stack level.
	 *
	 * Each stack level increase causes a new stack ID to be generated
	 * and used in the following loops. When building the evaluation
	 * stacks this stack ID is used to finally compound the stacks into
	 * a consequetive list of hierarchal stacks which can then be evaluated
	 * by recursively inspecting each stack to accumulate a bool verdict.
	 *
	 * @param Tx_Fluid_Core_Parser_SyntaxTree_AbstractNode[] $childNodes
	 * @throws Exception
	 * @return void
	 */
	public function setChildNodes(array $childNodes) {
		$stackLevel = 0;
		$idStack = array();
		$parentStack = NULL;
		foreach ($childNodes as $node) {
			end($idStack);
			$stackId = $idStack[key($idStack)];
			$stackItem = array();
			if ($node instanceof Tx_Fluid_Core_Parser_SyntaxTree_TextNode) {
				$nodeContent = $node->evaluate($this->renderingContext);
				$nodeContent = trim($nodeContent);
				if (strpos($nodeContent, '(') === FALSE && strpos($nodeContent, ')') === FALSE) {
					// will match any operator or comparator, inserting it in the stack
					// will fail with an Exception if encountering an unrecognized string
					// that is not a comparator or operator.
					$stackItem = $nodeContent;
				} else {
					// match all delimiters, operators and comparators into one big
					// sequence of syntactical components.
					$matches = array();
					$matched = preg_match_all('/\(|\)|AND|OR|\&\&|\|\||NULL|FALSE|TRUE|[0-9]+/', $nodeContent, $matches);
					if ($matched) {
						foreach ($matches[0] as $syntacticalComponent) {
							if ($syntacticalComponent === '(') {
								$stackLevel ++;
								$parentStack = $stackId;
								array_push($idStack, uniqid('', TRUE));
							} elseif ($syntacticalComponent === ')') {
								$stackLevel --;
								$parentStack = $stackLevel <= 0 ? NULL : $parentStack;
								$stackId = array_pop($idStack);
							} else {
								if ($parentStack && isset($this->stack[$parentStack]) === FALSE) {
									$this->stack[$parentStack] = array();
								}
								$this->addToStack($syntacticalComponent, uniqid('', TRUE), $parentStack);
							}
						}

					}
				}
			} elseif ($node instanceof Tx_Fluid_Core_Parser_SyntaxTree_BooleanNode) {
				// BooleanNode gets added onto the stack to be evaluated later
				$stackItem = $node->evaluate($this->renderingContext);
			} elseif ($node instanceof Tx_Fluid_Core_Parser_SyntaxTree_ViewHelperNode) {
				// ViewHelperNode gets added onto the stack to be evaluated later
				$stackItem = $node->evaluate($this->renderingContext);
			}
			$this->addToStack($stackItem, $stackId, $parentStack);
		}
		if ($stackLevel !== 0) {
			throw new Exception(
				'Syntax error in expression, Extended Condition ViewHelper. Parenthesis count mismatch. Difference: ' . strval($stackLevel),
				1352653464
			);
		}
	}

	/**
	 * @param mixed $item
	 * @param string $stackId
	 * @param string $parentStack
	 * @return void
	 */
	protected function addToStack($item, $stackId, $parentStack) {
		if ((is_array($item) && count($item) > 0) || !is_array($item)) {
			if (isset($this->stack[$stackId]) === FALSE) {
				$this->stack[$stackId] = array();
			}
			array_push($this->stack[$stackId], $item);
		}
		if ($parentStack !== NULL) {
			if (in_array($stackId, $this->stack[$parentStack]) === FALSE) {
				array_push($this->stack[$parentStack], $stackId);
			}
		}
	}
}
