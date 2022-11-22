<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyObjectStorage;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * Condition ViewHelper. Renders the then-child if Iterator/array
 * haystack contains needle value.
 *
 * ### Example:
 *
 * ```
 * {v:condition.iterator.contains(needle: 'foo', haystack: {0: 'foo'}, then: 'yes', else: 'no')}
 * ```
 */
class ContainsViewHelper extends AbstractConditionViewHelper
{
    /**
     * Initialize arguments
     *
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('needle', 'mixed', 'Needle to search for in haystack', true);
        $this->registerArgument('haystack', 'mixed', 'Haystack in which to look for needle', true);
        $this->registerArgument(
            'considerKeys',
            'boolean',
            'Tell whether to consider keys in the search assuming haystack is an array.',
            false,
            false
        );
    }

    /**
     * @param array $arguments
     * @return bool
     */
    protected static function evaluateCondition($arguments = null)
    {
        return is_array($arguments)
            && static::assertHaystackHasNeedle($arguments['haystack'], $arguments['needle'], $arguments) !== false;
    }

    /**
     * @param integer $index
     * @param array $arguments
     * @return mixed
     */
    protected static function getNeedleAtIndex($index, $arguments)
    {
        if (0 > $index) {
            return null;
        }
        $haystack = $arguments['haystack'];
        $asArray = [];
        if (is_array($haystack)) {
            $asArray = $haystack;
        } elseif ($haystack instanceof ObjectStorage) {
            $asArray = $haystack->toArray();
        } elseif ($haystack instanceof QueryResult) {
            $asArray = $haystack->toArray();
        } elseif (is_string($haystack)) {
            $asArray = str_split($haystack);
        }
        return (true === isset($asArray[$index]) ? $asArray[$index] : false);
    }

    /**
     * @param array|DomainObjectInterface[]|QueryResult|ObjectStorage $haystack
     * @param integer|DomainObjectInterface $needle
     * @param array $arguments
     * @return boolean|integer
     */
    protected static function assertHaystackHasNeedle($haystack, $needle, $arguments)
    {
        if (is_array($haystack)) {
            return static::assertHaystackIsArrayAndHasNeedle($haystack, $needle, $arguments);
        } elseif ($haystack instanceof LazyObjectStorage) {
            return static::assertHaystackIsObjectStorageAndHasNeedle($haystack, $needle);
        } elseif ($haystack instanceof ObjectStorage) {
            return static::assertHaystackIsObjectStorageAndHasNeedle($haystack, $needle);
        } elseif ($haystack instanceof QueryResult) {
            return static::assertHaystackIsQueryResultAndHasNeedle($haystack, $needle);
        } elseif (is_scalar($haystack) && is_scalar($needle)) {
            return strpos((string)$haystack, (string)$needle);
        }
        return false;
    }

    /**
     * @param array|DomainObjectInterface[]|QueryResult|ObjectStorage $haystack
     * @param string|int|DomainObjectInterface $needle
     * @return boolean|integer
     */
    protected static function assertHaystackIsQueryResultAndHasNeedle($haystack, $needle)
    {
        if ($needle instanceof DomainObjectInterface) {
            $needle = $needle->getUid();
        }
        /**
         * @var integer $index
         * @var DomainObjectInterface $candidate
         */
        foreach ($haystack as $index => $candidate) {
            if ($candidate->getUid() === (integer) $needle) {
                return $index;
            }
        }
        return false;
    }

    /**
     * @param array|DomainObjectInterface[]|QueryResult|ObjectStorage $haystack
     * @param integer|DomainObjectInterface $needle
     * @return boolean|integer
     */
    protected static function assertHaystackIsObjectStorageAndHasNeedle($haystack, $needle)
    {
        $index = 0;
        if ($needle instanceof AbstractDomainObject) {
            $needle = $needle->getUid();
        }
        /** @var DomainObjectInterface $candidate */
        foreach ($haystack as $candidate) {
            if ($candidate->getUid() === $needle) {
                return $index;
            }
            $index++;
        }
        return false;
    }

    /**
     * @param array&DomainObjectInterface[] $haystack
     * @param integer|DomainObjectInterface $needle
     * @param array $arguments
     * @return boolean|integer
     */
    protected static function assertHaystackIsArrayAndHasNeedle($haystack, $needle, $arguments)
    {
        if (!$needle instanceof DomainObjectInterface) {
            if ($arguments['considerKeys']) {
                $result = false !== array_search($needle, $haystack) || true === isset($haystack[$needle]);
            } else {
                /** @var integer|false $result */
                $result = array_search($needle, $haystack);
            }
            return $result;
        } else {
            foreach ($haystack as $index => $straw) {
                if ((integer) $straw->getUid() === $needle->getUid()) {
                    return $index;
                }
            }
        }
        return false;
    }
}
