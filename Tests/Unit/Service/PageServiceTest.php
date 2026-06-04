<?php
namespace FluidTYPO3\Vhs\Tests\Unit\Service;

use FluidTYPO3\Vhs\Service\PageService;
use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Page\PageInformation;

class PageServiceTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        PageService::resetCaches();
    }

    protected function tearDown(): void
    {
        PageService::resetCaches();
        unset($GLOBALS['TYPO3_REQUEST']);
        parent::tearDown();
    }

    public function testExplicitRequestWinsOverGlobalRequest(): void
    {
        $globalRequest = (new ServerRequest())->withAttribute(
            'frontend.page.information',
            $this->createPageInformation(111)
        );
        $activeRequest = (new ServerRequest())->withAttribute(
            'frontend.page.information',
            $this->createPageInformation(222)
        );
        $GLOBALS['TYPO3_REQUEST'] = $globalRequest;

        $subject = new PageService();
        $subject->setRequest($activeRequest);

        self::assertSame(
            $activeRequest,
            $this->callInaccessibleMethod($subject, 'getRequest')
        );
        self::assertSame(222, $this->callInaccessibleMethod($subject, 'getCurrentPageUid'));
    }

    public function testGetPage(): void
    {
        $pageRepository = $this->createPageRepositoryMock(['getPage']);
        $pageRepository->method('getPage')->willReturn(['uid' => 1]);

        $subject = $this->getMockBuilder(PageService::class)
            ->onlyMethods(['getPageRepository'])
            ->disableOriginalConstructor()
            ->getMock();
        $subject->method('getPageRepository')->willReturn($pageRepository);

        self::assertSame(['uid' => 1], $subject->getPage(1));
    }

    public function testGetPageSeparatesRuntimeCacheByRequestContext(): void
    {
        $request1 = new ServerRequest('https://example.org/request-1');
        $request2 = new ServerRequest('https://example.org/request-2');
        $pages = [
            ['uid' => 1, 'context' => 'request-1'],
            ['uid' => 1, 'context' => 'request-2'],
        ];

        $pageRepository = $this->createPageRepositoryMock(['getPage']);
        $pageRepository->expects($this->exactly(2))
            ->method('getPage')
            ->willReturnCallback(
                static function (int $pageUid, bool $disableGroupAccessCheck) use (&$pages): array {
                    self::assertSame(1, $pageUid);
                    self::assertFalse($disableGroupAccessCheck);
                    return array_shift($pages);
                }
            );

        $subject = $this->getMockBuilder(PageService::class)
            ->onlyMethods(['getPageRepository'])
            ->disableOriginalConstructor()
            ->getMock();
        $subject->method('getPageRepository')->willReturn($pageRepository);

        $subject->setRequest($request1);
        self::assertSame(['uid' => 1, 'context' => 'request-1'], $subject->getPage(1));

        $subject->setRequest($request2);
        self::assertSame(['uid' => 1, 'context' => 'request-2'], $subject->getPage(1));

        $subject->setRequest($request1);
        self::assertSame(['uid' => 1, 'context' => 'request-1'], $subject->getPage(1));
    }

    public function testGetMenu(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['FE']['hidePagesIfNotTranslatedByDefault'] = 1;
        $this->setFrontendRequest();

        $pageRepository = $this->createPageRepositoryMock(['getPage', 'getMenu', 'getPageOverlay']);
        $pageRepository->method('getPage')->willReturn(['uid' => 2]);
        $pageRepository->method('getMenu')->willReturn([['uid' => 2]]);
        $pageRepository->method('getPageOverlay')->willReturn([]);

        $subject = $this->getMockBuilder(PageService::class)
            ->onlyMethods(['getPageRepository'])
            ->disableOriginalConstructor()
            ->getMock();
        $subject->method('getPageRepository')->willReturn($pageRepository);

        self::assertSame([['uid' => 2]], $subject->getMenu(1));
    }

    public function testGetMenuSeparatesRuntimeCacheByRequestContext(): void
    {
        $request1 = new ServerRequest('https://example.org/request-1');
        $request2 = new ServerRequest('https://example.org/request-2');
        $menus = [
            [['uid' => 10, 'nav_hide' => 0]],
            [['uid' => 20, 'nav_hide' => 0]],
        ];

        $pageRepository = $this->createPageRepositoryMock(['getMenu']);
        $pageRepository->expects($this->exactly(2))
            ->method('getMenu')
            ->willReturnCallback(
                static function (int $pageUid) use (&$menus): array {
                    self::assertSame(1, $pageUid);
                    return array_shift($menus);
                }
            );

        $subject = $this->getMockBuilder(PageService::class)
            ->onlyMethods(['getPageRepository', 'hidePageForLanguageUid'])
            ->disableOriginalConstructor()
            ->getMock();
        $subject->method('getPageRepository')->willReturn($pageRepository);
        $subject->method('hidePageForLanguageUid')->willReturn(false);

        $subject->setRequest($request1);
        self::assertSame([['uid' => 10, 'nav_hide' => 0]], $subject->getMenu(1));

        $subject->setRequest($request2);
        self::assertSame([['uid' => 20, 'nav_hide' => 0]], $subject->getMenu(1));

        $subject->setRequest($request1);
        self::assertSame([['uid' => 10, 'nav_hide' => 0]], $subject->getMenu(1));
    }

    public function testResetCachesClearsPageAndMenuCaches(): void
    {
        $pageRepository = $this->createPageRepositoryMock(['getPage', 'getMenu']);
        $pageRepository->expects($this->exactly(2))
            ->method('getPage')
            ->willReturnOnConsecutiveCalls(
                ['uid' => 1, 'cache' => 'before-reset'],
                ['uid' => 1, 'cache' => 'after-reset']
            );
        $pageRepository->expects($this->exactly(2))
            ->method('getMenu')
            ->willReturnOnConsecutiveCalls(
                [['uid' => 10, 'nav_hide' => 0, 'cache' => 'before-reset']],
                [['uid' => 20, 'nav_hide' => 0, 'cache' => 'after-reset']]
            );

        $subject = $this->getMockBuilder(PageService::class)
            ->onlyMethods(['getPageRepository', 'hidePageForLanguageUid'])
            ->disableOriginalConstructor()
            ->getMock();
        $subject->method('getPageRepository')->willReturn($pageRepository);
        $subject->method('hidePageForLanguageUid')->willReturn(false);
        $subject->setRequest(new ServerRequest('https://example.org/request'));

        self::assertSame(['uid' => 1, 'cache' => 'before-reset'], $subject->getPage(1));
        self::assertSame([['uid' => 10, 'nav_hide' => 0, 'cache' => 'before-reset']], $subject->getMenu(1));

        PageService::resetCaches();

        self::assertSame(['uid' => 1, 'cache' => 'after-reset'], $subject->getPage(1));
        self::assertSame([['uid' => 20, 'nav_hide' => 0, 'cache' => 'after-reset']], $subject->getMenu(1));
    }

    /**
     * @dataProvider getGetRootLineTestValues
     */
    public function testGetRootLine(?int $pageUid, bool $reverse): void
    {
        $subject = new PageService();

        $rootLineUtility = $this->getMockBuilder(RootlineUtility::class)
            ->onlyMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();
        $rootLineUtility->method('get')->willReturn([]);

        GeneralUtility::addInstance(RootlineUtility::class, $rootLineUtility);
        $this->setFrontendRequest([
            'frontend.page.information' => $this->createPageInformation($pageUid ?? 123),
        ]);

        self::assertSame([], $subject->getRootLine($pageUid, $reverse));
    }

    public static function getGetRootLineTestValues(): array
    {
        return [
            'with page UID, not reversed' => [1, false],
            'with page UID, reversed' => [1, true],
            'without page UID, not reversed' => [null, false],
            'without page UID, reversed' => [null, true],
        ];
    }

    public function testIsAccessProtected(): void
    {
        $subject = new PageService();
        self::assertTrue($subject->isAccessProtected(['fe_group' => 1]));
        self::assertFalse($subject->isAccessProtected(['fe_group' => 0]));
    }

    /**
     * @dataProvider getIsAccessGrantedTestValues
     */
    public function testIsAccessGranted(bool $expected, array $page, FrontendUserAuthentication $user): void
    {
        $subject = new PageService();
        $this->setFrontendRequest(['frontend.user' => $user]);
        self::assertSame($expected, $subject->isAccessGranted($page));
    }

    public static function getIsAccessGrantedTestValues(): array
    {
        $noUser = self::createFrontendUserAuthentication(null, []);
        $pseudoUser = self::createFrontendUserAuthentication(null, []);
        $groupUser = self::createFrontendUserAuthentication([], [3, 4]);
        $anyGroupUser = self::createFrontendUserAuthentication(null, [3, 4]);

        return [
            'page not protected' => [true, ['fe_group' => 0], $noUser],
            'protected hide for any group without user login' => [true, ['fe_group' => -1], $noUser],
            'protected hide for any group with user login' => [false, ['fe_group' => -1], $groupUser],
            'protected for any group with mismatched groups' => [false, ['fe_group' => -2], $anyGroupUser],
            'protected with pseudo user without user data' => [false, ['fe_group' => 123], $pseudoUser],
            'protected with user with mismatched groups' => [false, ['fe_group' => 123], $groupUser],
            'protected with user with matched groups' => [true, ['fe_group' => 3], $groupUser],
        ];
    }

    private static function createFrontendUserAuthentication(?array $user, array $groups): FrontendUserAuthentication
    {
        $reflection = new \ReflectionClass(FrontendUserAuthentication::class);
        /** @var FrontendUserAuthentication $frontendUser */
        $frontendUser = $reflection->newInstanceWithoutConstructor();
        $frontendUser->user = $user;
        $frontendUser->groupData = ['uid' => $groups];
        return $frontendUser;
    }

    public function testIsCurrent(): void
    {
        $subject = new PageService();
        $this->setFrontendRequest([
            'frontend.page.information' => $this->createPageInformation(1),
        ]);
        self::assertTrue($subject->isCurrent(1));
        self::assertFalse($subject->isCurrent(2));
    }

    public function testIsActive(): void
    {
        $subject = $this->getMockBuilder(PageService::class)
            ->onlyMethods(['getRootLine'])
            ->disableOriginalConstructor()
            ->getMock();
        $subject->method('getRootLine')->willReturn([['uid' => 1], ['uid' => 2]]);
        self::assertTrue($subject->isActive(1));
        self::assertFalse($subject->isActive(34));
    }

    public function testShouldUseShortcutTarget(): void
    {
        $subject = new PageService();
        self::assertTrue(
            $subject->shouldUseShortcutTarget(['useShortcutData' => true]),
            'useShortcutData is not respected when true'
        );
        self::assertFalse(
            $subject->shouldUseShortcutTarget(['useShortcutData' => false]),
            'useShortcutData is not respected when true'
        );
        self::assertTrue(
            $subject->shouldUseShortcutTarget(['useShortcutData' => false, 'useShortcutTarget' => true]),
            'useShortcutTarget does not override useShortcutData when true'
        );
        self::assertFalse(
            $subject->shouldUseShortcutTarget(['useShortcutData' => true, 'useShortcutTarget' => false]),
            'useShortcutTarget does not override useShortcutData when false'
        );
    }

    public function testShouldUseShortcutUid(): void
    {
        $subject = new PageService();
        self::assertTrue(
            $subject->shouldUseShortcutUid(['useShortcutData' => true]),
            'useShortcutData is not respected when true'
        );
        self::assertFalse(
            $subject->shouldUseShortcutUid(['useShortcutData' => false]),
            'useShortcutData is not respected when true'
        );
        self::assertTrue(
            $subject->shouldUseShortcutUid(['useShortcutData' => false, 'useShortcutUid' => true]),
            'useShortcutUid does not override useShortcutData when true'
        );
        self::assertFalse(
            $subject->shouldUseShortcutUid(['useShortcutData' => true, 'useShortcutUid' => false]),
            'useShortcutUid does not override useShortcutData when false'
        );
    }

    /**
     * @dataProvider getGetShortcutTargetPageTestValues
     */
    public function testGetShortcutTargetPage(
        ?array $expected,
        array $page,
        array $pageFromRepository,
        array $menuFromRepository
    ): void {
        $subject = $this->getMockBuilder(PageService::class)
            ->onlyMethods(['getPage', 'getMenu'])
            ->disableOriginalConstructor()
            ->getMock();
        $subject->method('getPage')->willReturn($pageFromRepository);
        $subject->method('getMenu')->willReturn($menuFromRepository);

        self::assertSame($expected, $subject->getShortcutTargetPage($page));
    }

    public static function getGetShortcutTargetPageTestValues(): array
    {
        return [
            'page is not a shortcut page' => [null, ['doktype' => PageRepository::DOKTYPE_DEFAULT], [], []],
            'shortcut mode parent page' => [
                ['uid' => 1],
                [
                    'uid' => 1,
                    'pid' => 1,
                    'doktype' => PageRepository::DOKTYPE_SHORTCUT,
                    'shortcut_mode' => PageRepository::SHORTCUT_MODE_PARENT_PAGE,
                ],
                ['uid' => 1],
                [],
            ],
            'no shortcut mode' => [
                ['uid' => 1],
                [
                    'uid' => 1,
                    'pid' => 1,
                    'doktype' => PageRepository::DOKTYPE_SHORTCUT,
                    'shortcut_mode' => PageRepository::SHORTCUT_MODE_NONE,
                    'shortcut' => 1,
                ],
                ['uid' => 1],
                [],
            ],
            'random child page without shortcut page selected' => [
                ['uid' => 1],
                [
                    'uid' => 1,
                    'pid' => 1,
                    'doktype' => PageRepository::DOKTYPE_SHORTCUT,
                    'shortcut_mode' => PageService::SHORTCUT_MODE_RANDOM_SUBPAGE,
                    'shortcut' => 0,
                ],
                [],
                [['uid' => 1]],
            ],
            'random child page with shortcut page selected' => [
                ['uid' => 1],
                [
                    'uid' => 1,
                    'pid' => 1,
                    'doktype' => PageRepository::DOKTYPE_SHORTCUT,
                    'shortcut_mode' => PageService::SHORTCUT_MODE_RANDOM_SUBPAGE,
                    'shortcut' => 12,
                ],
                [],
                [['uid' => 1]],
            ],
            'first child page without shortcut page selected' => [
                ['uid' => 1],
                [
                    'uid' => 1,
                    'pid' => 1,
                    'doktype' => PageRepository::DOKTYPE_SHORTCUT,
                    'shortcut_mode' => PageRepository::SHORTCUT_MODE_FIRST_SUBPAGE,
                    'shortcut' => 0,
                ],
                [],
                [['uid' => 1]],
            ],
            'first child page with shortcut page selected' => [
                ['uid' => 1],
                [
                    'uid' => 1,
                    'pid' => 1,
                    'doktype' => PageRepository::DOKTYPE_SHORTCUT,
                    'shortcut_mode' => PageRepository::SHORTCUT_MODE_FIRST_SUBPAGE,
                    'shortcut' => 12,
                ],
                [],
                [['uid' => 1]],
            ],
        ];
    }

    public function testGetItemLinkWithExternalUrl(): void
    {
        $contentObjectRenderer = $this->getMockBuilder(ContentObjectRenderer::class)
            ->onlyMethods(['typoLink'])
            ->disableOriginalConstructor()
            ->getMock();
        $contentObjectRenderer->method('typoLink')->willReturn('link');
        GeneralUtility::addInstance(ContentObjectRenderer::class, $contentObjectRenderer);
        $this->setFrontendRequest();

        $subject = new PageService();

        // value "3" is PageRepositoty::DOKTYPE_LINK
        self::assertSame('link', $subject->getItemLink(['uid' => 1, 'doktype' => 3, 'url' => 'http://external']));
    }

    public function testGetItemLinkWithInternalPage(): void
    {
        $contentObjectRenderer = $this->getMockBuilder(ContentObjectRenderer::class)
            ->onlyMethods(['typoLink'])
            ->disableOriginalConstructor()
            ->getMock();
        $contentObjectRenderer->method('typoLink')->willReturn('link');
        GeneralUtility::addInstance(ContentObjectRenderer::class, $contentObjectRenderer);
        $this->setFrontendRequest();

        $subject = new PageService();
        // value "3" is PageRepositoty::DOKTYPE_DEFAULT
        self::assertSame('link', $subject->getItemLink(['uid' => 1, 'doktype' => 1]));
    }

    private function createPageRepositoryMock(array $methods): MockObject
    {
        if (class_exists(\TYPO3\CMS\Core\Domain\Repository\PageRepository::class)) {
            $class = \TYPO3\CMS\Core\Domain\Repository\PageRepository::class;
        } else {
            $class = \TYPO3\CMS\Frontend\Page\PageRepository::class;
        }
        return $this->getMockBuilder($class)->onlyMethods($methods)->disableOriginalConstructor()->getMock();
    }

    private function setFrontendRequest(array $attributes = []): void
    {
        $request = new ServerRequest('https://example.org/', 'GET');
        foreach ($attributes as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }
        $GLOBALS['TYPO3_REQUEST'] = $request;
    }

    private function createPageInformation(int $pageUid): PageInformation
    {
        $pageInformation = new PageInformation();
        $pageInformation->setId($pageUid);
        $pageInformation->setPageRecord(['uid' => $pageUid]);
        return $pageInformation;
    }
}
