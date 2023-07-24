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
use FluidTYPO3\Vhs\View\UncacheTemplateView;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
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

    public function initializeArguments(): void
    {
        $this->registerArgument('partial', 'string', 'Reference to a partial.', true);
        $this->registerArgument('section', 'string', 'Name of section inside the partial to render.');
        $this->registerArgument('arguments', 'array', 'Arguments to pass to the partial.');
        $this->registerArgument(
            'persistPartialPaths',
            'bool',
            'Normally, v:render.uncache will persist the partialRootPaths array that was active when the ViewHelper' .
            'was called, so the exact paths will be reused when rendering the uncached portion of the page output. ' .
            'This is done to ensure that even if you manually added some partial paths through some dynamic means (' .
            'for example, based on a controller argument) then those paths would be used. However, in some cases ' .
            'this will be undesirable - namely when using a cache that is shared between multiple TYPO3 instances ' .
            'and each instance has a different path in the server\'s file system (e.g. load balanced setups). ' .
            'On such setups you should set persistPartialPaths="0" on this ViewHelper to prevent it from caching ' .
            'the resolved partialRootPaths. The ViewHelper will then instead use whichever partialRootPaths are ' .
            'configured for the extension that calls `v:render.uncache`. Note that when this is done, the special ' .
            'use case of dynamic or controller-overridden partialRootPaths is simply not supported.',
            false,
            true
        );
    }

    /**
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
        if (!is_array($partialArguments)) {
            $partialArguments = (array) $partialArguments;
        }
        if (!isset($partialArguments['settings']) && $templateVariableContainer->exists('settings')) {
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

        $conf = [
            'userFunc' => UncacheTemplateView::class . '->callUserFunction',
            'partial' => $arguments['partial'],
            'section' => $arguments['section'],
            'arguments' => $partialArguments,
            'controllerContext' => $extbaseParameters,
        ];

        if ($arguments['persistPartialPaths'] ?? true) {
            $conf['partialRootPaths'] = $renderingContext->getTemplatePaths()->getPartialRootPaths();
        }

        /** @var ContentObjectRenderer $contentObjectRenderer */
        $contentObjectRenderer = $GLOBALS['TSFE']->cObj;

        $content = $contentObjectRenderer->cObjGetSingle(
            'COA_INT',
            [
                '10' => 'USER',
                '10.' => $conf,
            ]
        );
        return $content;
    }
}
