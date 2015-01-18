<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Asset\Compilable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\ViewHelpers\Asset\AbstractAssetViewHelper;

/**
 * Base class for Assets which must be compiled by an
 * AssetCompiler.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Asset\Compilable
 */
abstract class AbstractCompilableAssetViewHelper
	extends AbstractAssetViewHelper
	implements CompilableAssetInterface {

	/**
	 * Target compiler class name. Override this with
	 * a class or interface name in order to connect
	 * your custom CompilableAsset implementation to a
	 * specific AssetCompiler class (which must be
	 * registered in the template as well).
	 *
	 * @var string
	 */
	protected $compilerClassName = NULL;

	/**
	 * @return string
	 */
	public function getCompilerClassName() {
		return $this->compilerClassName;
	}

}
