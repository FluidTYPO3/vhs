<?php
namespace FluidTYPO3\Vhs\View;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\VersionUtility;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Attribute\AsAllowedCallable;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Fluid\Compatibility\TemplateParserBuilder;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextFactory;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\View\TemplateView;

class UncacheTemplateView extends TemplateView
{
    public function callUserFunction(string $postUserFunc, array $conf): string
    {
        $partial = $conf['partial'] ?? null;
        $section = $conf['section'] ?? null;
        $arguments = $conf['arguments'] ?? [];
        $parameters = $conf['controllerContext'] ?? null;
        $extensionName = $parameters instanceof ExtbaseRequestParameters
            ? $parameters->getControllerExtensionName()
            : $parameters['extensionName'] ?? null;

        if (empty($partial)) {
            return '';
        }

        $request = $parameters instanceof ExtbaseRequestParameters
            ? $GLOBALS['TYPO3_REQUEST']->withAttribute('extbase', $parameters)
            : clone $GLOBALS['TYPO3_REQUEST'];

        $renderingContext = $this->createRenderingContextWithRenderingContextFactory();
        if (VersionUtility::isCoreAtLeast13()) {
            $renderingContext->setAttribute(ServerRequestInterface::class, $request);
        } elseif (method_exists($renderingContext, 'setRequest')) {
            $renderingContext->setRequest($request);
        }

        $templatePaths = $renderingContext->getTemplatePaths();

        if (!empty($conf['partialRootPaths'])) {
            $templatePaths->setPartialRootPaths($conf['partialRootPaths']);
        } elseif ($extensionName) {
            $extensionKey = GeneralUtility::camelCaseToLowerCaseUnderscored($extensionName);
            $templatePaths->setTemplateRootPaths(['EXT:' . $extensionKey . '/Resources/Private/Templates/']);
            $templatePaths->setPartialRootPaths(['EXT:' . $extensionKey . '/Resources/Private/Partials/']);
            $templatePaths->setLayoutRootPaths(['EXT:' . $extensionKey . '/Resources/Private/Layouts/']);
        }

        $this->prepareContextsForUncachedRendering($renderingContext);

        /** @var mixed $output */
        $output = $this->renderPartial($partial, $section, $arguments);

        return is_scalar($output) ? (string) $output : '';
    }

    protected function prepareContextsForUncachedRendering(RenderingContextInterface $renderingContext): void
    {
        $this->setRenderingContext($renderingContext);
    }

    /**
     * @codeCoverageIgnore
     */
    protected function createRenderingContextWithRenderingContextFactory(): RenderingContextInterface
    {
        /** @var RenderingContextFactory $renderingContextFactory */
        $renderingContextFactory = GeneralUtility::makeInstance(RenderingContextFactory::class);
        return $renderingContextFactory->create();
    }
}
