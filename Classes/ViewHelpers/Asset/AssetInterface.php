<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Asset;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Basic interface which must be implemented by every
 * possible Asset type.
 */
interface AssetInterface
{

    /**
     * Render method
     *
     * @return void
     */
    public function render();

    /**
     * Build this asset. Override this method in the specific
     * implementation of an Asset in order to:
     *
     * - if necessary compile the Asset (LESS, SASS, CoffeeScript etc)
     * - make a final rendering decision based on arguments
     *
     * Note that within this function the ViewHelper and TemplateVariable
     * Containers are not dependable, you cannot use the ControllerContext
     * and RenderingContext and you should therefore also never call
     * renderChildren from within this function. Anything else goes; CLI
     * commands to build, caching implementations - you name it.
     *
     * @return mixed
     */
    public function build();

    /**
     * @return array
     */
    public function getDependencies();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return array
     */
    public function getVariables();

    /**
     * Returns the settings used by this particular Asset
     * during inclusion. Public access allows later inspection
     * of the TypoScript values which were applied to the Asset.
     *
     * @return array
     */
    public function getSettings();

    /**
     * @return array
     */
    public function getAssetSettings();

    /**
     * Allows public access to debug this particular Asset
     * instance later, when including the Asset in the page.
     *
     * @return array
     */
    public function getDebugInformation();

    /**
     * Returns TRUE if settings specify that the source of this
     * Asset should be rendered as if it were a Fluid template,
     * using variables from the "arguments" attribute
     *
     * @return boolean
     */
    public function assertFluidEnabled();

    /**
     * Returns TRUE if settings specify that the name of each Asset
     * should be placed above the built content when placed in merged
     * Asset cache files.
     *
     * @return boolean
     */
    public function assertAddNameCommentWithChunk();

    /**
     * Returns TRUE if the current Asset should be debugged as commanded
     * by settings in TypoScript and/or ViewHelper attributes.
     *
     * @return boolean
     */
    public function assertDebugEnabled();

    /**
     * @return boolean
     */
    public function assertAllowedInFooter();

    /**
     * @return boolean
     */
    public function assertHasBeenRemoved();
}
