<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Asset\Compilable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\ViewHelpers\Asset\AbstractAssetViewHelper;
use FluidTYPO3\Vhs\ViewHelpers\Asset\AssetInterface;

/**
 * Base class for ViewHelpers capable of compiling Assets,
 * aka. AssetCompilers. Contains a few base methods to handle
 * CompilableAssets - but there are
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Asset\Compilable
 */
abstract class AbstractAssetCompilerViewHelper
	extends AbstractAssetViewHelper
	implements AssetCompilerInterface {

	/**
	 * @var array
	 */
	protected $assets = array();

	/**
	 * @param AssetInterface $asset
	 * @return void
	 */
	public function addAsset(AssetInterface $asset) {
		$name = $asset->getName();
		$this->assets[$name] = $asset;
	}

	/**
	 * @return AssetInterface[]
	 */
	public function getAssets() {
		return $this->assets;
	}

	/**
	 * @return string
	 */
	public function build() {
		return implode("\n", $this->assets);
	}

}
