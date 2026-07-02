<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Content;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Core\ViewHelper\AbstractViewHelper;
use FluidTYPO3\Vhs\Proxy\DoctrineQueryProxy;
use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use FluidTYPO3\Vhs\Utility\RequestResolver;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * ViewHelper to access data of a content element record.
 */
class InfoViewHelper extends AbstractViewHelper
{
    use TemplateVariableViewHelperTrait;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var PageRepository
     */
    protected $pageRepository;

    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager): void
    {
        $this->configurationManager = $configurationManager;
    }

    public function injectPageRepository(PageRepository $pageRepository): void
    {
        $this->pageRepository = $pageRepository;
    }

    public function initializeArguments(): void
    {
        $this->registerAsArgument();
        $this->registerArgument(
            'contentUid',
            'integer',
            'This UID will be used to fetch content element data.',
            true
        );
        $this->registerArgument(
            'field',
            'string',
            'If specified, only this field will be returned/assigned instead of the complete content element record.'
        );
    }

    public function render(): mixed
    {
        /** @var int $contentUid */
        $contentUid = $this->arguments['contentUid'];

        /** @var string|null $field */
        $field = $this->arguments['field'];
        $selectFields = $field;

        if (!$field || !isset($GLOBALS['TCA']['tt_content']['columns'][$field])) {
            $selectFields = '*';
        }

        $record = $this->fetchRecord($selectFields ?? '*', $contentUid);

        $languageUid = RequestResolver::getLanguage()->getLanguageId();

        if ($languageUid && $record && $record['sys_language_uid'] !== $languageUid) {
            $languageAspect = new LanguageAspect(RequestResolver::getLanguage()->getLanguageId());
            $record = $this->pageRepository->getLanguageOverlay('tt_content', $record, $languageAspect);
        }

        if (!$record) {
            throw new \Exception(
                sprintf('Either record with uid %d or field %s does not exist.', $contentUid, $selectFields),
                1358679983
            );
        }

        // Check if single field or whole record should be returned
        $content = null;
        if (!$field) {
            $content = $record;
        } elseif (isset($record[$field])) {
            $content = $record[$field];
        }

        return $this->renderChildrenWithVariableOrReturnInput($content);
    }

    protected function fetchRecord(string $field, int $uid): ?array
    {
        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
        $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT, ':uid');

        $queryBuilder
            ->select($field)
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq('uid', ':uid')
            );
        $result = DoctrineQueryProxy::executeQueryOnQueryBuilder($queryBuilder);
        $record = DoctrineQueryProxy::fetchAssociative($result);
        return $record;
    }
}
