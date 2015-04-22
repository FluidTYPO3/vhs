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
 * Returns random element from array
 *
 * @author Björn Fromme, <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class RandomViewHelper extends AbstractViewHelper {

	use TemplateVariableViewHelperTrait;
	use ArrayConsumingViewHelperTrait;

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerAsArgument();
		$this->registerArgument('subject', 'mixed', 'The subject Traversable/Array instance from which to select a random element', FALSE, NULL);
	}

	/**
	 * Render method
	 *
	 * @throws Exception
	 * @return mixed
	 */
	public function render() {
		$subject = $this->getArgumentFromArgumentsOrTagContentAndConvertToArray('subject');
		$randomElement = $subject[array_rand($subject)];
		return $this->renderChildrenWithVariableOrReturnInput($randomElement);
	}

}
