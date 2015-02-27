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
 * Creates chunks from an input Array/Traversable with option to allocate items to a fixed number of chunks
 *
 * @author Benjamin Rau <rau@codearts.at>, codearts
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class ChunkViewHelper extends AbstractViewHelper {

	/**
	 * Render method
	 *
	 * @param integer $count The count of items per chunk or if fixed number of chunks
	 * @param boolean $fixed Whether to allocate items to a fixed number of chunks or not
	 * @param mixed $subject The subject Traversable/Array instance to shift
	 * @param string $as If specified, inserts a template variable with this name, then renders the child content, then removes the variable
	 * @param boolean $preserveKeys If set to true, the original array keys will be preserved in the chunks
	 * @throws \Exception
	 * @return array
	 */
	public function render($count, $fixed = FALSE, $subject = NULL, $as = NULL, $preserveKeys = FALSE) {
		if (NULL === $subject) {
			$subject = $this->renderChildren();
		}
		if (TRUE === $subject instanceof \Traversable) {
			$subject = iterator_to_array($subject, TRUE);
		} elseif (FALSE === is_array($subject)) {
			throw new \Exception('Cannot get values of unsupported type: ' . gettype($subject), 1357098192);
		}
		$output = array();
		if (0 >= $count) {
			return $output;
		}
		if (TRUE === (boolean) $fixed) {
			$subjectSize = count($subject);
			if (0 < $subjectSize) {
				$chunkSize = ceil($subjectSize / $count);
				$output = array_chunk($subject, $chunkSize, $preserveKeys);
			}
			// Fill the resulting array with empty items to get the desired element count
			$elementCount = count($output);
			if ($elementCount < $count) {
				$output += array_fill($elementCount, $count - $elementCount, NULL);
			}
		} else {
			$output = array_chunk($subject, $count, $preserveKeys);
		}
		if (NULL !== $as) {
			$variables = array($as => $output);
			$output = ViewHelperUtility::renderChildrenWithVariables($this, $this->templateVariableContainer, $variables);
		}
		return $output;
	}

}
