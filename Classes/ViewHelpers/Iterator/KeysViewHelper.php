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
 * Gets keys from an iterator
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @author Stefan Neufeind <info (at) speedpartner.de>
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class KeysViewHelper extends AbstractViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('subject', 'mixed', 'Input to work on - Array/Traversable/...', FALSE, NULL);
		$this->registerArgument('as', 'string', 'If specified, a template variable with this name containing the requested data will be inserted instead of returning it.', FALSE, NULL);
	}

	/**
	 * @return array
	 */
	public function render() {
		$subject = $this->arguments['subject'];
		if (NULL === $subject) {
			$subject = $this->renderChildren();
		}
		$subject = ViewHelperUtility::arrayFromArrayOrTraversableOrCSV($subject);

		$content = array_keys($subject);

		// Return if no assign
		$as = $this->arguments['as'];
		if (TRUE === empty($as)) {
			return $content;
		}

		$variables = array($as => $content);
		$output = ViewHelperUtility::renderChildrenWithVariables($this, $this->templateVariableContainer, $variables);

		return $output;
	}

}
