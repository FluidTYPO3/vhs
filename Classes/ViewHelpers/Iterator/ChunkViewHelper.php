<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
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
	 * @throws \Exception
	 * @return array
	 */
	public function render($count, $fixed = FALSE, $subject = NULL, $as = NULL) {
		if (NULL === $subject) {
			$subject = $this->renderChildren();
		}
		if (TRUE === $subject instanceof Traversable) {
			$subject = iterator_to_array($subject, TRUE);
		} elseif (FALSE === is_array($subject)) {
			throw new \Exception('Cannot get values of unsupported type: ' . gettype($subject), 1357098192);
		}
		if (TRUE === $fixed) {
			$subjectSize = count($subject);
			$chunkSize = ceil($subjectSize / $count);
			$output = array_chunk($subject, $chunkSize);
		} else {
			$output = array_chunk($subject, $count);
		}
		if (NULL !== $as) {
			$variables = array($as => $output);
			$output = ViewHelperUtility::renderChildrenWithVariables($this, $this->templateVariableContainer, $variables);
		}
		return $output;
	}

}
