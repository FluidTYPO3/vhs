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
     */
    public function build(): ?string;

    public function getDependencies(): array;
    public function getType(): string;
    public function getName(): string;
    public function getVariables(): array;

    /**
     * Returns the settings used by this particular Asset
     * during inclusion. Public access allows later inspection
     * of the TypoScript values which were applied to the Asset.
     */
    public function getSettings(): array;

    public function getAssetSettings(): array;

    /**
     * Allows public access to debug this particular Asset
     * instance later, when including the Asset in the page.
     */
    public function getDebugInformation(): array;

    /**
     * Returns TRUE if settings specify that the source of this
     * Asset should be rendered as if it were a Fluid template,
     * using variables from the "arguments" attribute.
     */
    public function assertFluidEnabled(): bool;

    /**
     * Returns TRUE if settings specify that the name of each Asset
     * should be placed above the built content when placed in merged
     * Asset cache files.
     */
    public function assertAddNameCommentWithChunk(): bool;

    /**
     * Returns TRUE if the current Asset should be debugged as commanded
     * by settings in TypoScript and/or ViewHelper attributes.
     */
    public function assertDebugEnabled(): bool;

    public function assertAllowedInFooter(): bool;
    public function assertHasBeenRemoved(): bool;
}
