<?php

declare(strict_types=1);


namespace FluidTYPO3\Vhs\Factory;


use TYPO3\CMS\Core\Utility\GeneralUtility;

class InstanceFactory
{
    /**
     * @return \TYPO3\CMS\Frontend\Page\PageRepository|\TYPO3\CMS\Core\Domain\Repository\PageRepository
     */
    public static function getPageRepository()
    {
        if (class_exists(\TYPO3\CMS\Frontend\Page\PageRepository::class)) {
            return GeneralUtility::makeInstance(\TYPO3\CMS\Frontend\Page\PageRepository::class);
        }
        return GeneralUtility::makeInstance(\TYPO3\CMS\Core\Domain\Repository\PageRepository::class);
    }
}
