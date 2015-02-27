<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\ViewHelperUtility;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

/**
 * ### Iterator Reversal ViewHelper
 *
 * Reverses the order of every member of an Iterator/Array,
 * preserving the original keys.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class ReverseViewHelper extends AbstractViewHelper {

	/**
	 * Initialize arguments
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('as', 'string', 'Which variable to update in the TemplateVariableContainer. If left out, returns reversed data instead of updating the variable (i.e. reference or copy)');
	}

	/**
	 * "Render" method - sorts a target list-type target. Either $array or
	 * $objectStorage must be specified. If both are, ObjectStorage takes precedence.
	 *
	 * Returns the same type as $subject. Ignores NULL values which would be
	 * OK to use in an f:for (empty loop as result)
	 *
	 * @param array|\Iterator $subject An array or Iterator implementation to sort
	 * @throws \Exception
	 * @return mixed
	 */
	public function render($subject = NULL) {
		$as = $this->arguments['as'];
		if (NULL === $subject && FALSE === isset($as)) {
			// this case enables inline usage if the "as" argument
			// is not provided. If "as" is provided, the tag content
			// (which is where inline arguments are taken from) is
			// expected to contain the rendering rather than the variable.
			$subject = $this->renderChildren();
		}
		$array = NULL;
		if (TRUE === is_array($subject)) {
			$array = $subject;
		} else {
			if (TRUE === $subject instanceof QueryResultInterface) {
				/** @var QueryResultInterface $subject */
				$array = $subject->toArray();
			} elseif (TRUE === $subject instanceof \Traversable) {
				/** @var \Iterator $subject */
				$array = iterator_to_array($subject, TRUE);
			} elseif (NULL !== $subject) {
				// a NULL value is respected and ignored, but any
				// unrecognized value other than this is considered a
				// fatal error.
				throw new Exception('Invalid variable type passed to Iterator/ReverseViewHelper. Expected any of Array, QueryResult, ' .
					'ObjectStorage or Iterator implementation but got ' . gettype($subject), 1351958941);
			}
		}
		$array = array_reverse($array, TRUE);
		if (NULL !== $as) {
			$variables = array($as => $array);
			$content = ViewHelperUtility::renderChildrenWithVariables($this, $this->templateVariableContainer, $variables);
			return $content;
		}
		return $array;
	}

}
