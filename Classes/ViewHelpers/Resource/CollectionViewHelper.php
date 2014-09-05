<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource;

/***************************************************************
*  Copyright notice
*
*  (c) 2014 Dmitri Pisarev <dimaip@gmail.com>
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
***************************************************************/

use \TYPO3\CMS\Core\Collection\RecordCollectionRepository;

/**
 * ### Collection ViewHelper
 * This viewhelper returns a collection referenced by uid.
 * For more information look here: 
 * http://docs.typo3.org/typo3cms/CoreApiReference/6.2/ApiOverview/Collections/Index.html#collections-api
 * 
 * ### Example
 * {v:resource.collection(uid:'123') -> v:var.set(name: 'someCollection')}
 *
 * @category ViewHelpers
 * @package Vhs
 * @author Dmitri Pisarev <dimaip@gmail.com>
*/

class CollectionViewHelper extends AbstractResourceViewHelper {

	/**
	 * @var RecordCollectionRepository
	 */
	protected $collectionRepository;

	/**
	 * @param RecordCollectionRepository $collectionRepository
	 * @return void
	 */
	public function injectCollectionRepository(RecordCollectionRepository $collectionRepository) {
		$this->collectionRepository = $collectionRepository;
	}

	/**
	 * Returns a specific collection referenced by uid.
	 *
	 * @param integer $uid
	 * @return mixed
	 */
	public function render($uid) {
		if (NULL !== $uid) {
			/** @var \TYPO3\CMS\Core\Collection\AbstractRecordCollection $collection */
			$collection = $this->collectionRepository->findByUid($uid);
			if (NULL !== $collection) {
				return $collection->loadContents();
			} else {
				return NULL;
			}
		}
	}
}
