<?php

namespace FluidTYPO3\Vhs\Utility;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageInformation;

/**
 * Class TsfeUtility
 */
class TsfeUtility
{
    /**
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }

    public function getPageId(): int {
        if(CoreUtility::getTypo3MajorVersion() > 12) {
            $pageInformation = $this->getRequest()->getAttribute('frontend.page.information');
            return $pageInformation->getId();
        }

        return $GLOBALS['TSFE']->id;
    }

    /**
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    public function getPageRecordFromRequest(): array
    {
        if (CoreUtility::getTypo3MajorVersion() > 12) {
            /** @var PageInformation $pageInformation */
            $pageInformation = $this->getRequest()->getAttribute('frontend.page.information');
            return $pageInformation->getPageRecord();
        }

        $pageArguments = $request->getAttribute('routing');
        $pageId = $pageArguments->getPageId();

        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('pages');
        $pageRecord = $connection->select('*', 'pages', ['uid' => $pageId])->fetchAssociative();

        return $pageRecord;
    }

    /**
     * @return array|null
     */
    public function getTyposcriptSetupArray(): ?array
    {
        $setup = CoreUtility::getTypo3MajorVersion() > 11
            ? $this->getRequest()->getAttribute('frontend.typoscript')->getSetupArray()
            : $GLOBALS['TSFE']->tmpl->setup;

        return $setup;
    }

    public function isNoCache(): bool {
        if(CoreUtility::getTypo3MajorVersion() > 12) {
            $instructions = $this->getRequest()->getAttribute('frontend.cache.instruction');
            return !empty($instructions) ? $instructions->isCachingAllowed() === false : false;
        }

        return (bool)($GLOBALS['TSFE']->no_cache ?? false);
    }
}
