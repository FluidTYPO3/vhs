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

	/**
	 * Render method
	 *
	 * @param string $pattern
	 * @param string $string
	 * @param boolean $global
	 * @return string
	 */
	public function render($pattern, $string, $global = FALSE) {
		$matches = array();
		if (TRUE === (boolean) $global) {
			preg_match_all($pattern, $string, $matches, PREG_SET_ORDER);
		} else {
			preg_match($pattern, $string, $matches);
		}
		if (0 < count($matches)) {
			$content = $this->renderThenChild();
		} else {
			$content = $this->renderElseChild();
		}
		return $this->renderChildrenWithVariableOrReturnInput($content);
	}

}
