<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Asset\Compilable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\ViewHelpers\Asset\AssetInterface;

/**
 * Basic interface for compilable Assets, which are a sub
 * class of Assets that do not get included immediate but
 * rather are collected and processed in bulk by an
 * associated AssetCompilerInterface implementation. The
 * association between CompilableAsset
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Asset\Compilable
 */
interface CompilableAssetInterface extends AssetInterface {

	/**
	 * @return string
	 */
	public function getCompilerClassName();

}
