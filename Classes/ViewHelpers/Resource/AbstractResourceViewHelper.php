<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\ResourceUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

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
     * @return array|NULL
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

        $files = [];
        $resourceFactory = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\ResourceFactory');

        if (false === empty($categories)) {
            $sqlCategories = implode(',', $GLOBALS['TYPO3_DB']->fullQuoteArray($categories, 'sys_category_record_mm'));
            $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                'uid_foreign',
                'sys_category_record_mm',
                'tablenames = \'sys_file\' AND uid_local IN (' . $sqlCategories . ')'
            );

            $fileUids = [];
            while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
                $fileUids[] = intval($row['uid_foreign']);
            }
            $fileUids = array_unique($fileUids);

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

                if (true === isset($fileUids) && false === in_array($file->getUid(), $fileUids)) {
                    continue;
                }

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
}
