<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ArrayConsumingViewHelperTrait;
use FluidTYPO3\Vhs\Traits\BasicViewHelperTrait;
use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyObjectStorage;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

/**
 * Sorts an instance of ObjectStorage, an Iterator implementation,
 * an Array or a QueryResult (including Lazy counterparts).
 *
 * Can be used inline, i.e.:
 *
 *
 *     <f:for each="{dataset -> vhs:iterator.sort(sortBy: 'name')}" as="item">
 *         // iterating data which is ONLY sorted while rendering this particular loop
 *     </f:for>
 */
class SortViewHelper extends AbstractViewHelper
{

    use BasicViewHelperTrait;
    use TemplateVariableViewHelperTrait;
    use ArrayConsumingViewHelperTrait;

    /**
     * Contains all flags that are allowed to be used
     * with the sorting functions
     *
     * @var array
     */
    protected $allowedSortFlags = [
        'SORT_REGULAR',
        'SORT_STRING',
        'SORT_NUMERIC',
        'SORT_NATURAL',
        'SORT_LOCALE_STRING',
        'SORT_FLAG_CASE'
    ];

    /**
     * Initialize arguments
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerAsArgument();
        $this->registerArgument('subject', 'mixed', 'The array/Traversable instance to sort');
        $this->registerArgument(
            'sortBy',
            'string',
            'Which property/field to sort by - leave out for numeric sorting based on indexes(keys)'
        );
        $this->registerArgument(
            'order',
            'string',
            'ASC, DESC, RAND or SHUFFLE. RAND preserves keys, SHUFFLE does not - but SHUFFLE is faster',
            false,
            'ASC'
        );
        $this->registerArgument(
            'sortFlags',
            'string',
            'Constant name from PHP for `SORT_FLAGS`: `SORT_REGULAR`, `SORT_STRING`, `SORT_NUMERIC`, ' .
            '`SORT_NATURAL`, `SORT_LOCALE_STRING` or `SORT_FLAG_CASE`. You can provide a comma seperated list or ' .
            'array to use a combination of flags.',
            false,
            'SORT_REGULAR'
        );
    }

    /**
     * "Render" method - sorts a target list-type target. Either $array or
     * $objectStorage must be specified. If both are, ObjectStorage takes precedence.
     *
     * Returns the same type as $subject. Ignores NULL values which would be
     * OK to use in an f:for (empty loop as result)
     * @return mixed
     * @throws \Exception
     */
    public function render()
    {
        $subject = $this->getArgumentFromArgumentsOrTagContent('subject');
        $sorted = null;
        if (true === is_array($subject)) {
            $sorted = $this->sortArray($subject);
        } else {
            if (true === $subject instanceof ObjectStorage || true === $subject instanceof LazyObjectStorage) {
                $sorted = $this->sortObjectStorage($subject);
            } elseif (true === $subject instanceof \Iterator) {
                /** @var \Iterator $subject */
                $array = iterator_to_array($subject, true);
                $sorted = $this->sortArray($array);
            } elseif (true === $subject instanceof QueryResultInterface) {
                /** @var QueryResultInterface $subject */
                $sorted = $this->sortArray($subject->toArray());
            } elseif (null !== $subject) {
                // a NULL value is respected and ignored, but any
                // unrecognized value other than this is considered a
                // fatal error.
                throw new \Exception(
                    'Unsortable variable type passed to Iterator/SortViewHelper. Expected any of Array, QueryResult, ' .
                    ' ObjectStorage or Iterator implementation but got ' . gettype($subject),
                    1351958941
                );
            }
        }
        return $this->renderChildrenWithVariableOrReturnInput($sorted);
    }

    /**
     * Sort an array
     *
     * @param array|\Iterator $array
     * @return array
     */
    protected function sortArray($array)
    {
        $sorted = [];
        foreach ($array as $index => $object) {
            if (true === isset($this->arguments['sortBy'])) {
                $index = $this->getSortValue($object);
            }
            while (isset($sorted[$index])) {
                $index .= '.1';
            }
            $sorted[$index] = $object;
        }
        if ('ASC' === $this->arguments['order']) {
            ksort($sorted, $this->getSortFlags());
        } elseif ('RAND' === $this->arguments['order']) {
            $sortedKeys = array_keys($sorted);
            shuffle($sortedKeys);
            $backup = $sorted;
            $sorted = [];
            foreach ($sortedKeys as $sortedKey) {
                $sorted[$sortedKey] = $backup[$sortedKey];
            }
        } elseif ('SHUFFLE' === $this->arguments['order']) {
            shuffle($sorted);
        } else {
            krsort($sorted, $this->getSortFlags());
        }
        return $sorted;
    }

    /**
     * Sort an ObjectStorage instance
     *
     * @param ObjectStorage $storage
     * @return ObjectStorage
     */
    protected function sortObjectStorage($storage)
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        /** @var ObjectStorage $temp */
        $temp = $objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage');
        foreach ($storage as $item) {
            $temp->attach($item);
        }
        $sorted = $this->sortArray($storage);
        $storage = $objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage');
        foreach ($sorted as $item) {
            $storage->attach($item);
        }
        return $storage;
    }

    /**
     * Gets the value to use as sorting value from $object
     *
     * @param mixed $object
     * @return mixed
     */
    protected function getSortValue($object)
    {
        $field = $this->arguments['sortBy'];
        $value = ObjectAccess::getPropertyPath($object, $field);
        if (true === $value instanceof \DateTime) {
            $value = intval($value->format('U'));
        } elseif (true === $value instanceof ObjectStorage || true === $value instanceof LazyObjectStorage) {
            $value = $value->count();
        } elseif (is_array($value)) {
            $value = count($value);
        }
        return $value;
    }

    /**
     * Parses the supplied flags into the proper value for the sorting
     * function.
     * @return int
     * @throws Exception
     */
    protected function getSortFlags()
    {
        $constants = $this->arrayFromArrayOrTraversableOrCSV($this->arguments['sortFlags']);
        $flags = 0;
        foreach ($constants as $constant) {
            if (false === in_array($constant, $this->allowedSortFlags)) {
                throw new Exception(
                    'The constant "' . $constant . '" you\'re trying to use as a sortFlag is not allowed. Allowed ' .
                    'constants are: ' . implode(', ', $this->allowedSortFlags) . '.',
                    1404220538
                );
            }
            $flags = $flags | constant(trim($constant));
        }
        return $flags;
    }
}
