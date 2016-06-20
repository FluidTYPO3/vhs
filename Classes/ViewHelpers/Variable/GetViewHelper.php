<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### Variable: Get
 *
 * ViewHelper used to read the value of a current template
 * variable. Can be used with dynamic indices in arrays:
 *
 *     <v:variable.get name="array.{dynamicIndex}" />
 *     <v:variable.get name="array.{v:variable.get(name: 'arrayOfSelectedKeys.{indexInArray}')}" />
 *     <f:for each="{v:variable.get(name: 'object.arrayProperty.{dynamicIndex}')}" as="nestedObject">
 *         ...
 *     </f:for>
 *
 * Or to read names of variables which contain dynamic parts:
 *
 *     <!-- if {variableName} is "Name", outputs value of {dynamicName} -->
 *     {v:variable.get(name: 'dynamic{variableName}')}
 *
 * If your target object is an array with unsequential yet
 * numeric indices (e.g. {123: 'value1', 513: 'value2'},
 * commonly seen in reindexed UID map arrays) use
 * `useRawIndex="TRUE"` to indicate you do not want your
 * array/QueryResult/Iterator to be accessed by locating
 * the Nth element - which is the default behavior.
 *
 * ```warning
 * Do not try `useRawKeys="TRUE"` on QueryResult or
 * ObjectStorage unless you are fully aware what you are
 * doing. These particular types require an unpredictable
 * index value - the SPL object hash value - when accessing
 * members directly. This SPL indexing and the very common
 * occurrences of QueryResult and ObjectStorage variables
 * in templates is the very reason why `useRawKeys` by
 * default is set to `FALSE`.
 * ```
 */
class GetViewHelper extends AbstractViewHelper
{

    /**
     * @param string $name
     * @param boolean $useRawKeys
     * @return mixed
     */
    public function render($name, $useRawKeys = false)
    {
        if (false === strpos($name, '.')) {
            if (true === $this->templateVariableContainer->exists($name)) {
                return $this->templateVariableContainer->get($name);
            }
        } else {
            $segments = explode('.', $name);
            $lastSegment = array_shift($segments);
            $templateVariableRootName = $lastSegment;
            if (true === $this->templateVariableContainer->exists($templateVariableRootName)) {
                $templateVariableRoot = $this->templateVariableContainer->get($templateVariableRootName);
                if (true === $useRawKeys) {
                    return ObjectAccess::getPropertyPath($templateVariableRoot, implode('.', $segments));
                }
                try {
                    $value = $templateVariableRoot;
                    foreach ($segments as $segment) {
                        if (true === ctype_digit($segment)) {
                            $segment = intval($segment);
                            $index = 0;
                                // Note: this loop approach is not a stupid solution. If you doubt this,
                                // attempt to feth a number at a numeric index from ObjectStorage ;)
                            foreach ($value as $possibleValue) {
                                if ($index === $segment) {
                                    $value = $possibleValue;
                                    break;
                                }
                                ++ $index;
                            }
                            continue;
                        }
                        $value = ObjectAccess::getProperty($value, $segment);
                    }
                    return $value;
                } catch (\Exception $e) {
                    return null;
                }
            }
        }
        return null;
    }
}
