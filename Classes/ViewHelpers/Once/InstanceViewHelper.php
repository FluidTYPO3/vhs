<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Once;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Once: Instance
 *
 * Displays nested content or "then" child once per instance
 * of the content element or plugin being rendered, as identified
 * by the contentObject UID (or globally if no contentObject
 * is associated).
 *
 * "Once"-style ViewHelpers are purposed to only display their
 * nested content once per XYZ, where the XYZ depends on the
 * specific type of ViewHelper (session, cookie etc).
 *
 * In addition the ViewHelper is a ConditionViewHelper, which
 * means you can utilize the f:then and f:else child nodes as
 * well as the "then" and "else" arguments.
 */
class InstanceViewHelper extends AbstractOnceViewHelper
{

    /**
     * @return string
     */
    protected function getIdentifier()
    {
        if (true === isset($this->arguments['identifier']) && null !== $this->arguments['identifier']) {
            return $this->arguments['identifier'];
        }
        $request = $this->controllerContext->getRequest();
        $identifier = implode('_', [
            $request->getControllerActionName(),
            $request->getControllerName(),
            $request->getPluginName(),
            $request->getControllerExtensionName()
        ]);
        return $identifier;
    }

    /**
     * @return void
     */
    protected function storeIdentifier()
    {
        $index = get_class($this);
        $identifier = $this->getIdentifier();
        if (false === is_array($GLOBALS[$index])) {
            $GLOBALS[$index] = [];
        }
        $GLOBALS[$index][$identifier] = true;
    }

    /**
     * @return boolean
     */
    protected function assertShouldSkip()
    {
        $index = get_class($this);
        $identifier = $this->getIdentifier();
        return isset($GLOBALS[$index][$identifier]);
    }
}
