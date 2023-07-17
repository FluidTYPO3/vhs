<?php
namespace FluidTYPO3\Vhs\Utility;

use Doctrine\DBAL\Result;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

class DoctrineQueryProxy
{
    public static function executeQueryOnQueryBuilder(QueryBuilder $queryBuilder): Result
    {
        if (method_exists($queryBuilder, 'executeQuery')) {
            return $queryBuilder->executeQuery();
        }
        /** @var Result $result */
        $result = $queryBuilder->execute();
        return $result;
    }
}
