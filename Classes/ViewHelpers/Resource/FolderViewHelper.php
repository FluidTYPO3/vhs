<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ResourceViewHelperTrait;
use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3\CMS\Core\Resource\Filter\FileExtensionFilter;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * ViewHelper to handle FAL folders, fetch all files of the given folders
 * The identifier must be in the format '[fileStorageId]:/folder/subfolder/'
 * Example identifier: 'file:1:/myFolder/' or '1:/myFolder/'
 *
 * @author Frank Rakow <frank.rakow@gestalten.de>, Gestaltende GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Resource
 */
class FolderViewHelper extends AbstractTagBasedViewHelper {

	use TemplateVariableViewHelperTrait;
	use ResourceViewHelperTrait;

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('identifier', 'mixed', 'The FAL combined identifiers (either CSV, array or implementing Traversable).', FALSE, NULL);
		$this->registerArgument('recursive', 'boolean', 'If TRUE,then all files are fetched recursivly.', FALSE, FALSE);
		$this->registerArgument('start', 'int', 'The item to start at.', FALSE, 0);
		$this->registerArgument('numberOfItems', 'int', 'The number of items to return', FALSE, 0);
		$this->registerArgument('filterExtensions', 'string', 'if not NULL, CSV of allowed file extensions.', FALSE, NULL);

		$this->registerAsArgument();
	}

	/**
	 * @return mixed
	 */
	public function render() {
		$files = $this->getFilesOfFolders(TRUE);
		if (1 === count($files)) {
			$files = array_shift($files);
		}
		return $this->renderChildrenWithVariableOrReturnInput($files);
	}



}
