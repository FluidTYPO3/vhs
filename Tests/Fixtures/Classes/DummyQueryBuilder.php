<?php

namespace FluidTYPO3\Vhs\Tests\Fixtures\Classes;

use Doctrine\DBAL\Result;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\QueryRestrictionContainerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DummyQueryBuilder extends QueryBuilder
{
    public Result&MockObject $result;
    public ExpressionBuilder&MockObject $expressionBuilder;
    public QueryRestrictionContainerInterface&MockObject $restrictions;
    public ConnectionPool&MockObject $connectionPool;

    public function __construct(TestCase $testCase)
    {
        /** @var ExpressionBuilder&MockObject $expressionBuilder */
        $expressionBuilder = (new MockBuilder($testCase, ExpressionBuilder::class))
            ->disableOriginalConstructor()
            ->getMock();
        $this->expressionBuilder = $expressionBuilder;

        /** @var Result&MockObject $result */
        $result = (new MockBuilder($testCase, Result::class))
            ->disableOriginalConstructor()
            ->getMock();
        $this->result = $result;

        /** @var QueryRestrictionContainerInterface&MockObject $restrictions */
        $restrictions = (new MockBuilder($testCase, QueryRestrictionContainerInterface::class))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->restrictions = $restrictions;

        /** @var ConnectionPool&MockObject $connectionPool */
        $connectionPool = (new MockBuilder($testCase, ConnectionPool::class))
            ->onlyMethods(['getQueryBuilderForTable'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->connectionPool = $connectionPool;
        $this->connectionPool->method('getQueryBuilderForTable')->willReturn($this);

        GeneralUtility::addInstance(ConnectionPool::class, $this->connectionPool);
    }

    public function select(string ...$selects): QueryBuilder
    {
        return $this;
    }

    public function from(string $from, string $alias = null): QueryBuilder
    {
        return $this;
    }

    public function where(...$predicates): QueryBuilder
    {
        return $this;
    }

    public function andWhere(...$where): QueryBuilder
    {
        return $this;
    }

    public function orderBy(string $fieldName, string $order = null): QueryBuilder
    {
        return $this;
    }

    public function setMaxResults(?int $maxResults = null): QueryBuilder
    {
        return $this;
    }

    public function getRestrictions(): QueryRestrictionContainerInterface
    {
        return $this->restrictions;
    }

    public function createNamedParameter(
        mixed $value,
        mixed $type = Connection::PARAM_STR,
        string $placeHolder = null
    ): string {
        return 'param';
    }

    public function expr(): ExpressionBuilder
    {
        return $this->expressionBuilder;
    }

    public function executeQuery(): Result
    {
        return $this->result;
    }
}
