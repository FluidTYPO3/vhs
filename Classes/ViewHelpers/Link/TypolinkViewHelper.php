<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Link;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\DefaultRenderMethodViewHelperTrait;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### TypolinkViewhelper
 *
 * Renders a link with the TypoLink function.
 * Can be used with the LinkWizard
 *
 * For more info on the typolink function, please consult the offical core-documentation:
 * http://docs.typo3.org/typo3cms/TyposcriptIn45MinutesTutorial/TypoScriptFunctions/Typolink/Index.html
 *
 * ### Examples
 *
 *     <!-- tag -->
 *     <v:link.typolink configuration="{typoLinkConfiguration}" />
 *     <v:link.typolink configuration="{object}">My LinkText</v:link.typolink>
 *     <!-- with a {parameter} variable containing the PID -->
 *     <v:link.typolink configuration="{parameter: parameter}" />
 *     <!-- with a {fields.link} variable from the LinkWizard (incl. 'class', 'target' etc.) inside a flux form -->
 *     <v:link.typolink configuration="{parameter: fields.link}" />
 *     <!-- same with a {page} variable from fluidpages -->
 *     <v:link.typolink configuration="{parameter: page.uid}" />
 *     <!-- With extensive configuration -->
 *     <v:link.typolink configuration="{parameter: page.uid, additionalParams: '&print=1', title: 'Follow the link'}">Click Me!</v:link.typolink>
 *
 * @author Cedric Ziel <cedric@cedric-ziel.com>, Cedric Ziel - Internetdienstleistungen & EDV
 */
class TypolinkViewHelper extends AbstractViewHelper
{

    use DefaultRenderMethodViewHelperTrait;

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
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        return $GLOBALS['TSFE']->cObj->typoLink($renderChildrenClosure(), $arguments['configuration']);
    }
}
