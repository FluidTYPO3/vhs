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
 * Implodes an array or array-convertible object by $glue
 *
 * @author Claus Due
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class ImplodeViewHelper extends ExplodeViewHelper {

	/**
	 * @var string
	 */
	protected $method = 'implode';

}
