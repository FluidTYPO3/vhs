<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Render;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### Base class for all rendering ViewHelpers.
 *
 * If errors occur they can be graciously ignored and
 * replaced by a small error message or the error itself.
 */
abstract class AbstractRenderViewHelper extends AbstractViewHelper
{
    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
     * @return void
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * Initialize arguments
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument(
            'onError',
            'string',
            'Optional error message to display if error occur while rendering. If NULL, lets the error Exception ' .
            'pass trough (and break rendering)',
            false,
            null
        );
        $this->registerArgument(
            'graceful',
            'boolean',
            'If forced to FALSE, errors are not caught but rather "transmitted" as every other error would be',
            false,
            false
        );
    }

    /**
     * @param array $arguments
     * @return array
     */
    protected static function getPreparedNamespaces(array $arguments)
    {
        $namespaces = [];
        foreach ((array) $arguments['namespaces'] as $namespaceIdentifier => $namespace) {
            $addedOverriddenNamespace = '{namespace ' . $namespaceIdentifier . '=' . $namespace . '}';
            array_push($namespaces, $addedOverriddenNamespace);
        }
        return $namespaces;
    }

    /**
     * @param RenderingContextInterface $renderingContext
     * @return \TYPO3\CMS\Fluid\View\StandaloneView
     */
    protected static function getPreparedClonedView(RenderingContextInterface $renderingContext)
    {
        $view = static::getPreparedView();
        $newRenderingContext = $view->getRenderingContext();
        if (method_exists($renderingContext, 'getControllerContext')) {
            $controllerContext = clone $renderingContext->getControllerContext();

            $view->setFormat($controllerContext->getRequest()->getFormat());
            $newRenderingContext->setViewHelperVariableContainer(
                $renderingContext->getViewHelperVariableContainer()
            );
            if (method_exists($newRenderingContext, 'setControllerContext')) {
                $newRenderingContext->setControllerContext($controllerContext);
            }
        } elseif (method_exists($renderingContext, 'getRequest') && method_exists($newRenderingContext, 'setRequest')) {
            $newRenderingContext->setRequest($renderingContext->getRequest());
        }
        $variables = (array) $renderingContext->getVariableProvider()->getAll();
        $view->assignMultiple($variables);
        return $view;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface|\TYPO3Fluid\Fluid\View\ViewInterface $view
     * @param array $arguments
     * @throws \Exception
     * @return string
     */
    protected static function renderView($view, array $arguments)
    {
        try {
            $content = $view->render();
        } catch (\Exception $error) {
            if (!$arguments['graceful']) {
                throw $error;
            }
            $content = $error->getMessage() . ' (' . $error->getCode() . ')';
        }
        return $content;
    }

    /**
     * @return \TYPO3\CMS\Fluid\View\StandaloneView
     */
    protected static function getPreparedView()
    {
        /** @var StandaloneView $view */
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        return $view;
    }
}
