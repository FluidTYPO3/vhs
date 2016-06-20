<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page\Menu;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * ### Page: Deferred menu rendering ViewHelper
 *
 * Place this ViewHelper inside any other ViewHelper which
 * has been configured with the `deferred` attribute set to
 * TRUE - this will cause the output of the parent to only
 * contain the content of this ViewHelper.
 *
 * DEPRECATED: use v:menu.deferred instead
 *
 * @deprecated use \FluidTYPO3\Vhs\ViewHelpers\Menu\DeferredViewHelper, remove in 4.0.0
 */
class DeferredViewHelper extends \FluidTYPO3\Vhs\ViewHelpers\Menu\DeferredViewHelper
{
}
