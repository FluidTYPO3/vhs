<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
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
 * ************************************************************* */

/**
 * ViewHelper to output or assign FAL sys_file records
 *
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Resource
 */
class Tx_Vhs_ViewHelpers_Resource_FileViewHelper extends Tx_Vhs_ViewHelpers_Resource_AbstractResourceViewHelper {

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

		// Backup as argument
		if (TRUE === $this->templateVariableContainer->exists($as)) {
			$backupVariable = $this->templateVariableContainer->get($as);
			$this->templateVariableContainer->remove($as);
		}

		// Render Children
		$this->templateVariableContainer->add($as, $files);
		$output = $this->renderChildren();
		$this->templateVariableContainer->remove($as);

		// Restore as argument
		if (TRUE === isset($backupVariable)) {
			$this->templateVariableContainer->add($as, $backupVariable);
		}

		return $output;
	}

}
