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
 * Basic interface for ViewHelpers whose purpose it is to
 * compile other Asset type ViewHelpers. Each Compiler's
 * class name or topmost interface name is used in other
 * CompilableAssets to indicate that the CompilableAsset
 * should be compiled by this particular Compiler.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Asset\Compilable
 */
interface AssetCompilerInterface extends AssetInterface {

	/**
	 * @param AssetInterface $asset
	 * @return void
	 */
	public function addAsset(AssetInterface $asset);

	/**
	 * @return AssetInterface[]
	 */
	public function getAssets();

}
