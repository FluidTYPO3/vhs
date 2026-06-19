<?php

namespace FluidTYPO3\Vhs\Tests\Functional\ViewHelpers;

use FluidTYPO3\Vhs\Service\PageService;
use FluidTYPO3\Vhs\ViewHelpers\Menu\AbstractMenuViewHelper;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FrameworkViewHelperTest extends AbstractFunctionalViewHelperCase
{
    public function testRequestAndSiteViewHelpersReadFromRequestContext(): void
    {
        $normalizedParams = $this->getMockBuilder(NormalizedParams::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getSiteUrl'])
            ->getMock();
        $normalizedParams->method('getSiteUrl')->willReturn('https://example.test/');

        $request = $this->createFrontendRequest(['normalizedParams' => $normalizedParams]);

        self::assertSame(
            'https://example.test/path/?foo=bar|https://example.test/path/?foo=bar|https://example.test/',
            $this->executeTemplateWithRequest('<v:uri.request />|<v:page.absoluteUrl />|<v:site.url />', $request)
        );
    }

    public function testSiteNameReadsGlobalConfiguration(): void
    {
        $previous = $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'] ?? null;
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'] = 'Functional Site';

        try {
            self::assertSame('Functional Site', $this->executeTemplate('<v:site.name />'));
        } finally {
            if ($previous === null) {
                unset($GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename']);
            } else {
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'] = $previous;
            }
        }
    }

    public function testStaticPrefixReadsFrontendControllerSetup(): void
    {
        $controller = $this->createTypoScriptFrontendControllerStub();
        $controller->tmpl->setup['plugin.']['tx_vhs.']['settings.']['prependPath'] = '/static/';

        $request = $this->createFrontendRequest(['frontend.controller' => $controller]);

        self::assertSame('/static/', $this->executeTemplateWithRequest('<v:page.staticPrefix />', $request));
    }

    public function testTsfeRegisterSetAndGetUseFrontendControllerFromRequest(): void
    {
        $controller = $this->createTypoScriptFrontendControllerStub();
        $request = $this->createFrontendRequest(['frontend.controller' => $controller]);

        $source = '<v:variable.register.set name="direct" value="value" />'
            . '<v:variable.register.set name="body">body-value</v:variable.register.set>'
            . '<v:variable.register.get name="direct" />|<v:variable.register.get name="body" />';

        self::assertSame('value|body-value', $this->executeTemplateWithRequest($source, $request));
    }

    public function testPageServiceBackedViewHelpersCanUseInjectedSingleton(): void
    {
        $pageRepository = $this->getMockBuilder(PageRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getPage_noCheck', 'getPage'])
            ->getMock();
        $pageRepository->method('getPage_noCheck')->with(42)->willReturn(['uid' => 42, 'title' => 'The Page']);
        $pageRepository->method('getPage')->with(123)->willReturn(['is_siteroot' => true, 'pid' => 1]);

        $pageService = $this->getMockBuilder(PageService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getPageRepository', 'getRootLine'])
            ->getMock();
        $pageService->method('getPageRepository')->willReturn($pageRepository);
        $pageService->method('getRootLine')->with(42)->willReturn([['uid' => 1], ['uid' => 42]]);
        GeneralUtility::setSingletonInstance(PageService::class, $pageService);

        $source = '<v:page.info pageUid="42" field="title" />'
            . '|<v:page.rootline pageUid="42" as="rootline">{rootline.0.uid}-{rootline.1.uid}</v:page.rootline>'
            . '|<v:condition.page.isChildPage pageUid="123" respectSiteRoot="1" then="child" else="root" />';

        self::assertSame('The Page|1-42|root', $this->executeTemplateWithRequest($source, $this->createFrontendRequest()));
    }

    public function testHasSubpagesUsesConfiguredPageService(): void
    {
        $pageService = $this->getMockBuilder(PageService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMenu'])
            ->getMock();
        $pageService->method('getMenu')->willReturnOnConsecutiveCalls([['uid' => 2]], []);
        \FluidTYPO3\Vhs\ViewHelpers\Condition\Page\HasSubpagesViewHelper::setPageService($pageService);

        $source = '<v:condition.page.hasSubpages pageUid="1" then="has" else="empty" />'
            . '|<v:condition.page.hasSubpages pageUid="1" then="has" else="empty" />';

        self::assertSame('has|empty', $this->executeTemplateWithRequest($source, $this->createFrontendRequest()));
    }

    public function testDeferredMenuCanRenderStoredStringOrStoredArray(): void
    {
        $stringResult = $this->executeTemplateWithRenderingContext(
            '<v:menu.deferred />',
            [],
            static function ($renderingContext): void {
                $renderingContext->getViewHelperVariableContainer()->addAll(
                    AbstractMenuViewHelper::class,
                    [
                        'deferredString' => 'deferredString',
                        'deferredArray' => ['foo' => 'bar'],
                    ]
                );
            }
        );

        $arrayResult = $this->executeTemplateWithRenderingContext(
            '<v:menu.deferred as="menu">{menu.foo}</v:menu.deferred>{menu}',
            ['menu' => 'original'],
            static function ($renderingContext): void {
                $renderingContext->getViewHelperVariableContainer()->addAll(
                    AbstractMenuViewHelper::class,
                    [
                        'deferredString' => 'deferredString',
                        'deferredArray' => ['foo' => 'bar'],
                    ]
                );
            }
        );

        self::assertSame('deferredString', $stringResult);
        self::assertSame('baroriginal', $arrayResult);
    }
}
