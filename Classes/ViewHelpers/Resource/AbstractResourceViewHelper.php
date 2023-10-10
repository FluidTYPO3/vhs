<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\DoctrineQueryProxy;
use FluidTYPO3\Vhs\Utility\ResourceUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Base class for resource related view helpers.
 */
abstract class AbstractResourceViewHelper extends AbstractTagBasedViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument(
            'identifier',
            'mixed',
            'The FAL combined identifiers (either CSV, array or implementing Traversable).'
        );
        $this->registerArgument(
            'categories',
            'mixed',
            'The sys_category records to select the resources from (either CSV, array or implementing Traversable).'
        );
        $this->registerArgument(
            'treatIdAsUid',
            'boolean',
            'If TRUE, the identifier argument is treated as resource uids.',
            false,
            false
        );
        $this->registerArgument(
            'treatIdAsReference',
            'boolean',
            'If TRUE, the identifier argument is treated as reference uids and will be resolved to resources ' .
            'via sys_file_reference.',
            false,
            false
        );
    }

    /**
     * @param mixed $categories
     */
    public function getFiles(bool $onlyProperties = false, ?string $identifier = null, $categories = null): ?array
    {
        $identifier = $this->arrayForMixedArgument($identifier, 'identifier');
        $categories = $this->arrayForMixedArgument($categories, 'categories');
        $treatIdAsUid = (boolean) $this->arguments['treatIdAsUid'];
        $treatIdAsReference = (boolean) $this->arguments['treatIdAsReference'];

        if ($treatIdAsUid && $treatIdAsReference) {
            throw new \RuntimeException(
                'The arguments "treatIdAsUid" and "treatIdAsReference" may not both be TRUE.',
                1384604695
            );
        }

        if (empty($identifier) && empty($categories)) {
             return null;
        }

        foreach ($identifier as $key => $maybeUrl) {
            if (substr($maybeUrl, 0, 5) !== 't3://') {
                continue;
            }
            $parts = parse_url($maybeUrl);
            if (!isset($parts['host']) || $parts['host'] !== 'file' || !isset($parts['query'])) {
                continue;
            }
            parse_str($parts['query'], $queryParts);
            if (isset($queryParts['uid'])) {
                $identifier[$key] = $queryParts['uid'];
                $treatIdAsUid = true;
            }
        }

        $files = [];
        /** @var ResourceFactory $resourceFactory */
        $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);

        if (!empty($categories)) {
            /** @var ConnectionPool $connectionPool */
            $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
            $queryBuilder = $connectionPool->getQueryBuilderForTable($this->getTablenameForSystemConfiguration());
            $queryBuilder->createNamedParameter(
                $this->getTablenameForSystemConfiguration(),
                \PDO::PARAM_STR,
                ':tablenames'
            );
            $queryBuilder->createNamedParameter($categories, Connection::PARAM_STR_ARRAY, ':categories');

            $queryBuilder
                ->select('uid_foreign')
                ->from('sys_category_record_mm')
                ->where(
                    $queryBuilder->expr()->eq('tablenames', ':tablenames')
                )
                ->andWhere(
                    $queryBuilder->expr()->in('uid_local', ':categories')
                );
            $statement = DoctrineQueryProxy::executeQueryOnQueryBuilder($queryBuilder);
            $rows = DoctrineQueryProxy::fetchAllAssociative($statement);

            /** @var int[] $fileUids */
            $fileUids = array_unique(array_column($rows, 'uid_foreign'));

            if (empty($identifier)) {
                foreach ($fileUids as $fileUid) {
                    try {
                        $file = $resourceFactory->getFileObject($fileUid);

                        if ($onlyProperties) {
                            $file = ResourceUtility::getFileArray($file);
                        }

                        $files[] = $file;
                    } catch (\Exception $e) {
                        continue;
                    }
                }

                return $files;
            }
        }

        foreach ($identifier as $i) {
            try {
                if ($treatIdAsUid) {
                    $file = $resourceFactory->getFileObject(intval($i));
                } elseif ($treatIdAsReference) {
                    $fileReference = $resourceFactory->getFileReferenceObject(intval($i));
                    $file = $fileReference->getOriginalFile();
                } else {
                    $file = $resourceFactory->getFileObjectFromCombinedIdentifier($i);
                }

                /** @var File|ProcessedFile|null $file */
                if ($file === null) {
                    continue;
                }

                if (isset($fileUids) && !in_array($file->getUid(), $fileUids)) {
                    continue;
                }

                if ($onlyProperties) {
                    if ($file instanceof ProcessedFile) {
                        $file = $file->toArray();
                    } else {
                        $file = ResourceUtility::getFileArray($file);
                    }
                }

                $files[] = $file;
            } catch (\Exception $e) {
                continue;
            }
        }

        return $files;
    }

    /**
     * Mixed argument with CSV, array, Traversable
     *
     * @param mixed $argument
     */
    public function arrayForMixedArgument($argument, string $name): array
    {
        if (null === $argument) {
            $argument = $this->arguments[$name];
        }

        if ($argument instanceof \Traversable) {
            $argument = iterator_to_array($argument);
        } elseif (is_string($argument)) {
            $argument = GeneralUtility::trimExplode(',', $argument, true);
        } else {
            $argument = (array) $argument;
        }

        return $argument;
    }

    /**
     * This fuction decides if sys_file or sys_file_metadata is used for a query on sys_category_record_mm
     * This is neccessary because it depends on the TYPO3 version and the state of the extension filemetadata if
     * 'sys_file' should be used or 'sys_file_metadata'.
     */
    private function getTablenameForSystemConfiguration(): string
    {
        return 'sys_file_metadata';
    }
}
