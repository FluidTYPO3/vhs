<?php
namespace FluidTYPO3\Vhs\View;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Fluid\Compatibility\TemplateParserBuilder;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextFactory;
use TYPO3\CMS\Fluid\View\TemplateView;
use TYPO3Fluid\Fluid\Core\Compiler\TemplateCompiler;
use TYPO3Fluid\Fluid\Core\Parser\TemplateParser;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Uncache Template View
 */
class UncacheTemplateView extends TemplateView
{
    /**
     * @var TemplateParser|\TYPO3Fluid\Fluid\Core\Parser\TemplateParser
     */
    protected $templateParser;

    /**
     * @var TemplateCompiler|\TYPO3Fluid\Fluid\Core\Compiler\TemplateCompiler
     */
    protected $templateCompiler;

    /**
     * @return array
     */
    public function __sleep()
    {
        return ['renderingStack'];
    }

    /**
     * @param string $postUserFunc
     * @param array $conf
     * @param string $content
     * @return string|null
     */
    public function callUserFunction($postUserFunc, $conf, $content)
    {
        $partial = $conf['partial'] ?? null;
        $section = $conf['section'] ?? null;
        $arguments = $conf['arguments'] ?? [];
        $parameters = $conf['controllerContext'] ?? null;

        if (empty($partial)) {
            return '';
        }

        if (class_exists(ExtbaseRequestParameters::class)) {
            /** @var RenderingContextFactory $renderingContextFactory */
            $renderingContextFactory = GeneralUtility::makeInstance(RenderingContextFactory::class);
            /** @var RenderingContext $renderingContext */
            $renderingContext = $renderingContextFactory->create();

            if (method_exists($renderingContext, 'setRequest')) {
                $renderingContext->setRequest(
                    new Request($GLOBALS['TYPO3_REQUEST']->withAttribute('extbase', $parameters))
                );
            }
        } else {
            /** @var ControllerContext $controllerContext */
            $controllerContext = GeneralUtility::makeInstance(ControllerContext::class);
            /** @var Request $request */
            $request = GeneralUtility::makeInstance(Request::class);
            $controllerContext->setRequest($request);

            /** @var UriBuilder $uriBuilder */
            $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
            $uriBuilder->setRequest($request);
            $controllerContext->setUriBuilder($uriBuilder);

            if ($parameters) {
                if (method_exists($request, 'setControllerActionName')) {
                    $request->setControllerActionName($parameters['actionName']);
                }

                if (method_exists($request, 'setControllerExtensionName')) {
                    $request->setControllerExtensionName($parameters['extensionName']);
                }

                if (method_exists($request, 'setControllerName')) {
                    $request->setControllerName($parameters['controllerName']);
                }

                if (method_exists($request, 'setControllerObjectName')) {
                    $request->setControllerObjectName($parameters['controllerObjectName']);
                }

                if (method_exists($request, 'setPluginName')) {
                    $request->setPluginName($parameters['pluginName']);
                }

                if (method_exists($request, 'setFormat')) {
                    $request->setFormat($parameters['format']);
                }
            }

            /** @var RenderingContext $renderingContext */
            $renderingContext = GeneralUtility::makeInstance(RenderingContext::class);
        }

        $this->prepareContextsForUncachedRendering($renderingContext);
        if (!empty($conf['partialRootPaths'])) {
            $templatePaths = $renderingContext->getTemplatePaths();
            $templatePaths->setPartialRootPaths($conf['partialRootPaths']);
        }
        return $this->renderPartialUncached($renderingContext, $partial, $section, $arguments);
    }

    protected function prepareContextsForUncachedRendering(RenderingContextInterface $renderingContext): void
    {
        $this->setRenderingContext($renderingContext);
        $this->templateParser = $renderingContext->getTemplateParser();
        $this->templateCompiler = $renderingContext->getTemplateCompiler();
    }

    /**
     * @param RenderingContextInterface $renderingContext
     * @param string $partial
     * @param string|null $section
     * @param array $arguments
     * @return string|null
     */
    protected function renderPartialUncached(
        RenderingContextInterface $renderingContext,
        $partial,
        $section = null,
        array $arguments = []
    ) {
        $this->renderingStack[] = [
            'type' => static::RENDERING_TEMPLATE,
            'parsedTemplate' => null,
            'renderingContext' => $renderingContext,
        ];
        $rendered = $this->renderPartial($partial, $section, $arguments);
        array_pop($this->renderingStack);
        return $rendered;
    }
}
