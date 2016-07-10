<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page\Menu;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * ### Page: Auto Sub Menu ViewHelper
 *
 * Recycles the parent menu ViewHelper instance, resetting the
 * page UID used as starting point and repeating rendering of
 * the exact same tag content.
 *
 * Used in custom menu rendering to indicate where a submenu is
 * to be rendered; accepts only a single argument called `pageUid`
 * which defines the new starting page UID that is used in the
 * recycled parent menu instance.
 *
 * DEPRECATED: use v:menu.sub instead
 *
 * @deprecated \FluidTYPO3\Vhs\ViewHelpers\Menu\SubViewHelper, remove in 4.0.0
 */
class SubViewHelper extends \FluidTYPO3\Vhs\ViewHelpers\Menu\SubViewHelper
{
}
