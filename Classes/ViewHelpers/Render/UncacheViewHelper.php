<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Render;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\RequestResolver;
use FluidTYPO3\Vhs\View\UncacheContentObject;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Uncaches partials. Use like ``f:render``.
 * The partial will then be rendered each time.
 * Please be aware that this will impact render time.
 * Arguments must be serializable and will be cached.
 */
class UncacheViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * Initialize
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('partial', 'string', 'Reference to a partial.', true);
        $this->registerArgument('section', 'string', 'Name of section inside the partial to render.', false, null);
        $this->registerArgument('arguments', 'array', 'Arguments to pass to the partial.', false, null);
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
        /** @var RenderingContext $renderingContext */
        $templateVariableContainer = $renderingContext->getVariableProvider();
        $partialArguments = $arguments['arguments'];
        if (false === is_array($partialArguments)) {
            $partialArguments = [];
        }
        if (false === isset($partialArguments['settings']) && true === $templateVariableContainer->exists('settings')) {
            $partialArguments['settings'] = $templateVariableContainer->get('settings');
        }

        $substKey = 'INT_SCRIPT.' . $GLOBALS['TSFE']->uniqueHash();
        $content = '<!--' . $substKey . '-->';

        $request = RequestResolver::resolveRequestFromRenderingContext($renderingContext);

        if (class_exists(ExtbaseRequestParameters::class) && method_exists($request, 'getAttribute')) {
            /** @var ExtbaseRequestParameters $extbaseParameters */
            $extbaseParameters = $request->getAttribute('extbase');
        } else {
            $extbaseParameters = [
                'actionName' => $request->getControllerActionName(),
                'extensionName' => $request->getControllerExtensionName(),
                'controllerName' => $request->getControllerName(),
                'controllerObjectName' => $request->getControllerObjectName(),
                'pluginName' => $request->getPluginName(),
                'format' => $request->getFormat(),
            ];
        }

        $GLOBALS['TSFE']->config['INTincScript'][$substKey] = [
            'type' => 'POSTUSERFUNC',
            'cObj' => serialize(GeneralUtility::makeInstance(UncacheContentObject::class, $GLOBALS['TSFE']->cObj)),
            'postUserFunc' => 'render',
            'conf' => [
                'partial' => $arguments['partial'],
                'section' => $arguments['section'],
                'arguments' => $partialArguments,
                'partialRootPaths' => $renderingContext->getTemplatePaths()->getPartialRootPaths(),
                'controllerContext' => $extbaseParameters,
            ],
            'content' => $content
        ];

        return $content;
    }
}
