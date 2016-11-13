<?php
namespace FluidTYPO3\Vhs\View;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Utility\ArrayUtility;
use TYPO3\CMS\Fluid\Compatibility\TemplateParserBuilder;
use TYPO3\CMS\Fluid\Core\Compiler\TemplateCompiler;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\View\TemplateView;

/**
 * Uncache Template View
 */
class UncacheTemplateView extends TemplateView
{

    /**
     * @param string $postUserFunc
     * @param array $conf
     * @param string $content
     * @return string
     */
    public function callUserFunction($postUserFunc, $conf, $content)
    {
        $partial = $conf['partial'];
        $section = $conf['section'];
        $arguments = true === is_array($conf['arguments']) ? $conf['arguments'] : [];
        /** @var ControllerContext $controllerContext */
        $controllerContext = $conf['controllerContext'];
        if (true === empty($partial)) {
            return '';
        }
        $this->configurationManager = $this->objectManager->get(ConfigurationManagerInterface::class);
        /** @var RenderingContext $renderingContext */
        $renderingContext = $this->objectManager->get(RenderingContext::class);
        $this->prepareContextsForUncachedRendering($renderingContext, $controllerContext);
        $this->setControllerContext($controllerContext);
        $this->setViewConfiguration($this);
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
        $renderingContext->setControllerContext($controllerContext);
        $this->setRenderingContext($renderingContext);
        $this->templateParser = TemplateParserBuilder::build();
        $this->templateCompiler = $this->objectManager->get(TemplateCompiler::class);
        if (isset($GLOBALS['typo3CacheManager'])) {
            $cacheManager = $GLOBALS['typo3CacheManager'];
        } else {
            $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
        }
        $this->templateCompiler->setTemplateCache($cacheManager->getCache('fluid_template'));
    }

    /**
     * @param ViewInterface $view
     *
     * @return void
     */
    protected function setViewConfiguration(ViewInterface $view)
    {
        // Template Path Override
        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK , $this->controllerContext->getRequest()->getControllerExtensionName()
        );

        // set TemplateRootPaths
        $viewFunctionName = 'setTemplateRootPaths';
        if (method_exists($view, $viewFunctionName)) {
            $setting = 'templateRootPaths';
            $parameter = $this->getViewProperty($extbaseFrameworkConfiguration, $setting);
            // no need to bother if there is nothing to set
            if ($parameter) {
                $view->$viewFunctionName($parameter);
            }
        }

        // set LayoutRootPaths
        $viewFunctionName = 'setLayoutRootPaths';
        if (method_exists($view, $viewFunctionName)) {
            $setting = 'layoutRootPaths';
            $parameter = $this->getViewProperty($extbaseFrameworkConfiguration, $setting);
            // no need to bother if there is nothing to set
            if ($parameter) {
                $view->$viewFunctionName($parameter);
            }
        }

        // set PartialRootPaths
        $viewFunctionName = 'setPartialRootPaths';
        if (method_exists($view, $viewFunctionName)) {
            $setting = 'partialRootPaths';
            $parameter = $this->getViewProperty($extbaseFrameworkConfiguration, $setting);
            // no need to bother if there is nothing to set
            if ($parameter) {
                $view->$viewFunctionName($parameter);
            }
        }
    }

    /**
     * Handles the path resolving for *rootPath(s)
     *
     * numerical arrays get ordered by key ascending
     *
     * @param array $extbaseFrameworkConfiguration
     * @param string $setting parameter name from TypoScript
     *
     * @return array
     */
    protected function getViewProperty($extbaseFrameworkConfiguration, $setting)
    {
        $values = [];
        if (
            !empty($extbaseFrameworkConfiguration['view'][$setting])
            && is_array($extbaseFrameworkConfiguration['view'][$setting])
        ) {
            $values = ArrayUtility::sortArrayWithIntegerKeys($extbaseFrameworkConfiguration['view'][$setting]);
            $values = array_reverse($values, true);
        }

        return $values;
    }

    /**
     * @param RenderingContextInterface $renderingContext
     * @param string $partial
     * @param string $section
     * @param array $arguments
     * @return string
     */
    protected function renderPartialUncached(
        RenderingContextInterface $renderingContext,
        $partial,
        $section = null,
        array $arguments = []
    ) {
        array_push(
            $this->renderingStack,
            ['type' => self::RENDERING_TEMPLATE, 'parsedTemplate' => null, 'renderingContext' => $renderingContext]
        );
        $rendered = $this->renderPartial($partial, $section, $arguments);
        array_pop($this->renderingStack);
        return $rendered;
    }
}
