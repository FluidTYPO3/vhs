<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Collection\RecordCollectionRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ### Collection ViewHelper
 * This viewhelper returns a collection referenced by uid.
 * For more information look here:
 * http://docs.typo3.org/typo3cms/CoreApiReference/6.2/ApiOverview/Collections/Index.html#collections-api
 *
 * ### Example
 *
 * ```
 * {v:resource.collection(uid:'123') -> v:var.set(name: 'someCollection')}
 * ```
 */
class CollectionViewHelper extends AbstractResourceViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('uid', 'integer', 'UID of the collection to be rendered', true);
    }

    /**
     * Returns a specific collection referenced by uid.
     *
     * @return mixed
     */
    public function render()
    {
        $uid = $this->arguments['uid'];
        if (null !== $uid) {
            if (!class_exists(RecordCollectionRepository::class)) {
                throw new \Exception('On TYPO3v12, v:resource.collection requires EXT:legacy_collections', 1670521759);
            }
            /** @var RecordCollectionRepository $collectionRepository */
            $collectionRepository = GeneralUtility::makeInstance(RecordCollectionRepository::class);
            /** @var \TYPO3\CMS\Core\Collection\AbstractRecordCollection $collection */
            $collection = $collectionRepository->findByUid($uid);
            if (null !== $collection) {
                $collection->loadContents();
            }
            return $collection;
        }
        return null;
    }
}
