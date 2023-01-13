<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Once;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\RequestResolver;

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
    protected static function getIdentifier(array $arguments): string
    {
        if (isset($arguments['identifier']) && $arguments['identifier']) {
            return $arguments['identifier'];
        }

        $request = RequestResolver::resolveRequestFromRenderingContext(static::$currentRenderingContext);

        $identifier = implode('_', [
            $request->getControllerActionName(),
            $request->getControllerName(),
            $request->getPluginName(),
            $request->getControllerExtensionName()
        ]);
        return $identifier;
    }

    protected static function storeIdentifier(array $arguments): void
    {
        $identifier = static::getIdentifier($arguments);
        if (!isset($GLOBALS[static::class])) {
            $GLOBALS[static::class] = [];
        }
        $GLOBALS[static::class][$identifier] = true;
    }

    protected static function assertShouldSkip(array $arguments): bool
    {
        $identifier = static::getIdentifier($arguments);
        return isset($GLOBALS[static::class][$identifier]);
    }
}
