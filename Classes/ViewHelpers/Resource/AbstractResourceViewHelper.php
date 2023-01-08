<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Doctrine\DBAL\Driver\Result;
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
    /**
     * Initialize arguments.
     *
     * @return void
     * @api
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument(
            'identifier',
            'mixed',
            'The FAL combined identifiers (either CSV, array or implementing Traversable).',
            false,
            null
        );
        $this->registerArgument(
            'categories',
            'mixed',
            'The sys_category records to select the resources from (either CSV, array or implementing Traversable).',
            false,
            null
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
     * Returns the files
     *
     * @param boolean $onlyProperties
     * @param mixed $identifier
     * @param mixed $categories
     * @return array|null
     * @throws \RuntimeException
     */
    public function getFiles($onlyProperties = false, $identifier = null, $categories = null)
    {
        $identifier = $this->arrayForMixedArgument($identifier, 'identifier');
        $categories = $this->arrayForMixedArgument($categories, 'categories');
        $treatIdAsUid = (boolean) $this->arguments['treatIdAsUid'];
        $treatIdAsReference = (boolean) $this->arguments['treatIdAsReference'];

        if (true === $treatIdAsUid && true === $treatIdAsReference) {
            throw new \RuntimeException(
                'The arguments "treatIdAsUid" and "treatIdAsReference" may not both be TRUE.',
                1384604695
            );
        }

        if (true === empty($identifier) && true === empty($categories)) {
             return null;
        }

        foreach ($identifier as $key => $maybeUrl) {
            if (substr($maybeUrl, 0, 5) !== 't3://') {
                continue;
            }
            $parts = parse_url($maybeUrl);
            if (false === isset($parts['host']) || $parts['host'] !== 'file' || false === isset($parts['query'])) {
                continue;
            }
            parse_str($parts['query'], $queryParts);
            if (true === isset($queryParts['uid'])) {
                $identifier[$key] = $queryParts['uid'];
                $treatIdAsUid = true;
            }
        }

        $files = [];
        /** @var ResourceFactory $resourceFactory */
        $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);

        if (false === empty($categories)) {
            /** @var ConnectionPool $connectionPool */
            $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
            $queryBuilder = $connectionPool->getQueryBuilderForTable($this->getTablenameForSystemConfiguration());
            $queryBuilder->createNamedParameter(
                $this->getTablenameForSystemConfiguration(),
                \PDO::PARAM_STR,
                ':tablenames'
            );
            $queryBuilder->createNamedParameter($categories, Connection::PARAM_STR_ARRAY, ':categories');

            /** @var Result $statement */
            $statement = $queryBuilder
                ->select('uid_foreign')
                ->from('sys_category_record_mm')
                ->where(
                    $queryBuilder->expr()->eq('tablenames', ':tablenames')
                )
                ->andWhere(
                    $queryBuilder->expr()->in('uid_local', ':categories')
                )
                ->execute();
            $rows = $statement->fetchAllAssociative();

            /** @var int[] $fileUids */
            $fileUids = array_unique(array_column($rows, 'uid_foreign'));

            if (true === empty($identifier)) {
                foreach ($fileUids as $fileUid) {
                    try {
                        $file = $resourceFactory->getFileObject($fileUid);

                        if (true === $onlyProperties) {
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
                if (true === $treatIdAsUid) {
                    $file = $resourceFactory->getFileObject(intval($i));
                } elseif (true === $treatIdAsReference) {
                    $fileReference = $resourceFactory->getFileReferenceObject(intval($i));
                    $file = $fileReference->getOriginalFile();
                } else {
                    $file = $resourceFactory->getFileObjectFromCombinedIdentifier($i);
                }

                /** @var File|ProcessedFile|null $file */
                if ($file === null) {
                    continue;
                }

                if (true === isset($fileUids) && false === in_array($file->getUid(), $fileUids)) {
                    continue;
                }

                if (true === $onlyProperties) {
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
     * @param string $name
     * @return array
     */
    public function arrayForMixedArgument($argument, $name)
    {
        if (null === $argument) {
            $argument = $this->arguments[$name];
        }

        if (true === $argument instanceof \Traversable) {
            $argument = iterator_to_array($argument);
        } elseif (true === is_string($argument)) {
            $argument = GeneralUtility::trimExplode(',', $argument, true);
        } else {
            $argument = (array) $argument;
        }

        return $argument;
    }

    /**
     * This fuction decides if sys_file or sys_file_metadata is used for a query on sys_category_record_mm
     * This is neccessary because it depends on the TYPO3 version and the state of the extension filemetadata if
     * 'sys_file' should be used or 'sys_file_metadata'
     *
     * @return string
     */
    private function getTablenameForSystemConfiguration()
    {
        return 'sys_file_metadata';
    }
}
