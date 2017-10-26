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
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Fluid\Compatibility\TemplateParserBuilder;
use TYPO3\CMS\Fluid\Core\Compiler\TemplateCompiler;
use TYPO3\CMS\Fluid\Core\Parser\TemplateParser;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\View\TemplateView;

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
     * @return void
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
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
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
        /** @var RenderingContext $renderingContext */
        $renderingContext = $this->objectManager->get(RenderingContext::class);
        $this->prepareContextsForUncachedRendering($renderingContext, $controllerContext);
        $this->setControllerContext($controllerContext);
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
        if (method_exists($renderingContext, 'getTemplateParser')) {
            $this->templateParser = $renderingContext->getTemplateParser();
        } else {
            $this->templateParser = TemplateParserBuilder::build();
        }
        if (method_exists($renderingContext, 'getTemplateCompiler')) {
            $this->templateCompiler = $renderingContext->getTemplateCompiler();
        } else {
            $this->templateCompiler = $this->objectManager->get(TemplateCompiler::class);
            $cacheManager = GeneralUtility::makeInstance(CacheManager::class);

            $this->templateCompiler->setTemplateCache($cacheManager->getCache('fluid_template'));
        }
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
