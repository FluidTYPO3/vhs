<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Render;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\ViewHelperUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * ### Base class for all rendering ViewHelpers.
 *
 * If errors occur they can be graciously ignored and
 * replaced by a small error message or the error itself.
 */
abstract class AbstractRenderViewHelper extends AbstractViewHelper
{

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
     * @return void
     */
    public function injectObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

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
        if (method_exists($renderingContext, 'getControllerContext')) {
            $controllerContext = clone $renderingContext->getControllerContext();
            $view->setControllerContext($controllerContext);
            $view->setFormat($controllerContext->getRequest()->getFormat());
        }
        $view->assignMultiple(ViewHelperUtility::getVariableProviderFromRenderingContext($renderingContext)->getAll());
        return $view;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view
     * @param array $arguments
     * @throws \Exception
     * @return string
     */
    protected static function renderView(ViewInterface $view, array $arguments)
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
        return static::getObjectManager()->get(StandaloneView::class);
    }

    /**
     * @return ObjectManagerInterface
     */
    protected static function getObjectManager()
    {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }
}
