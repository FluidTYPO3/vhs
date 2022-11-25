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
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Fluid\Compatibility\TemplateParserBuilder;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
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
     * @var ObjectManagerInterface
     */
    protected $objectManager;

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
     * @return void
     */
    public function __wakeup()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->objectManager = $objectManager;
    }

    /**
     * @param ObjectManagerInterface $objectManager
     * @return void
     */
    public function injectObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
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
        /** @var ControllerContext $controllerContext */
        $controllerContext = $this->objectManager->get(ControllerContext::class);
        /** @var Request $request */
        $request = $this->objectManager->get(Request::class);
        $controllerContext->setRequest($request);

        /** @var UriBuilder $uriBuilder */
        $uriBuilder = $this->objectManager->get(UriBuilder::class);
        $uriBuilder->setRequest($request);
        $controllerContext->setUriBuilder($uriBuilder);

        if ($conf['controllerContext'] ?? false) {
            $request->setControllerActionName($conf['controllerContext']['actionName']);
            $request->setControllerExtensionName($conf['controllerContext']['extensionName']);
            $request->setControllerName($conf['controllerContext']['controllerName']);
            $request->setControllerObjectName($conf['controllerContext']['controllerObjectName']);
            $request->setPluginName($conf['controllerContext']['pluginName']);
            $request->setFormat($conf['controllerContext']['format']);
        }

        if (empty($partial)) {
            return '';
        }

        /** @var RenderingContext $renderingContext */
        $renderingContext = $this->objectManager->get(RenderingContext::class);
        $this->prepareContextsForUncachedRendering($renderingContext, $controllerContext);
        $this->setControllerContext($controllerContext);
        if (!empty($conf['partialRootPaths'])) {
            $templatePaths = $renderingContext->getTemplatePaths();
            $templatePaths->setPartialRootPaths($conf['partialRootPaths']);
        }
        return $this->renderPartialUncached($renderingContext, $partial, $section, $arguments);
    }

    /**
     * @param RenderingContextInterface $renderingContext
     * @param ControllerContext $controllerContext
     * @return void
     */
    protected function prepareContextsForUncachedRendering(
        RenderingContextInterface $renderingContext,
        ControllerContext $controllerContext
    ) {
        /** @var RenderingContext $renderingContext */
        $renderingContext->setControllerContext($controllerContext);
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
