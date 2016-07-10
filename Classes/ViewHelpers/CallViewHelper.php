<?php
namespace FluidTYPO3\Vhs\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### Call ViewHelper
 *
 * Calls a method on an existing object. Usable as inline or tag.
 *
 * ### Examples
 *
 *     <!-- inline, useful as argument, for example in f:for -->
 *     {object -> v:call(method: 'toArray')}
 *     <!-- tag, useful to quickly output simple values -->
 *     <v:call object="{object}" method="unconventionalGetter" />
 *     <v:call method="unconventionalGetter">{object}</v:call>
 *     <!-- arguments for the method -->
 *     <v:call object="{object}" method="doSomethingWithArguments" arguments="{0: 'foo', 1: 'bar'}" />
 */
class CallViewHelper extends AbstractViewHelper
{

    /**
     * @param string $method
     * @param object $object
     * @param array $arguments
     * @throws \RuntimeException
     * @return mixed
     */
    public function render($method, $object = null, array $arguments = [])
    {
        if (null === $object) {
            $object = $this->renderChildren();
            if (false === is_object($object)) {
                throw new \RuntimeException(
                    'Using v:call requires an object either as "object" attribute, tag content or inline argument',
                    1356849652
                );
            }
        }
        if (false === method_exists($object, $method)) {
            throw new \RuntimeException(
                'Method "' . $method . '" does not exist on object of type ' . get_class($object),
                1356834755
            );
        }
        return call_user_func_array(array($object, $method), $arguments);
    }
}
