<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\ViewHelperUtility;

/**
 * ViewHelper to output or assign FAL sys_file records
 *
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Resource
 */
class FileViewHelper extends AbstractResourceViewHelper {

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		parent::initializeArguments();

		$this->registerArgument('as', 'string', 'If specified, a template variable with this name containing the requested data will be inserted instead of returning it.', FALSE, NULL);
	}

	/**
	 * @return mixed
	 */
	public function render() {
		$files = $this->getFiles(TRUE);
		if (1 === count($files)) {
			$files = array_shift($files);
		}

		// Return if no assign
		$as = $this->arguments['as'];
		if (TRUE === empty($as)) {
			return $files;
		}
		$variables = array($as => $files);
		$output = ViewHelperUtility::renderChildrenWithVariables($this, $this->templateVariableContainer, $variables);
		return $output;
	}

}
