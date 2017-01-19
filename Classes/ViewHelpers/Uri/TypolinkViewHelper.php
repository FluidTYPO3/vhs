<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Uri;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\ViewHelpers\Uri\TypolinkViewHelper as FluidTypolinkViewHelper;

/**
 * ### TypolinkViewhelper
 *
 * Renders a uri with the TypoLink function.
 * Can be used with the LinkWizard
 *
 * For more info on the typolink function, please consult the offical core-documentation:
 * http://docs.typo3.org/typo3cms/TyposcriptIn45MinutesTutorial/TypoScriptFunctions/Typolink/Index.html
 *
 * DEPRECATED: Use TYPO3\CMS\Fluid\ViewHelpers\Uri\TypolinkViewHelper instead
 *
 * ### Examples
 *
 *     <!-- tag -->
 *     <v:uri.typolink configuration="{typoLinkConfiguration}" />
 *     <v:uri.typolink configuration="{object}" />
 *     <!-- with a {parameter} variable containing the PID -->
 *     <v:uri.typolink configuration="{parameter: parameter}" />
 *     <!-- with a {fields.link} variable from the LinkWizard inside a flux form -->
 *     <v:uri.typolink configuration="{parameter: fields.link}" />
 *     <!-- same with a {page} variable from fluidpages -->
 *     <v:uri.typolink configuration="{parameter: page.uid}" />
 *     <!-- With extensive configuration -->
 *     <v:uri.typolink configuration="{parameter: page.uid, additionalParams: '&print=1'}" />
 *
 * @deprecated Use TYPO3\CMS\Fluid\ViewHelpers\Uri\TypolinkViewHelper, remove in 4.0.0
 */
class TypolinkViewHelper extends AbstractViewHelper implements CompilableInterface
{
    use CompileWithRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * Initializes the arguments for the ViewHelper
     */
    public function initializeArguments()
    {
        $this->registerArgument('configuration', 'array', 'The typoLink configuration', true);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        GeneralUtility::deprecationLog(
            'Deprecated TypoLinkViewHelper from VHS was used. Please use ' .
            'TYPO3\CMS\Fluid\ViewHelpers\Uri\TypolinkViewHelper instead.'
        );
        return FluidTypolinkViewHelper::renderStatic(
            $arguments['configuration'],
            $renderChildrenClosure,
            $renderingContext
        );
    }
}
