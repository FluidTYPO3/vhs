<?php
namespace FluidTYPO3\Vhs\Traits;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Class PageRecordViewHelperTrait
 *
 * Trait implemented by ViewHelpers which require page record
 * related arguments
 *
 */
trait PageRecordViewHelperTrait
{

    /**
     * Registers all page record related arguments required
     * to handle access restrictions and shortcuts.
     *
     * @return void
     */
    protected function registerPageRecordArguments()
    {
        $this->registerArgument(
            'showAccessProtected',
            'boolean',
            'If TRUE links to access protected pages are always rendered regardless of user login status',
            false,
            false
        );
        $this->registerArgument(
            'classAccessProtected',
            'string',
            'Optional class name to add to links which are access protected',
            false,
            'protected'
        );
        $this->registerArgument(
            'classAccessGranted',
            'string',
            'Optional class name to add to links which are access protected but access is actually granted',
            false,
            'access-granted'
        );
        $this->registerArgument(
            'useShortcutUid',
            'boolean',
            'If TRUE, substitutes the link UID of a shortcut with the target page UID (and thus avoiding ' .
            'redirects) but does not change other data - which is done by using useShortcutData.'
        );
        $this->registerArgument(
            'useShortcutTarget',
            'boolean',
            'Optional param for using shortcut target instead of shortcut itself for current link'
        );
        $this->registerArgument(
            'useShortcutData',
            'boolean',
            'Shortcut to set useShortcutTarget and useShortcutData simultaneously',
            false,
            false
        );
    }
}
