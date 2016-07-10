<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Asset;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * ### Basic Script ViewHelper
 *
 * Allows inserting a `<script>` Asset. Settings specify
 * where to insert the Asset and how to treat it.
 *
 * @package Vhs
 * @subpackage ViewHelpers\Asset
 */
class ScriptViewHelper extends AbstractAssetViewHelper
{

    /**
     * @var string
     */
    protected $type = 'js';
}
