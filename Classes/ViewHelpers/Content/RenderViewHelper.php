<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Content;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;

/**
 * ViewHelper used to render content elements in Fluid page templates
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @author Dominique Feyer, <dfeyer@ttree.ch>
 * @author Daniel Schöne, <daniel@schoene.it>
 * @author Björn Fromme, <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Content
 */
class RenderViewHelper extends AbstractContentViewHelper {

	use TemplateVariableViewHelperTrait;

	/**
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerAsArgument();
	}

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		if ('BE' === TYPO3_MODE) {
			return '';
		}

		$content = $this->getContentRecords();
		if (FALSE === $this->hasArgument('as')) {
			$content = implode(LF, $content);
		}

		return $this->renderChildrenWithVariableOrReturnInput($content);
	}

}
