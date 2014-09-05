<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Content;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Benjamin Rau <rau@codearts.at>
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
 * ViewHelper to access data of the current content element record
 *
 * @author Benjamin Rau <rau@codearts.at>
 * @package Vhs
 * @subpackage ViewHelpers\Content
 */
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use FluidTYPO3\Vhs\Utility\ViewHelperUtility;

class InfoViewHelper extends AbstractViewHelper {

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface An instance of the Configuration Manager
	 * @return void
	 */
	public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('contentUid', 'integer', 'If specified, this UID will be used to fetch content element data instead of using the current content element.', FALSE, 0);
		$this->registerArgument('as', 'string', 'If specified, a template variable with this name containing the requested data will be inserted instead of returning it.', FALSE, NULL);
		$this->registerArgument('field', 'string', 'If specified, only this field will be returned/assigned instead of the complete content element record.', FALSE, NULL);
	}

	/**
	 * @return mixed
	 */
	public function render() {
		$contentUid = intval($this->arguments['contentUid']);
		if (0 === $contentUid) {
			$cObj = $this->configurationManager->getContentObject();
			$record = $cObj->data;
		}

		$field = $this->arguments['field'];

		if (FALSE === isset($record) && 0 !== $contentUid) {
			if (NULL !== $field && TRUE === isset($GLOBALS['TCA']['tt_content']['columns'][$field])) {
				$selectFields = $field;
			} else {
				$selectFields = '*';
			}
			$record = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow($selectFields, 'tt_content', sprintf('uid=%d', $contentUid));

			// Add the page overlay
			$languageUid = intval($GLOBALS['TSFE']->sys_language_uid);
			if (0 !== $languageUid && $GLOBALS['TSFE']->sys_language_contentOL) {
				$record = $GLOBALS['TSFE']->sys_page->getRecordOverlay('tt_content', $record, $GLOBALS['TSFE']->sys_language_content, $GLOBALS['TSFE']->sys_language_contentOL);
			}
		}

		if (FALSE === $record && FALSE === isset($record)) {
			throw new \Exception(sprintf('Either record with uid %d or field %s do not exist.', $contentUid, $selectFields), 1358679983);
		}

		// Check if single field or whole record should be returned
		$content = NULL;
		if (NULL === $field) {
			$content = $record;
		} elseif (TRUE === isset($record[$field])) {
			$content = $record[$field];
		}

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
