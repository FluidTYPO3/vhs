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


    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument(
            'async',
            'boolean',
            'If TRUE, adds "async" attribute to script tag (only works when standalone is set)',
            false,
            false
        );
        $this->registerArgument(
            'defer',
            'boolean',
            'If TRUE, adds "defer" attribute to script tag (only works when standalone is set)',
            false,
            false
        );
    }
}
