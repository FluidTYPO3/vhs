<?php
namespace FluidTYPO3\Vhs\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\ViewHelpers\Asset\AbstractAssetViewHelper;

/**
 * ### Basic Asset ViewHelper
 *
 * Places the contents of the asset (the tag body) directly
 * in the additional header content of the page. This most
 * basic possible version of an Asset has only the core
 * features shared by every Asset type:
 *
 * - a "name" attribute which is required, identifying the Asset
 *   by a lowerCamelCase or lowercase_underscored value, your
 *   preference (but lowerCamelCase recommended for consistency).
 * - a "dependencies" attribute with a CSV list of other named
 *   Assets upon which the current Asset depends. When used, this
 *   Asset will be included after every asset listed as dependency.
 * - a "group" attribute which is optional and is used ty further
 *   identify the Asset as belonging to a particular group which
 *   can be suppressed or manipulated through TypoScript. For
 *   example, the default value is "fluid" and if TypoScript is
 *   used to exclude the group "fluid" then any Asset in that
 *   group will simply not be loaded.
 * - an "overwrite" attribute which if enabled causes any existing
 *   asset with the same name to be overwritten with the current
 *   Asset instead. If rendered in a loop only the last instance
 *   is actually used (this allows Assets in Partials which are
 *   rendered in an f:for loop).
 * - a "debug" property which enables output of the information
 *   used by the current Asset, with an option to force debug
 *   mode through TypoScript.
 * - additional properties which affect how the Asset is processed.
 *   For a full list see the argument descriptions; the same
 *   settings can be applied through TypoScript per-Asset, globally
 *   or per-Asset-group.
 *
 * > Note: there are no static TypoScript templates for VHS but
 * > you will find a complete list in the README.md file in the
 * > root of the extension folder.
 *
 * @package Vhs
 * @subpackage ViewHelpers
 */
class AssetViewHelper extends AbstractAssetViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->overrideArgument(
            'standalone',
            'boolean',
            'If TRUE, excludes this Asset from any concatenation which may be applied',
            false,
            true
        );
    }
}
