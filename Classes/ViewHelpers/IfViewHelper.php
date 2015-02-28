<?php
namespace FluidTYPO3\Vhs\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\BooleanNode;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers
 */
class IfViewHelper extends AbstractConditionViewHelper {

	const OPERATOR_IS_EQUAL = '==';
	const OPERATOR_IS_NOT_EQUAL = '!=';
	const OPERATOR_IS_GREATER_OR_EQUAL = '>=';
	const OPERATOR_IS_SMALLER_OR_EQUAL = '<=';
	const OPERATOR_IS_SMALLER = '<';
	const OPERATOR_IS_GREATER = '>';

	const OPERATOR_LOGICAL_AND = 'AND';
	const OPERATOR_LOGICAL_OR = 'OR';
	const OPERATOR_BOOLEAN_AND = '&&';
	const OPERATOR_BOOLEAN_OR = '||';

	/**
	 * @var array
	 */
	protected $comparisonOperators = array(
		self::OPERATOR_IS_EQUAL => self::OPERATOR_IS_EQUAL,
		self::OPERATOR_IS_NOT_EQUAL => self::OPERATOR_IS_NOT_EQUAL,
		self::OPERATOR_IS_GREATER_OR_EQUAL => self::OPERATOR_IS_GREATER_OR_EQUAL,
		self::OPERATOR_IS_SMALLER_OR_EQUAL => self::OPERATOR_IS_SMALLER_OR_EQUAL,
		self::OPERATOR_IS_SMALLER => self::OPERATOR_IS_SMALLER,
		self::OPERATOR_IS_GREATER => self::OPERATOR_IS_GREATER
	);

	/**
	 * @var array
	 */
	protected $logicalOperators = array(
		self::OPERATOR_LOGICAL_AND => self::OPERATOR_LOGICAL_AND,
		self::OPERATOR_LOGICAL_OR => self::OPERATOR_LOGICAL_OR,
		self::OPERATOR_BOOLEAN_AND => self::OPERATOR_LOGICAL_AND,
		self::OPERATOR_BOOLEAN_OR => self::OPERATOR_LOGICAL_OR
	);

	/**
	 * Lower value means less precedence
	 *
	 * @var array
	 */
	protected $operatorPrecedence = array(
		self::OPERATOR_LOGICAL_OR => 0,
		self::OPERATOR_LOGICAL_AND => 1
	);

	/**
	 * Initialize
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		$this->registerArgument('stack', 'array', 'The stack to be evaluated', TRUE);
	}

	/**
	 * @return string
	 * @api
	 */
	public function render() {
		$stack = $this->arguments['stack'];

		if (TRUE === $this->evaluateStack($stack)) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}

	/**
	 * @throws \RuntimeException
	 * @param array $stack
	 * @return boolean
	 */
	protected function evaluateStack(array $stack) {
		$stackCount = count($stack);

		if (0 === $stackCount) {
			return FALSE;
		} elseif (1 === $stackCount) {
			return BooleanNode::convertToBoolean(reset($stack));
		} elseif (3 === $stackCount) {
			list ($leftSide, $operator, $rightSide) = array_values($stack);
			if (TRUE === is_string($operator) && TRUE === isset($this->comparisonOperators[$operator])) {
				$operator = $this->comparisonOperators[$operator];
				return BooleanNode::evaluateComparator($operator, $leftSide, $rightSide);
			}
		}

		$operator = FALSE;
		$operatorPrecedence = PHP_INT_MAX;
		$operatorIndex = FALSE;

		foreach ($stack as $index => $element) {
			if (TRUE === is_string($element) && TRUE === isset($this->logicalOperators[$element])) {
				$currentOperator = $this->logicalOperators[$element];
				$currentOperatorPrecedence = $this->operatorPrecedence[$currentOperator];
				if ($currentOperatorPrecedence <= $operatorPrecedence) {
					$operator = $currentOperator;
					$operatorPrecedence = $currentOperatorPrecedence;
					$operatorIndex = $index;
				}
			}
		}

		if (FALSE === $operator) {
			throw new \RuntimeException('The stack was not comparable and did not include any logical operators.', 1385071197);
		}

		$operatorIndex = array_search($operatorIndex, array_keys($stack));
		if (0 === $operatorIndex || $operatorIndex + 1 >= $stackCount) {
			throw new \RuntimeException('The stack may not contain a logical operator at the first or last element.', 1385072228);
		}

		$leftSide = array_slice($stack, 0, $operatorIndex);
		$rightSide = array_slice($stack, $operatorIndex + 1);

		return $this->evaluateLogicalOperator($leftSide, $operator, $rightSide);
	}

	/**
	 * @throws \RuntimeException
	 * @param array $leftSide
	 * @param string $operator
	 * @param array $rightSide
	 * @return boolean
	 */
	protected function evaluateLogicalOperator(array $leftSide, $operator, array $rightSide) {
		$leftCondition = $this->evaluateStack($this->prepareSideForEvaluation($leftSide));
		$rightCondition = $this->evaluateStack($this->prepareSideForEvaluation($rightSide));

		if (self::OPERATOR_LOGICAL_AND === $operator) {
			return $leftCondition && $rightCondition;
		} elseif (self::OPERATOR_LOGICAL_OR === $operator) {
			return $leftCondition || $rightCondition;
		}

		throw new \RuntimeException('The stack could not be evaluated (internal error).', 1385072357);
	}

	/**
	 * @param array $side
	 * @return array
	 */
	protected function prepareSideForEvaluation(array $side) {
		if (1 === count($side)) {
			$element = reset($side);
			if (TRUE === is_array($element)) {
				return $element;
			}
		}
		return $side;
	}

}
