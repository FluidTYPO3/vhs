<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ArrayConsumingViewHelperTrait;
use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
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

	use TemplateVariableViewHelperTrait;
	use ArrayConsumingViewHelperTrait;

	/**
	 * Initialize arguments
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerAsArgument();
		$this->registerArgument('haystack', 'mixed', 'The input array/Traversable to reverse', FALSE, NULL);
	}

	/**
	 * Render method
	 *
	 * @param integer $start
	 * @param integer $length
	 * @return array
	 */
	public function render($start = 0, $length = NULL) {
		$haystack = $this->getArgumentFromArgumentsOrTagContentAndConvertToArray('haystack');
		$output = array_slice($haystack, $start, $length, TRUE);
		return $this->renderChildrenWithVariableOrReturnInput($output);
	}

}
