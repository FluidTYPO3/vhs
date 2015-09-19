<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\ViewHelpers\Condition\Iterator\ContainsViewHelper;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Returns next element in array $haystack from position of $needle
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class NextViewHelper extends ContainsViewHelper {

	/**
	 * Default implementation for CompilableInterface. See CompilableInterface
	 * for a detailed description of this method.
	 *
	 * @param array $arguments
	 * @param \Closure $renderChildrenClosure
	 * @param \TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
	 * @return mixed
	 * @see \TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface
	 */
	static public function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext) {

		$haystack = $arguments['haystack'];
		$needle = $arguments['needle'];
		$evaluation = self::assertHaystackHasNeedle($haystack, $needle, $arguments);

		return self::getNeedleAtIndex($evaluation !== FALSE ? $evaluation + 1 : -1);
	}

}
