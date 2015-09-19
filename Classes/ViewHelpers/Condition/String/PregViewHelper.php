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
 * name with argument $as.
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
	}

	/**
	 * This method decides if the condition is TRUE or FALSE. It can be overriden in extending viewhelpers to adjust functionality.
	 *
	 * @param array $arguments ViewHelper arguments to evaluate the condition for this ViewHelper, allows for flexiblity in overriding this method.
	 * @return bool
	 */
	static protected function evaluateCondition($arguments = NULL) {
		$matches = array();
		if (TRUE === (boolean) $arguments['global']) {
			preg_match_all($arguments['pattern'], $arguments['string'], $matches, PREG_SET_ORDER);
		} else {
			preg_match($arguments['pattern'], $arguments['string'], $matches);
		}
		return 0 < count($matches);
	}

	/**
	 * renders <f:then> child if $condition is true, otherwise renders <f:else> child.
	 *
	 * @return string the rendered string
	 * @api
	 */
	public function render() {
		if (static::evaluateCondition($this->arguments)) {
			$content = $this->renderThenChild();
		} else {
			$content = $this->renderElseChild();
		}
		return $this->renderChildrenWithVariableOrReturnInput($content);
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
		if (static::evaluateCondition($arguments)) {
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
		return self::renderChildrenWithVariableOrReturnInputStatic($content, $arguments['as'], $renderingContext, $renderChildrenClosure);
	}

}
