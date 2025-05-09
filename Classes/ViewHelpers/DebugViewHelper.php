<?php
namespace FluidTYPO3\Vhs\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Reflection\Exception\PropertyNotAccessibleException;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\NodeInterface;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ObjectAccessorNode;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### ViewHelper Debug ViewHelper (sic)
 *
 * Debugs instances of other ViewHelpers and language
 * structures. Use in conjunction with other ViewHelpers
 * to inspect their current and possible arguments and
 * render their documentation:
 *
 * ```
 * <v:debug><f:format.html>{variable}</f:format.html></v:debug>
 * ```
 *
 * Or the same expression in inline syntax:
 *
 * ```
 * {variable -> f:format.html() -> v:debug()}
 * ```
 *
 * Can also be used to inspect `ObjectAccessor` instances
 * (e.g. variables you try to access) and rather than just
 * dumping the entire contents of the variable as is done
 * by `<f:debug />`, this ViewHelper makes a very simple
 * dump with a warning if the variable is not defined. If
 * an object is encountered (for example a domain object)
 * this ViewHelper will not dump the object but instead
 * will scan it for accessible properties (e.g. properties
 * which have a getter method!) and only present those
 * properties which can be accessed, along with the type
 * of variable that property currently contains:
 *
 * ```
 * {domainObject -> v:debug()}
 * ```
 *
 * Assuming that `{domainObject}` is an instance of an
 * object which has two methods: `getUid()` and `getTitle()`,
 * debugging that instance will render something like this
 * in plain text:
 *
 * ```
 * Path: {domainObject}
 * Value type: object
 * Accessible properties on {domainObject}:
 *    {form.uid} (integer)
 *    {form.title} (string)
 * ```
 *
 * The class itself can contain any number of protected
 * properties, but only those which have a getter method
 * can be accessed by Fluid and as therefore we only dump
 * those properties which you **can in fact access**.
 */
class DebugViewHelper extends AbstractViewHelper
{
    /**
     * @var ViewHelperNode[]
     */
    protected array $childViewHelperNodes = [];

    /**
     * @var ObjectAccessorNode[]
     */
    protected array $childObjectAccessorNodes = [];

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * @return string
     */
    public function render()
    {
        $nodes = [];
        foreach ($this->childViewHelperNodes as $viewHelperNode) {
            $viewHelper = $viewHelperNode->getUninitializedViewHelper();
            $arguments = $viewHelper->prepareArguments();
            $givenArguments = $viewHelperNode->getArguments();
            $viewHelperReflection = new \ReflectionClass($viewHelper);
            $viewHelperDescription = $viewHelperReflection->getDocComment();
            $viewHelperDescription = htmlentities((string) $viewHelperDescription);
            $viewHelperDescription = '[CLASS DOC]' . LF . $viewHelperDescription . LF;
            $renderMethodDescription = $viewHelperReflection->getMethod('render')->getDocComment();
            $renderMethodDescription = htmlentities((string) $renderMethodDescription);
            $renderMethodDescription = implode(LF, array_map('trim', explode(LF, $renderMethodDescription)));
            $renderMethodDescription = '[RENDER METHOD DOC]' . LF . $renderMethodDescription . LF;
            $argumentDefinitions = [];
            foreach ($arguments as $argument) {
                $name = $argument->getName();
                $argumentDefinitions[$name] = ObjectAccess::getGettableProperties($argument);
            }
            $sections = [
                $viewHelperDescription,
                DebuggerUtility::var_dump($argumentDefinitions, '[ARGUMENTS]', 4, true, false, true),
                DebuggerUtility::var_dump($givenArguments, '[CURRENT ARGUMENTS]', 4, true, false, true),
                $renderMethodDescription
            ];
            $nodes[] = implode(LF, $sections);
        }
        if (0 < count($this->childObjectAccessorNodes)) {
            $nodes[] = '[VARIABLE ACCESSORS]';
            /** @var array|object $templateVariables */
            $templateVariables = $this->renderingContext->getVariableProvider()->getAll();
            foreach ($this->childObjectAccessorNodes as $objectAccessorNode) {
                $path = $objectAccessorNode->getObjectPath();
                $segments = explode('.', $path);
                try {
                    $value = ObjectAccess::getProperty($templateVariables, array_shift($segments));
                    foreach ($segments as $segment) {
                        if (!is_array($value) && !is_object($value)) {
                            break;
                        }
                        $value = ObjectAccess::getProperty($value, $segment);
                    }
                    $type = gettype($value);
                } catch (PropertyNotAccessibleException $error) {
                    $value = null;
                    $type = 'UNDEFINED/INACCESSIBLE';
                }
                $sections = [
                    'Path: {' . $path . '}',
                    'Value type: ' . $type,
                ];
                if (is_object($value)) {
                    $sections[] = 'Accessible properties on {' . $path . '}:';
                    $gettable = ObjectAccess::getGettablePropertyNames($value);
                    unset($gettable[0]);
                    foreach ($gettable as $gettableProperty) {
                        $sections[] = '   {' . $path . '.' . $gettableProperty . '} (' .
                            gettype(ObjectAccess::getProperty($value, $gettableProperty)) . ')';
                    }
                } elseif (null !== $value) {
                    $sections[] = DebuggerUtility::var_dump(
                        $value,
                        'Dump of variable "' . $path . '"',
                        4,
                        true,
                        false,
                        true
                    );
                }
                $nodes[] = implode(LF, $sections);
            }
        }
        return '<pre>' . implode(LF . LF, $nodes) . '</pre>';
    }

    /**
     * Sets the direct child nodes of the current syntax tree node.
     *
     * @param NodeInterface[] $childNodes
     */
    public function setChildNodes(array $childNodes): void
    {
        foreach ($childNodes as $childNode) {
            if ($childNode instanceof ViewHelperNode) {
                $this->childViewHelperNodes[] = $childNode;
            } elseif ($childNode instanceof ObjectAccessorNode) {
                $this->childObjectAccessorNodes[] = $childNode;
            }
        }
    }
}
