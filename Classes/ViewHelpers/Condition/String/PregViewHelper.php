<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\String;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;
use FluidTYPO3\Vhs\Traits\ConditionViewHelperTrait;

/**
 * ### Condition: String matches regular expression
 *
 * Condition ViewHelper which renders the `then` child if provided
 * string matches provided regular expression. $matches array containing
 * the results can be made available by providing a template variable
 * name with argument $as. Resulting matches are assigned to the template
 * through the variable $matches.
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Condition\String
 */
class PregViewHelper extends AbstractConditionViewHelper {

	use TemplateVariableViewHelperTrait;
	use ConditionViewHelperTrait;

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('pattern', 'string', 'regex pattern to match string against', TRUE);
		$this->registerArgument('string', 'string', 'string to match with the regex pattern', TRUE);
		$this->registerArgument('global', 'boolean', 'match global', FALSE, FALSE);
		$this->registerAsArgument();
	}

	/**
	 * This method decides if the condition is TRUE or FALSE. It can be overriden in extending viewhelpers to adjust functionality.
	 *
	 * @param array $matches
	 * @return boolean
	 */
	static protected function evaluateCondition($matches = array()) {
		return 0 < count($matches);
	}

	/**
	 * Evaluates the regular expression and returns resulting matches
	 *
	 * @param array $arguments
	 * @return array
	 */
	static protected function evaluateExpression($arguments = NULL) {
		$matches = array();
		if (TRUE === (boolean) $arguments['global']) {
			preg_match_all($arguments['pattern'], $arguments['string'], $matches, PREG_SET_ORDER);
		} else {
			preg_match($arguments['pattern'], $arguments['string'], $matches);
		}
		return $matches;
	}

	/**
	 * renders <f:then> child if $condition is true, otherwise renders <f:else> child.
	 *
	 * @return string the rendered string
	 * @api
	 */
	public function render() {
		$matches = static::evaluateExpression($this->arguments);
		if (static::evaluateCondition($matches)) {
			$content = $this->renderThenChild();
		} else {
			$content = $this->renderElseChild();
		}
		return $this->renderChildrenWithVariableOrReturnInput($content, array('matches' => $matches));
	}

	/**
	 * Default implementation for use in compiled templates
	 *
	 * TODO: remove at some point, because this is only here for legacy reasons.
	 * the AbstractConditionViewHelper in 6.2.* doesn't have a default render
	 * method. 7.2+ on the other hand provides basically exactly this method here
	 * luckily it's backwards compatible out of the box.
	 * tl;dr -> remove after expiration of support for anything below 7.2
	 *
	 * @param array $arguments
	 * @param \Closure $renderChildrenClosure
	 * @param \TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
	 * @return mixed
	 */
	static public function renderStatic(array $arguments, \Closure $renderChildrenClosure, \TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
		$hasEvaluated = TRUE;
		$content = '';
		$matches = static::evaluateExpression($arguments);
		if (static::evaluateCondition($matches)) {
			$result = static::renderStaticThenChild($arguments, $hasEvaluated);
			if ($hasEvaluated) {
				$content = $result;
			}
		} else {
			$result = static::renderStaticElseChild($arguments, $hasEvaluated);
			if ($hasEvaluated) {
				$content = $result;
			}
		}
		return self::renderChildrenWithVariableOrReturnInputStatic($content, $arguments['as'], $renderingContext, $renderChildrenClosure, array('matches' => $matches));
	}

}
