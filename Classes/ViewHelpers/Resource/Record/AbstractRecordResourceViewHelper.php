<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource\Record;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use FluidTYPO3\Vhs\Utility\DoctrineQueryProxy;
use FluidTYPO3\Vhs\Utility\ErrorUtility;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Base class: Record Resource ViewHelpers
 */
abstract class AbstractRecordResourceViewHelper extends AbstractViewHelper implements RecordResourceViewHelperInterface
{
    use TemplateVariableViewHelperTrait;

    protected string $table = '';
    protected string $field = '';
    protected string $idField = 'uid';

    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager): void
    {
        $this->configurationManager = $configurationManager;
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('table', 'string', 'The table to lookup records.', true);
        $this->registerArgument('field', 'string', 'The field of the table associated to resources.', true);
        $this->registerArgument(
            'record',
            'array',
            'The actual record. Alternatively you can use the "uid" argument.'
        );
        $this->registerArgument(
            'uid',
            'integer',
            'The uid of the record. Alternatively you can use the "record" argument.'
        );
        $this->registerArgument(
            'as',
            'string',
            'If specified, a template variable with this name containing the requested data will be inserted ' .
            'instead of returning it.'
        );
    }

    /**
     * @param mixed $identity
     * @return mixed
     */
    public function getResource($identity)
    {
        return $identity;
    }

    public function getResources(array $record): array
    {
        $field = $this->getField();

        if (!isset($record[$field])) {
            ErrorUtility::throwViewHelperException(
                'The "field" argument was not found on the selected record.',
                1384612728
            );
        }

        if (empty($record[$field])) {
            return [];
        }

        return GeneralUtility::trimExplode(',', $record[$field]);
    }

    public function getTable(): string
    {
        /** @var string|null $table */
        $table = $this->arguments['table'] ?? null;
        if (null === $table) {
            $table = $this->table;
        }

        if (empty($table) || !is_string($table)) {
            ErrorUtility::throwViewHelperException(
                'The "table" argument must be specified and must be a string.',
                1384611336
            );
        }

        return $table;
    }

    public function getField(): string
    {
        /** @var string|null $field */
        $field = $this->arguments['field'] ?? null;
        if (null === $field) {
            $field = $this->field;
        }

        if (empty($field) || !is_string($field)) {
            ErrorUtility::throwViewHelperException(
                'The "field" argument must be specified and must be a string.',
                1384611355
            );
        }

        return $field;
    }

    public function getRecord(int $id): ?array
    {
        $table = $this->getTable();
        $idField = $this->idField;

        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $connectionPool->getQueryBuilderForTable($table);

        /** @var Context $context */
        $context = GeneralUtility::makeInstance(Context::class);
        $fePreview = $context->hasAspect('frontend.preview')
            && $context->getPropertyFromAspect('frontend.preview', 'isPreview');

        if ($fePreview) {
            $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);
        }

        $queryBuilder->createNamedParameter($id, \PDO::PARAM_INT, ':id');

        $queryBuilder
            ->select('*')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq($idField, ':id')
            );
        $statement = DoctrineQueryProxy::executeQueryOnQueryBuilder($queryBuilder);
        $result = DoctrineQueryProxy::fetchAssociative($statement);
        return $result;
    }

    public function getActiveRecord(): array
    {
        /** @var ContentObjectRenderer $contentObject */
        $contentObject = $this->configurationManager->getContentObject();
        return $contentObject->data;
    }

    /**
     * @return mixed
     */
    public function render()
    {
        /** @var array|null $record */
        $record = $this->arguments['record'] ?? null;
        /** @var int|null $uid */
        $uid = $this->arguments['uid'] ?? null;

        if (null === $record) {
            if (null === $uid) {
                $record = $this->getActiveRecord();
            } else {
                $record = $this->getRecord($uid);
            }
        }

        if (null === $record) {
            ErrorUtility::throwViewHelperException(
                'No record was found. The "record" or "uid" argument must be specified.',
                1384611413
            );
        }

        // attempt to load resources. If any Exceptions happen, transform them to
        // ViewHelperExceptions which render as an inline text error message.
        $content = null;
        try {
            $resources = $this->getResources((array) $record);
            $content = $this->renderChildrenWithVariableOrReturnInput($resources);
        } catch (\Exception $error) {
            // we are doing the pokemon-thing and catching the very top level
            // of Exception because the range of Exceptions that are possibly
            // thrown by the getResources() method in subclasses are not
            // extended from a shared base class like RuntimeException. Thus,
            // we are forced to "catch them all" - but we also output them.
            ErrorUtility::throwViewHelperException($error->getMessage(), $error->getCode());
        }
        return $content;
    }
}
