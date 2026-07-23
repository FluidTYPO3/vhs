<?php
namespace FluidTYPO3\Vhs\Proxy;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Doctrine\DBAL\Driver\ResultStatement;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Result;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

class DoctrineQueryProxy
{
    /**
     * Returns \Doctrine\DBAL\Result on v11+, \Doctrine\DBAL\Driver\ResultStatement on v10
     *
     * @return Result|ResultStatement
     */
    public static function executeQueryOnQueryBuilder(QueryBuilder $queryBuilder)
    {
        if (method_exists($queryBuilder, 'executeQuery')) {
            return $queryBuilder->executeQuery();
        }
        /** @var Result $result */
        $result = $queryBuilder->execute();
        return $result;
    }

    /**
     * @param Result|ResultStatement $result
     */
    public static function fetchAssociative($result): ?array
    {
        if (method_exists($result, 'fetchAssociative')) {
            /** @var array|null $output */
            $output = $result->fetchAssociative() ?: null;
        } else {
            /** @var array|null $output */
            $output = $result->fetch(FetchMode::ASSOCIATIVE);
        }

        return $output;
    }

    /**
     * @param Result|ResultStatement $result
     */
    public static function fetchAllAssociative($result): array
    {
        if (method_exists($result, 'fetchAllAssociative')) {
            return $result->fetchAllAssociative() ?: [];
        }
        return $result->fetchAll(FetchMode::ASSOCIATIVE) ?: [];
    }
}
