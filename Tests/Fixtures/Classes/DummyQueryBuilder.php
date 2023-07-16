<?php

namespace FluidTYPO3\Vhs\Tests\Fixtures\Classes;

use Doctrine\DBAL\Result;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\QueryRestrictionContainerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DummyQueryBuilder extends QueryBuilder
{
    public Result $result;
    public ExpressionBuilder $expressionBuilder;
    public QueryRestrictionContainerInterface $restrictions;
    public ConnectionPool $connectionPool;

    public function __construct(TestCase $testCase)
    {
        $this->expressionBuilder = (new MockBuilder($testCase, ExpressionBuilder::class))
            ->disableOriginalConstructor()
            ->getMock();
        $this->result = (new MockBuilder($testCase, Result::class))
            ->disableOriginalConstructor()
            ->getMock();
        $this->restrictions = (new MockBuilder($testCase, QueryRestrictionContainerInterface::class))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->connectionPool = (new MockBuilder($testCase, ConnectionPool::class))
            ->setMethods(['getQueryBuilderForTable'])
            ->disableOriginalConstructor()
            ->getMock();
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

    public function setMaxResults(int $maxResults): QueryBuilder
    {
        return $this;
    }

    public function getRestrictions(): QueryRestrictionContainerInterface
    {
        return $this->restrictions;
    }

    public function createNamedParameter($value, int $type = \PDO::PARAM_STR, string $placeHolder = null): string
    {
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
