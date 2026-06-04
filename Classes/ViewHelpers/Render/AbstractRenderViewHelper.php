<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Render;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use FluidTYPO3\Vhs\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\View\AbstractTemplateView;
use TYPO3Fluid\Fluid\View\TemplateView;
use TYPO3Fluid\Fluid\View\TemplatePaths;
use TYPO3Fluid\Fluid\View\ViewInterface;

/**
 * ### Base class for all rendering ViewHelpers.
 *
 * If errors occur they can be graciously ignored and
 * replaced by a small error message or the error itself.
 */
abstract class AbstractRenderViewHelper extends AbstractViewHelper
{
    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager): void
    {
        $this->configurationManager = $configurationManager;
    }

    public function initializeArguments(): void
    {
        $this->registerArgument(
            'onError',
            'string',
            'Optional error message to display if error occur while rendering. If NULL, lets the error Exception ' .
            'pass trough (and break rendering)'
        );
        $this->registerArgument(
            'graceful',
            'boolean',
            'If forced to FALSE, errors are not caught but rather "transmitted" as every other error would be',
            false,
            false
        );
    }

    protected static function getPreparedNamespaces(array $arguments): array
    {
        $namespaces = [];
        foreach ((array) $arguments['namespaces'] as $namespaceIdentifier => $namespace) {
            $addedOverriddenNamespace = '{namespace ' . $namespaceIdentifier . '=' . $namespace . '}';
            $namespaces[] = $addedOverriddenNamespace;
        }
        return $namespaces;
    }

    protected static function getPreparedClonedView(RenderingContextInterface $renderingContext): AbstractTemplateView
    {
        $view = static::getPreparedView();
        $newRenderingContext = $view->getRenderingContext();
        if (method_exists($renderingContext, 'getControllerContext')) {
            $controllerContext = clone $renderingContext->getControllerContext();

            if (method_exists($view, 'setFormat')) {
                $view->setFormat($controllerContext->getRequest()->getFormat());
            }
            $templatePaths = $newRenderingContext->getTemplatePaths();
            if ($templatePaths instanceof TemplatePaths && method_exists($templatePaths, 'setFormat')) {
                $templatePaths->setFormat((string) $controllerContext->getRequest()->getFormat());
            }
            $newRenderingContext->setViewHelperVariableContainer(
                $renderingContext->getViewHelperVariableContainer()
            );
            if (method_exists($newRenderingContext, 'setControllerContext')) {
                $newRenderingContext->setControllerContext($controllerContext);
            }
        } elseif (method_exists($renderingContext, 'getAttribute')
            && method_exists($newRenderingContext, 'setAttribute')
            && $renderingContext->hasAttribute(ServerRequestInterface::class)
        ) {
            $newRenderingContext->setAttribute(
                ServerRequestInterface::class,
                $renderingContext->getAttribute(ServerRequestInterface::class)
            );
        }
        $variables = (array) $renderingContext->getVariableProvider()->getAll();
        $view->assignMultiple($variables);
        return $view;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface|ViewInterface $view
     */
    protected static function renderView($view, array $arguments): string
    {
        try {
            /** @var string|null $content */
            $content = $view->render();
        } catch (\Exception $error) {
            if (!($arguments['graceful'] ?? false)) {
                throw $error;
            }
            $content = $error->getMessage() . ' (' . $error->getCode() . ')';
        }
        return (string) $content;
    }

    protected static function getPreparedView(): AbstractTemplateView
    {
        /** @var AbstractTemplateView $view */
        $view = GeneralUtility::makeInstance(TemplateView::class);
        return $view;
    }

    protected static function configureTemplatePaths(
        AbstractTemplateView $view,
        string $file,
        ?string $format,
        array $paths
    ): void {
        $templatePaths = $view->getRenderingContext()->getTemplatePaths();
        if (!($templatePaths instanceof TemplatePaths)) {
            return;
        }

        $templatePaths->setTemplatePathAndFilename($file);
        if (null !== $format) {
            $templatePaths->setFormat($format);
        }
        if (isset($paths['layoutRootPaths']) && is_array($paths['layoutRootPaths'])) {
            $templatePaths->setLayoutRootPaths($paths['layoutRootPaths']);
        }
        if (isset($paths['partialRootPaths']) && is_array($paths['partialRootPaths'])) {
            $templatePaths->setPartialRootPaths($paths['partialRootPaths']);
        }
    }

    protected static function configureTemplateSource(AbstractTemplateView $view, string $source): void
    {
        $templatePaths = $view->getRenderingContext()->getTemplatePaths();
        if (!($templatePaths instanceof TemplatePaths)) {
            return;
        }

        $templatePaths->setTemplateSource($source);
    }
}
