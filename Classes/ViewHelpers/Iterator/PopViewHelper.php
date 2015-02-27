<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\ViewHelperUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

/**
 * Pops the last value off $subject (but does not change $subject itself as array_pop would)
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class PopViewHelper extends AbstractViewHelper {

	/**
	 * Render method
	 *
	 * @param mixed $subject The subject Traversable/Array instance to pop
	 * @param string $as If specified, inserts a template variable with this name, then renders the child content, then removes the variable
	 * @throws \Exception
	 * @return array
	 */
	public function render($subject = NULL, $as = NULL) {
		if (NULL === $subject) {
			$subject = $this->renderChildren();
		}
		if (TRUE === $subject instanceof \Traversable) {
			$subject = iterator_to_array($subject, TRUE);
		} elseif (FALSE === is_array($subject)) {
			throw new Exception('Cannot get values of unsupported type: ' . gettype($subject), 1357098192);
		}
		$output = array_pop($subject);
		if (NULL !== $as) {
			$variables = array($as => $output);
			$output = ViewHelperUtility::renderChildrenWithVariables($this, $this->templateVariableContainer, $variables);
		}
		return $output;
	}

}
