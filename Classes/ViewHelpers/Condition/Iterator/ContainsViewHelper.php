<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyObjectStorage;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
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
    public function initializeArguments(): void
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

    public static function verdict(array $arguments, RenderingContextInterface $renderingContext): bool
    {
        /** @var array|DomainObjectInterface[]|QueryResult|ObjectStorage|iterable $haystack */
        $haystack = $arguments['haystack'];
        /** @var mixed $needle */
        $needle = $arguments['needle'];
        return is_array($arguments)
            && static::assertHaystackHasNeedle($haystack, $needle, $arguments) !== false;
    }

    protected static function getNeedleAtIndex(int $index, array $arguments): mixed
    {
        if (0 > $index) {
            return null;
        }
        /** @var array|DomainObjectInterface[]|QueryResult|ObjectStorage|iterable $haystack */
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
        return $asArray[$index] ?? false;
    }

    /**
     * @param array|DomainObjectInterface[]|QueryResult|ObjectStorage|iterable $haystack
     */
    protected static function assertHaystackHasNeedle($haystack, mixed $needle, array $arguments): bool|int
    {
        if (is_array($haystack)) {
            return static::assertHaystackIsArrayAndHasNeedle($haystack, $needle, $arguments);
        } elseif ($haystack instanceof LazyObjectStorage) {
            /** @var int|DomainObjectInterface $needle */
            return static::assertHaystackIsObjectStorageAndHasNeedle($haystack, $needle);
        } elseif ($haystack instanceof ObjectStorage) {
            /** @var int|DomainObjectInterface $needle */
            return static::assertHaystackIsObjectStorageAndHasNeedle($haystack, $needle);
        } elseif ($haystack instanceof QueryResult) {
            /** @var int|DomainObjectInterface $needle */
            return static::assertHaystackIsQueryResultAndHasNeedle($haystack, $needle);
        } elseif (is_scalar($haystack) && is_scalar($needle)) {
            return strpos((string)$haystack, (string)$needle);
        }
        return false;
    }

    /**
     * @param QueryResult $haystack
     */
    protected static function assertHaystackIsQueryResultAndHasNeedle(
        $haystack,
        int|DomainObjectInterface $needle
    ): bool|int {
        if ($needle instanceof DomainObjectInterface) {
            $needle = $needle->getUid();
        }
        /**
         * @var integer $index
         * @var DomainObjectInterface $candidate
         */
        foreach ($haystack as $index => $candidate) {
            if ($candidate->getUid() === (int) $needle) {
                return $index;
            }
        }
        return false;
    }

    protected static function assertHaystackIsObjectStorageAndHasNeedle(
        ObjectStorage $haystack,
        int|DomainObjectInterface $needle
    ): bool|int {
        $index = 0;
        if ($needle instanceof DomainObjectInterface) {
            $needle = (int) $needle->getUid();
        }

        /** @var DomainObjectInterface $candidate */
        foreach ($haystack as $candidate) {
            if ($candidate->getUid() === (int) $needle) {
                return $index;
            }
            $index++;
        }
        return false;
    }

    protected static function assertHaystackIsArrayAndHasNeedle(
        array $haystack,
        mixed $needle,
        array $arguments
    ): bool|int {
        if (!$needle instanceof DomainObjectInterface) {
            if ($arguments['considerKeys']) {
                $result = false !== array_search($needle, $haystack) || isset($haystack[$needle]);
            } else {
                /** @var integer|false $result */
                $result = array_search($needle, $haystack);
            }
            return $result;
        } else {
            foreach ($haystack as $index => $straw) {
                if ((int) $straw->getUid() === (int) $needle->getUid()) {
                    return $index;
                }
            }
        }
        return false;
    }
}
