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
use FluidTYPO3\Vhs\Traits\VhsViewHelperTrait;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Gets keys from an iterator
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @author Stefan Neufeind <info (at) speedpartner.de>
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class KeysViewHelper extends AbstractViewHelper {

	use TemplateVariableViewHelperTrait;
	use ArrayConsumingViewHelperTrait;

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerAsArgument();
		$this->registerArgument('subject', 'mixed', 'Input to work on - Array/Traversable/...', FALSE, NULL);
	}

	/**
	 * @return array
	 */
	public function render() {
		$subject = $this->getArgumentFromArgumentsOrTagContentAndConvertToArray('subject');
		$content = array_keys($subject);
		return $this->renderChildrenWithVariableOrReturnInput($content);
	}

}
