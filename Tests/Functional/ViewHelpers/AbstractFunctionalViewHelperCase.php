<?php

namespace FluidTYPO3\Vhs\Tests\Functional\ViewHelpers;

use FluidTYPO3\Vhs\Tests\Fixtures\Classes\FunctionalTypoScriptFrontendController;
use FluidTYPO3\Vhs\Tests\Fixtures\Classes\RenderingContext;
use FluidTYPO3\Vhs\Utility\VersionUtility;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Core\ApplicationContext;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Frontend\Page\PageInformation;
use TYPO3Fluid\Fluid\View\TemplateView;

abstract class AbstractFunctionalViewHelperCase extends TestCase
{
    private RenderingContext $renderingContext;
    private array $singletonInstancesBackup = [];
    private bool $hadGlobalRequest = false;
    private ?ServerRequestInterface $globalRequestBackup = null;

    protected function setUp(): void
    {
        parent::setUp();

        if (!defined('LF')) {
            define('LF', "\n");
        }

        if (!defined('TYPO3_REQUESTTYPE')) {
            define('TYPO3_REQUESTTYPE', SystemEnvironmentBuilder::REQUESTTYPE_FE);
        }
        if (!defined('TYPO3_REQUESTTYPE_FE')) {
            define('TYPO3_REQUESTTYPE_FE', SystemEnvironmentBuilder::REQUESTTYPE_FE);
        }
        if (!defined('TYPO3_REQUESTTYPE_CLI')) {
            define('TYPO3_REQUESTTYPE_CLI', SystemEnvironmentBuilder::REQUESTTYPE_CLI);
        }

        $root = realpath(__DIR__ . '/../../../');
        Environment::initialize(
            new ApplicationContext('Development'),
            true,
            false,
            $root,
            $root . '/public',
            $root . '/var',
            $root . '/typo3conf',
            $root . '/index.php',
            'linux',
        );

        $this->singletonInstancesBackup = GeneralUtility::getSingletonInstances();
        $this->hadGlobalRequest = isset($GLOBALS['TYPO3_REQUEST']);
        $this->globalRequestBackup = $GLOBALS['TYPO3_REQUEST'] ?? null;
        $this->renderingContext = new RenderingContext();
    }

    protected function tearDown(): void
    {
        GeneralUtility::resetSingletonInstances($this->singletonInstancesBackup);
        GeneralUtility::purgeInstances();

        if ($this->hadGlobalRequest) {
            $GLOBALS['TYPO3_REQUEST'] = $this->globalRequestBackup;
        } else {
            unset($GLOBALS['TYPO3_REQUEST']);
        }

        parent::tearDown();
    }

    protected function executeTemplate(string $source, array $variables = []): ?string
    {
        return $this->executeTemplateWithRenderingContext($source, $variables);
    }

    protected function executeTemplateWithRenderingContext(
        string $source,
        array $variables = [],
        ?\Closure $configureRenderingContext = null
    ): ?string
    {
        $view = new TemplateView($this->renderingContext);
        if ($configureRenderingContext) {
            $configureRenderingContext($this->renderingContext);
        }
        $this->renderingContext->templatePaths->setTemplateSource($source);
        $this->renderingContext->variableProvider->setSource($variables);
        return $view->render();
    }

    protected function executeTemplateWithRequest(
        string $source,
        ServerRequestInterface $request,
        array $variables = [],
        ?\Closure $configureRenderingContext = null
    ): ?string {
        $hadGlobalRequest = isset($GLOBALS['TYPO3_REQUEST']);
        $globalRequestBackup = $GLOBALS['TYPO3_REQUEST'] ?? null;
        $GLOBALS['TYPO3_REQUEST'] = $request;

        try {
            return $this->executeTemplateWithRenderingContext(
                $source,
                $variables,
                static function (RenderingContext $renderingContext) use ($request, $configureRenderingContext): void {
                    if (VersionUtility::isCoreAtLeast13()) {
                        $renderingContext->setAttribute(ServerRequestInterface::class, $request);
                    } elseif (method_exists($renderingContext, 'setRequest')) {
                        $renderingContext->setRequest(new Request($request));
                    }
                    if ($configureRenderingContext) {
                        $configureRenderingContext($renderingContext);
                    }
                }
            );
        } finally {
            if ($hadGlobalRequest) {
                $GLOBALS['TYPO3_REQUEST'] = $globalRequestBackup;
            } else {
                unset($GLOBALS['TYPO3_REQUEST']);
            }
        }
    }

    protected function assertTemplateRenders(?string $expected, string $source, array $variables = []): void
    {
        self::assertSame($expected, $this->executeTemplate($source, $variables));
    }

    protected function createFrontendRequest(
        array $attributes = [],
        string $uri = 'https://example.test/path/?foo=bar',
        int $pageUid = 123
    ): ServerRequestInterface {
        return $this->createRequest($attributes, $uri, SystemEnvironmentBuilder::REQUESTTYPE_FE, $pageUid);
    }

    protected function createBackendRequest(
        array $attributes = [],
        string $uri = 'https://example.test/typo3/'
    ): ServerRequestInterface {
        return $this->createRequest($attributes, $uri, SystemEnvironmentBuilder::REQUESTTYPE_BE);
    }

    protected function createTypoScriptFrontendControllerStub(): object
    {
        $className = 'TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController';
        if (!class_exists($className)) {
            class_alias(FunctionalTypoScriptFrontendController::class, $className);
        }

        if (is_a($className, FunctionalTypoScriptFrontendController::class, true)) {
            return new $className();
        }

        $controller = $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->getMock();
        if (!isset($controller->register) || !is_array($controller->register)) {
            $controller->register = [];
        }
        if (!isset($controller->tmpl) || !is_object($controller->tmpl)) {
            $controller->tmpl = (object) [
                'setup' => [],
            ];
        }

        return $controller;
    }

    private function createRequest(
        array $attributes,
        string $uri,
        int $applicationType,
        int $pageUid = 123
    ): ServerRequestInterface {
        $pageInformation = null;
        if (VersionUtility::isCoreAtLeast13()) {
            $pageInformation = new PageInformation();
            $pageInformation->setId($pageUid);
        }

        $request = (new ServerRequest($uri, 'GET'))
            ->withAttribute('applicationType', $applicationType)
            ->withAttribute('frontend.page.information', $pageInformation)
            ->withAttribute('frontend.user', null)
            ->withAttribute('frontend.controller', null);

        foreach ($attributes as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }

        return $request;
    }
}
