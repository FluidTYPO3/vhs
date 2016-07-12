<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Implode ViewHelper
 *
 * Implodes an array or array-convertible object by $glue.
 */
class ImplodeViewHelper extends ExplodeViewHelper
{

    /**
     * @var string
     */
    protected $method = 'implode';

    /**
     * Initialize
     *
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->overrideArgument('content', 'string', 'Array or array-convertible object to be imploded by glue');
        $this->overrideArgument(
            'glue',
            'string',
            'String used as glue in the content to be imploded. Use glue value of "constant:NAMEOFCONSTANT" ' .
            '(fx "constant:LF" for linefeed as glue)',
            false,
            ','
        );
    }
}
