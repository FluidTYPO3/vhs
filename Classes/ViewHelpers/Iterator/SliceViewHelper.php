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
 * Slice an Iterator by $start and $length
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class SliceViewHelper extends AbstractViewHelper {

	/**
	 * Render method
	 *
	 * @param mixed $haystack
	 * @param integer $start
	 * @param integer $length
	 * @param string $as
	 * @throws \Exception
	 * @return array
	 */
	public function render($haystack = NULL, $start = 0, $length = NULL, $as = NULL) {
		if (NULL === $haystack) {
			$haystack = $this->renderChildren();
		}
		if (TRUE === $haystack instanceof \Traversable) {
			$haystack = iterator_to_array($haystack, TRUE);
		} elseif (FALSE === is_array($haystack)) {
			throw new Exception('Cannot slice unsupported type: ' . gettype($haystack), 1353812601);
		}
		$output = array_slice($haystack, $start, $length, TRUE);
		if (NULL !== $as) {
			$variables = array($as => $output);
			$output = ViewHelperUtility::renderChildrenWithVariables($this, $this->templateVariableContainer, $variables);
		}
		return $output;
	}

}
