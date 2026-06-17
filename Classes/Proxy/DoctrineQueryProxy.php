<?php
namespace FluidTYPO3\Vhs\Proxy;

use Doctrine\DBAL\Result;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

class DoctrineQueryProxy
{
    public static function executeQueryOnQueryBuilder(QueryBuilder $queryBuilder): Result
    {
        return $queryBuilder->executeQuery();
    }

    public static function fetchAssociative(Result $result): ?array
    {
        return $result->fetchAssociative() ?: null;
    }

    public static function fetchAllAssociative(Result $result): array
    {
        return $result->fetchAllAssociative() ?: [];
    }
}
