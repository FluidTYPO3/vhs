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

/**
 * Gets values from an iterator, removing current keys (if any exist)
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class ValuesViewHelper extends AbstractViewHelper {

	/**
	 * Render method
	 *
	 * @param mixed $subject
	 * @throws \Exception
	 * @return array
	 */
	public function render($subject = NULL) {
		$as = $this->arguments['as'];
		if (NULL === $subject) {
			$subject = $this->renderChildren();
		}
		if (TRUE === $subject instanceof \Iterator) {
			$subject = iterator_to_array($subject, TRUE);
		} elseif (FALSE === is_array($subject)) {
			throw new \Exception('Cannot get values of unsupported type: ' . gettype($subject), 1357098192);
		}
		$output = array_values($subject);
		if (NULL !== $as) {
			$variables = array($as => $output);
			$output = ViewHelperUtility::renderChildrenWithVariables($this, $this->templateVariableContainer, $variables);
		}
		return $output;
	}

}
