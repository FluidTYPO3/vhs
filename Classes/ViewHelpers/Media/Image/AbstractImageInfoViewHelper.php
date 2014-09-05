<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media\Image;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;
use FluidTYPO3\Vhs\Utility\ResourceUtility;

/**
 * Base class: Media\Image view helpers
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Media\Image
 */
abstract class AbstractImageInfoViewHelper extends AbstractViewHelper {

	/**
	 * @var \TYPO3\CMS\Core\Resource\ResourceFactory
	 */
	protected $resourceFactory;

	public function __construct() {
		$this->resourceFactory = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\ResourceFactory');
	}

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		$this->registerArgument('src', 'string', 'Path to or id of the image file to determine info for.', TRUE);
		$this->registerArgument('treatIdAsUid', 'boolean', 'If TRUE, the path argument is treated as a resource uid.', FALSE, FALSE);
		$this->registerArgument('treatIdAsReference', 'boolean', 'If TRUE, the path argument is treated as a reference uid and will be resolved to a resource via sys_file_reference.', FALSE, FALSE);
	}

	/**
	 * @throws Exception
	 * @return array
	 */
	public function getInfo() {
		$src = $this->arguments['src'];
		$treatIdAsUid = (boolean) $this->arguments['treatIdAsUid'];
		$treatIdAsReference = (boolean) $this->arguments['treatIdAsReference'];

		if (NULL === $src) {
			$src = $this->renderChildren();
			if (NULL === $src) {
				return array();
			}
		}

		if (TRUE === $treatIdAsUid || TRUE === $treatIdAsReference) {
			$id = (integer) $src;
			$info = array();
			if (TRUE === $treatIdAsUid) {
				$info = $this->getInfoByUid($id);
			} elseif (TRUE === $treatIdAsReference) {
				$info = $this->getInfoByReference($id);
			}
		} else {
			$file = GeneralUtility::getFileAbsFileName($src);
			if (FALSE === file_exists($file) || TRUE === is_dir($file)) {
				throw new Exception('Cannot determine info for "' . $file . '". File does not exist or is a directory.', 1357066532);
			}
			$imageSize = getimagesize($file);
			$info = array(
				'width'  => $imageSize[0],
				'height' => $imageSize[1],
				'type'   => $imageSize['mime'],
			);
		}

		return $info;
	}

	/**
	 * @param integer $id
	 * @return array
	 */
	public function getInfoByReference($id) {
		$fileReference = $this->resourceFactory->getFileReferenceObject($id);
		$file = $fileReference->getOriginalFile();
		return ResourceUtility::getFileArray($file);
	}

	/**
	 * @param integer $uid
	 * @return array
	 */
	public function getInfoByUid($uid) {
		$file = $this->resourceFactory->getFileObject($uid);
		return ResourceUtility::getFileArray($file);
	}

}
