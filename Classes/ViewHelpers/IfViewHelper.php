<?php
namespace FluidTYPO3\Vhs\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\BooleanNode;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * If (condition) ViewHelper supporting a stack of conditions
 * instead of a single condition.
 *
 * @deprecated
 */
class IfViewHelper extends AbstractConditionViewHelper
{
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
    static protected $comparisonOperators = [
        self::OPERATOR_IS_EQUAL => self::OPERATOR_IS_EQUAL,
        self::OPERATOR_IS_NOT_EQUAL => self::OPERATOR_IS_NOT_EQUAL,
        self::OPERATOR_IS_GREATER_OR_EQUAL => self::OPERATOR_IS_GREATER_OR_EQUAL,
        self::OPERATOR_IS_SMALLER_OR_EQUAL => self::OPERATOR_IS_SMALLER_OR_EQUAL,
        self::OPERATOR_IS_SMALLER => self::OPERATOR_IS_SMALLER,
        self::OPERATOR_IS_GREATER => self::OPERATOR_IS_GREATER
    ];

    /**
     * @var array
     */
    static protected $logicalOperators = [
        self::OPERATOR_LOGICAL_AND => self::OPERATOR_LOGICAL_AND,
        self::OPERATOR_LOGICAL_OR => self::OPERATOR_LOGICAL_OR,
        self::OPERATOR_BOOLEAN_AND => self::OPERATOR_LOGICAL_AND,
        self::OPERATOR_BOOLEAN_OR => self::OPERATOR_LOGICAL_OR
    ];

    /**
     * Lower value means less precedence
     *
     * @var array
     */
    static protected $operatorPrecedence = [
        self::OPERATOR_LOGICAL_OR => 0,
        self::OPERATOR_LOGICAL_AND => 1
    ];

    /**
     * Initialize
     *
     * @return void
     * @api
     */
    public function initializeArguments()
    {
        GeneralUtility::deprecationLog(static::class . ' is deprecated, will be removed when VHS supports TYPO3v8 LTS as minimum');
        self::registerArgument('stack', 'array', 'The stack to be evaluated', true);
    }

    /**
     * This method decides if the condition is TRUE or FALSE. It can be overriden in extending
     * viewhelpers to adjust functionality.
     *
     * @param array $arguments ViewHelper arguments to evaluate the condition for this ViewHelper, allows for
     *                         flexiblity in overriding this method.
     * @return bool
     */
    protected static function evaluateCondition($arguments = null)
    {
        return true === self::evaluateStack($arguments['stack']);
    }

    /**
     * @throws \RuntimeException
     * @param array $stack
     * @return boolean
     */
    protected static function evaluateStack(array $stack)
    {
        $stackCount = count($stack);

        if (0 === $stackCount) {
            return false;
        } elseif (1 === $stackCount) {
            return BooleanNode::convertToBoolean(reset($stack));
        } elseif (3 === $stackCount) {
            list ($leftSide, $operator, $rightSide) = array_values($stack);
            if (true === is_string($operator) && true === isset(self::$comparisonOperators[$operator])) {
                $operator = self::$comparisonOperators[$operator];
                return BooleanNode::evaluateComparator($operator, $leftSide, $rightSide);
            }
        }

        $operator = false;
        $operatorPrecedence = PHP_INT_MAX;
        $operatorIndex = false;

        foreach ($stack as $index => $element) {
            if (true === is_string($element) && true === isset(self::$logicalOperators[$element])) {
                $currentOperator = self::$logicalOperators[$element];
                $currentOperatorPrecedence = self::$operatorPrecedence[$currentOperator];
                if ($currentOperatorPrecedence <= $operatorPrecedence) {
                    $operator = $currentOperator;
                    $operatorPrecedence = $currentOperatorPrecedence;
                    $operatorIndex = $index;
                }
            }
        }

        if (false === $operator) {
            throw new \RuntimeException(
                'The stack was not comparable and did not include any logical operators.',
                1385071197
            );
        }

        $operatorIndex = array_search($operatorIndex, array_keys($stack));
        if (0 === $operatorIndex || $operatorIndex + 1 >= $stackCount) {
            throw new \RuntimeException(
                'The stack may not contain a logical operator at the first or last element.',
                1385072228
            );
        }

        $leftSide = array_slice($stack, 0, $operatorIndex);
        $rightSide = array_slice($stack, $operatorIndex + 1);

        return self::evaluateLogicalOperator($leftSide, $operator, $rightSide);
    }

    /**
     * @throws \RuntimeException
     * @param array $leftSide
     * @param string $operator
     * @param array $rightSide
     * @return boolean
     */
    protected static function evaluateLogicalOperator(array $leftSide, $operator, array $rightSide)
    {
        $leftCondition = self::evaluateStack(self::prepareSideForEvaluation($leftSide));
        $rightCondition = self::evaluateStack(self::prepareSideForEvaluation($rightSide));

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
    protected static function prepareSideForEvaluation(array $side)
    {
        if (1 === count($side)) {
            $element = reset($side);
            if (true === is_array($element)) {
                return $element;
            }
        }
        return $side;
    }
}
