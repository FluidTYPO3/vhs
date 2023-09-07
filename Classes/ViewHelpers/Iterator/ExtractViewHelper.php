<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### Iterator / Extract VieWHelper
 *
 * Loop through the iterator and extract a key, optionally join the
 * results if more than one value is found.
 *
 * #### Extract values from an array by key
 *
 * The extbase version of indexed_search returns an array of the
 * previous search, which cannot easily be shown in the input field
 * of the result page. This can be solved.
 *
 * #### Input from extbase version of indexed_search">
 *
 * ```
 * [
 *     0 => [
 *         'sword' => 'firstWord',
 *         'oper' => 'AND'
 *     ],
 *     1 => [
 *         'sword' => 'secondWord',
 *         'oper' => 'AND'
 *     ],
 *     3 => [
 *         'sword' => 'thirdWord',
 *         'oper' => 'AND'
 *     ]
 * ]
 * ```
 *
 * Show the previous search words in the search form of the
 * result page:
 *
 * #### Example
 *
 * ```
 * <f:form.textfield name="search[sword]"
 *     value="{v:iterator.extract(key:'sword', content: searchWords) -> v:iterator.implode(glue: ' ')}"
 *     class="tx-indexedsearch-searchbox-sword" />
 * ```
 *
 * #### Get the names of several users
 *
 * Provided we have a bunch of FrontendUsers and we need to show
 * their firstname combined into a string:
 *
 * ```
 * <h2>Welcome
 * <v:iterator.implode glue=", "><v:iterator.extract key="firstname" content="frontendUsers" /></v:iterator.implode>
 * <!-- alternative: -->
 * {frontendUsers -> v:iterator.extract(key: 'firstname') -> v:iterator.implode(glue: ', ')}
 * </h2>
 * ```
 *
 * #### Output
 *
 * ```
 * <h2>Welcome Peter, Paul, Marry</h2>
 * ```
 *
 * #### Complex example
 *
 * ```
 * {anArray->v:iterator.extract(path: 'childProperty.secondNestedChildObject')
 *     -> v:iterator.sort(direction: 'DESC', sortBy: 'propertyOnSecondChild')
 *     -> v:iterator.slice(length: 10)->v:iterator.extract(key: 'uid')}
 * ```
 *
 * #### Single return value
 *
 * Outputs the "uid" value of the first record in variable $someRecords without caring if there are more than
 * one records. Always extracts the first value and then stops. Equivalent of changing -> v:iterator.first().
 *
 * ```
 * {someRecords -> v:iterator.extract(key: 'uid', single: TRUE)}
 * ```
 */
class ExtractViewHelper extends AbstractViewHelper
{
    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument(
            'content',
            'mixed',
            'The array or Iterator that contains either the value or arrays of values'
        );
        $this->registerArgument(
            'key',
            'string',
            'The name of the key from which you wish to extract the value',
            true
        );
        $this->registerArgument(
            'recursive',
            'boolean',
            'If TRUE, attempts to extract the key from deep nested arrays',
            false,
            true
        );
        $this->registerArgument(
            'single',
            'boolean',
            'If TRUE, returns only one value - always the first one - instead of an array of values',
            false,
            false
        );
    }

    /**
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        /** @var array $content */
        $content = $arguments['content'] ?? $renderChildrenClosure();
        /** @var string $key */
        $key = $arguments['key'];
        $recursive = (boolean) $arguments['recursive'];
        $single = (boolean) $arguments['single'];
        try {
            // extraction from Iterators could potentially use a getter method which throws
            // exceptions - although this would be bad practice. Catch the exception here
            // and turn it into a WARNING log message so that output does not break.
            if ($recursive) {
                $result = static::recursivelyExtractKey($content, $key);
            } else {
                $result = static::extractByKey($content, $key);
            }
        } catch (\Exception $error) {
            if (class_exists(LogManager::class)) {
                /** @var LogManager $logManager */
                $logManager = GeneralUtility::makeInstance(LogManager::class);
                $logManager->getLogger(__CLASS__)->warning($error->getMessage(), ['content' => $content]);
            } else {
                GeneralUtility::sysLog($error->getMessage(), 'vhs', GeneralUtility::SYSLOG_SEVERITY_WARNING);
            }
            $result = [];
        }

        if ($single && ($result instanceof \Traversable || is_array($result))) {
            return reset($result);
        }

        return $result;
    }

    /**
     * Extract by key
     *
     * @param mixed $iterator
     * @return mixed NULL or whatever we found at $key
     * @throws \Exception
     */
    protected static function extractByKey($iterator, string $key)
    {
        if (!is_array($iterator) && !$iterator instanceof \Traversable) {
            throw new \Exception('Traversable object or array expected but received ' . gettype($iterator), 1361532490);
        }

        $result = ObjectAccess::getPropertyPath($iterator, $key);

        return $result;
    }

    /**
     * Recursively extract the key
     *
     * @param mixed $iterator
     * @return array
     * @throws \Exception
     */
    protected static function recursivelyExtractKey($iterator, string $key)
    {
        if (!is_array($iterator) && !$iterator instanceof \Traversable) {
            throw new \Exception('Traversable object or array expected but received ' . gettype($iterator), 1515498714);
        }

        $content = [];

        foreach ($iterator as $v) {
            // Lets see if we find something directly:
            $result = ObjectAccess::getPropertyPath($v, $key);
            if (null !== $result) {
                $content[] = $result;
            } elseif (is_array($v) || $v instanceof \Traversable) {
                $content[] = static::recursivelyExtractKey($v, $key);
            }
        }

        $content = static::flattenArray($content);

        return $content;
    }

    /**
     * Flatten the result structure, to iterate it cleanly in fluid
     */
    protected static function flattenArray(array $content, array $flattened = []): array
    {
        if (empty($content)) {
            return $content;
        }

        foreach ($content as $sub) {
            if (is_array($sub)) {
                $flattened = static::flattenArray($sub, $flattened);
            } else {
                $flattened[] = $sub;
            }
        }

        return $flattened;
    }
}
