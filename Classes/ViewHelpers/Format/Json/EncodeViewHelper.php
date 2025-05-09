<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format\Json;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\CompileWithContentArgumentAndRenderStatic;
use FluidTYPO3\Vhs\Utility\ErrorUtility;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### JSON Encoding ViewHelper
 *
 * Returns a string containing the JSON representation of the argument.
 * The argument may be any of the following types:
 *
 * - arrays, associative and traditional
 * - DomainObjects
 * - arrays containing DomainObjects
 * - ObjectStorage containing DomainObjects
 * - standard types (string, integer, boolean, float, NULL)
 * - DateTime including ones found as property values on DomainObjects
 *
 * Recursion protection is enabled for DomainObjects with the option to
 * add a special marker (any variable type above also supported here)
 * which is inserted where an object which would cause recursion would
 * be placed.
 *
 * Be specially careful when you JSON encode DomainObjects which have
 * recursive relations to itself using either 1:n or m:n - in this case
 * the one member of the converted relation will be whichever value you
 * specified as "recursionMarker" - or the default value, NULL. When
 * using the output of such conversion in JavaScript please make sure you
 * check the type before assuming that every member of a converted 1:n
 * or m:n recursive relation is in fact a JavaScript. Not doing so may
 * result in fatal JavaScript errors in the client browser.
 */
class EncodeViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    protected static array $encounteredClasses = [];

    public function initializeArguments(): void
    {
        $this->registerArgument('value', 'mixed', 'Value to encode as JSON');
        $this->registerArgument(
            'useTraversableKeys',
            'boolean',
            'If TRUE, preserves keys from Traversables converted to arrays. Not recommended for ObjectStorages!',
            false,
            false
        );
        $this->registerArgument(
            'preventRecursion',
            'boolean',
            'If FALSE, allows recursion to occur which could potentially be fatal to the output unless managed',
            false,
            true
        );
        $this->registerArgument(
            'recursionMarker',
            'mixed',
            'String or null - inserted instead of recursive instances of objects'
        );
        $this->registerArgument(
            'dateTimeFormat',
            'string',
            'A date() format for DateTime values to JSON-compatible values. NULL means JS UNIXTIME (time()*1000)'
        );
        $this->registerArgument('pretty', 'bool', 'If TRUE, outputs JSON with JSON_PRETTY_PRINT', false, false);
    }

    /**
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $value = $renderChildrenClosure();
        $useTraversableKeys = (boolean) $arguments['useTraversableKeys'];
        $preventRecursion = (boolean) $arguments['preventRecursion'];
        /** @var string $recursionMarker */
        $recursionMarker = $arguments['recursionMarker'] ?? '**recursion**';
        /** @var string|null $dateTimeFormat */
        $dateTimeFormat = $arguments['dateTimeFormat'];
        $options = JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_TAG;
        if ($arguments['pretty']) {
            $options |= JSON_PRETTY_PRINT;
        }

        static::$encounteredClasses = [];
        $json = static::encodeValue(
            $value,
            $useTraversableKeys,
            $preventRecursion,
            $recursionMarker,
            $dateTimeFormat,
            $options
        );
        return $json;
    }

    /**
     * @param mixed $value
     * @return string|false
     */
    protected static function encodeValue(
        $value,
        bool $useTraversableKeys,
        bool $preventRecursion,
        ?string $recursionMarker,
        ?string $dateTimeFormat,
        int $options
    ) {
        if ($value instanceof \Traversable) {
            // Note: also converts ObjectStorage to \Vendor\Extname\Domain\Model\ObjectType[] which are each converted
            $value = iterator_to_array($value, $useTraversableKeys);
        } elseif ($value instanceof DomainObjectInterface) {
            // Convert to associative array,
            $value = static::recursiveDomainObjectToArray($value, $preventRecursion, $recursionMarker);
        } elseif ($value instanceof \DateTime) {
            $value = static::dateTimeToUnixtimeMiliseconds($value, $dateTimeFormat);
        }

        // process output of conversion, catching specially supported object types such as DomainObject and DateTime
        if (is_array($value)) {
            $value = static::recursiveArrayOfDomainObjectsToArray($value, $preventRecursion, $recursionMarker);
            $value = static::recursiveDateTimeToUnixtimeMiliseconds($value, $dateTimeFormat);
        }
        $json = json_encode($value, $options);
        if (JSON_ERROR_NONE !== json_last_error()) {
            ErrorUtility::throwViewHelperException('The provided argument cannot be converted into JSON.', 1358440181);
        }
        return $json;
    }

    /**
     * Converts any encountered DateTime instances to UNIXTIME timestamps
     * which are then multiplied by 1000 to create a JavaScript appropriate
     * time stamp - ready to be loaded into a Date object client-side.
     *
     * Works on already converted DomainObjects which are at this point just
     * associative arrays of values - which might be DateTime instances.
     */
    protected static function recursiveDateTimeToUnixtimeMiliseconds(array $array, ?string $dateTimeFormat): array
    {
        foreach ($array as $key => $possibleDateTime) {
            if ($possibleDateTime instanceof \DateTime) {
                $array[$key] = static::dateTimeToUnixtimeMiliseconds($possibleDateTime, $dateTimeFormat);
            } elseif (is_array($possibleDateTime)) {
                $array[$key] = static::recursiveDateTimeToUnixtimeMiliseconds($possibleDateTime, $dateTimeFormat);
            }
        }
        return $array;
    }

    /**
     * Formats a single DateTime instance to whichever value is demanded by
     * the format specified in $dateTimeFormat (DateTime::format syntax).
     * Default format is NULL a JS UNIXTIME (time()*1000) is produced.
     *
     * @return integer|string
     */
    protected static function dateTimeToUnixtimeMiliseconds(\DateTime $dateTime, ?string $dateTimeFormat)
    {
        if (null === $dateTimeFormat) {
            return intval($dateTime->format('U')) * 1000;
        }
        return $dateTime->format($dateTimeFormat);
    }

    /**
     * Convert an array of possible DomainObject instances. The argument
     * $possibleDomainObjects could also an associative array representation
     * of another DomainObject - which means each value could potentially
     * be another DomainObject, an ObjectStorage of DomainObjects or a simple
     * value type. The type is checked and another recursive call is used to
     * convert any nested objects.
     *
     * @param array|DomainObjectInterface[]|\Traversable[] $domainObjects
     * @return DomainObjectInterface[]|array[]|string[]|\Traversable[]|null[]
     */
    protected static function recursiveArrayOfDomainObjectsToArray(
        array $domainObjects,
        bool $preventRecursion,
        ?string $recursionMarker
    ): array {
        foreach ($domainObjects as $key => $possibleDomainObject) {
            if ($possibleDomainObject instanceof DomainObjectInterface) {
                $domainObjects[$key] = static::recursiveDomainObjectToArray(
                    $possibleDomainObject,
                    $preventRecursion,
                    $recursionMarker
                );
            } elseif ($possibleDomainObject instanceof \Traversable) {
                $traversableAsArray = iterator_to_array($possibleDomainObject);
                $domainObjects[$key] = static::recursiveArrayOfDomainObjectsToArray(
                    $traversableAsArray,
                    $preventRecursion,
                    $recursionMarker
                );
            }
        }
        return $domainObjects;
    }

    /**
     * Convert a single DomainObject instance first to an array, then pass
     * that array through recursive DomainObject detection. This will convert
     * any 1:1, 1:n, n:1 and m:n relations.
     *
     * @param DomainObjectInterface $domainObject
     * @param boolean $preventRecursion
     * @param string $recursionMarker
     * @return array|string|null
     */
    protected static function recursiveDomainObjectToArray(
        DomainObjectInterface $domainObject,
        bool $preventRecursion,
        ?string $recursionMarker
    ) {
        $hash = spl_object_hash($domainObject);
        if ($preventRecursion && in_array($hash, static::$encounteredClasses)) {
            return $recursionMarker;
        }
        /** @var array $converted */
        $converted = ObjectAccess::getGettableProperties($domainObject);
        static::$encounteredClasses[] = $hash;
        $converted = static::recursiveArrayOfDomainObjectsToArray($converted, $preventRecursion, $recursionMarker);
        return $converted;
    }
}
