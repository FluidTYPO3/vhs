<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\ViewHelperUtility;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * ### Variable: Set
 *
 * Sets a single variable in the TemplateVariableContainer
 * scope. The variable then becomes accessible as {var}.
 *
 * Combines well with `v:variable.get` to set shorter variable
 * names referencing dynamic variables, such as:
 *
 *     <v:variable.set name="myObject" value="{v:variable.get(name: 'arrayVariable.{offset}')}" />
 *     <!-- If {index} == 4 then {myObject} is now == {arrayVariable.4} -->
 *     {myObject.name} <!-- corresponds to {arrayVariable.4.name} -->
 *
 * Note that `{arrayVariable.{offset}.name}` is not possible
 * due to the way Fluid parses nodes; the above piece of
 * code would try reading `arrayVariable.{offset}.name`
 * as a variable actually called "arrayVariable.{offset}.name"
 * rather than the correct `arrayVariable[offset][name]`.
 *
 * In many ways this ViewHelper works like `f:alias`
 * with one exception: in `f:alias` the variable only
 * becomes accessible in the tag content, whereas `v:variable.set`
 * inserts the variable in the template and leaves it there
 * (it "leaks" the variable).
 *
 * If $name contains a dot, VHS will attempt to load the object
 * stored under the named used as the first segment part and
 * set the value at the remaining path. E.g.
 * `{value -> v:variable.set(name: 'object.property.subProperty')}`
 * would attempt to load `{object}` first, then set
 * `property.subProperty` on that object/array using
 * ObjectAccess::setPropertyPath(). If `{object}` is not
 * an object or an array, the variable will not be set. Please
 * note: Extbase does not currently support setting variables
 * deeper than two levels, meaning a `name` of fx `foo.bar.baz`
 * will be ignored. To set values deeper than two levels you
 * must first extract the second-level object then set the
 * value on that object.
 *
 * Using as `{value -> v:variable.set(name: 'myVar')}` makes `{myVar}` contain
 * `{value}`.
 */
class SetViewHelper extends AbstractViewHelper implements CompilableInterface
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('value', 'mixed', 'Value to set');
        $this->registerArgument('name', 'string', 'Name of variable to assign');
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $name = $arguments['name'];
        $value = $renderChildrenClosure();
        $variableProvider = ViewHelperUtility::getVariableProviderFromRenderingContext($renderingContext);
        if (false === strpos($name, '.')) {
            if (true === $variableProvider->exists($name)) {
                $variableProvider->remove($name);
            }
            $variableProvider->add($name, $value);
        } elseif (1 === mb_substr_count($name, '.')) {
            $parts = explode('.', $name);
            $objectName = array_shift($parts);
            $path = implode('.', $parts);
            if (false === $variableProvider->exists($objectName)) {
                return null;
            }
            $object = $variableProvider->get($objectName);
            try {
                ObjectAccess::setProperty($object, $path, $value);
                // Note: re-insert the variable to ensure unreferenced values like arrays also get updated
                $variableProvider->remove($objectName);
                $variableProvider->add($objectName, $object);
            } catch (\Exception $error) {
                return null;
            }
        }
        return null;
    }
}
