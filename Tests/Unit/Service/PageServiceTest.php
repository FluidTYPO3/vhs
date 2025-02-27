<?php
namespace FluidTYPO3\Vhs\Tests\Unit\Service;

use FluidTYPO3\Vhs\Service\PageService;
use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class PageServiceTest extends AbstractTestCase
{
    public function testGetPage(): void
    {
        $pageRepository = $this->createPageRepositoryMock(['getPage']);
        $pageRepository->method('getPage')->willReturn(['uid' => 1]);

        $subject = $this->getMockBuilder(PageService::class)
            ->setMethods(['getPageRepository'])
            ->disableOriginalConstructor()
            ->getMock();
        $subject->method('getPageRepository')->willReturn($pageRepository);

        self::assertSame(['uid' => 1], $subject->getPage(1));
    }

    public function testGetMenu(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['FE']['hidePagesIfNotTranslatedByDefault'] = 1;
        $GLOBALS['TSFE'] = (object) ['sys_language_uid' => 1];

        $pageRepository = $this->createPageRepositoryMock(['getPage', 'getMenu', 'getPageOverlay']);
        $pageRepository->method('getPage')->willReturn(['uid' => 2]);
        $pageRepository->method('getMenu')->willReturn([['uid' => 2]]);
        $pageRepository->method('getPageOverlay')->willReturn([]);

        $subject = $this->getMockBuilder(PageService::class)
            ->setMethods(['getPageRepository'])
            ->disableOriginalConstructor()
            ->getMock();
        $subject->method('getPageRepository')->willReturn($pageRepository);

        self::assertSame([['uid' => 2]], $subject->getMenu(1));
    }

    /**
     * @dataProvider getGetRootLineTestValues
     */
    public function testGetRootLine(?int $pageUid, bool $reverse): void
    {
        $subject = new PageService();

        $rootLineUtility = $this->getMockBuilder(RootlineUtility::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();
        $rootLineUtility->method('get')->willReturn([]);

        GeneralUtility::addInstance(RootlineUtility::class, $rootLineUtility);
        $GLOBALS['TSFE'] = (object) ['id' => $pageUid ?? 123];

        self::assertSame([], $subject->getRootLine($pageUid, $reverse));
    }

    public function getGetRootLineTestValues(): array
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
        $GLOBALS['TSFE'] = (object) ['fe_user' => $user];
        self::assertSame($expected, $subject->isAccessGranted($page));
    }

    public function getIsAccessGrantedTestValues(): array
    {
        $noUser = $this->getMockBuilder(FrontendUserAuthentication::class)->disableOriginalConstructor()->getMock();
        $pseudoUser = $this->getMockBuilder(FrontendUserAuthentication::class)->disableOriginalConstructor()->getMock();
        $groupUser = $this->getMockBuilder(FrontendUserAuthentication::class)->disableOriginalConstructor()->getMock();
        $groupUser->groupData = ['uid' => [3, 4]];
        $groupUser->user = [];
        $anyGroupUser = $this->getMockBuilder(FrontendUserAuthentication::class)
            ->disableOriginalConstructor()
            ->getMock();
        $anyGroupUser->groupData = ['uid' => [3, 4]];

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

    public function testIsCurrent(): void
    {
        $subject = new PageService();
        $GLOBALS['TSFE'] = (object) ['id' => 1];
        self::assertTrue($subject->isCurrent(1));
        self::assertFalse($subject->isCurrent(2));
    }

    public function testIsActive(): void
    {
        $subject = $this->getMockBuilder(PageService::class)
            ->setMethods(['getRootLine'])
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
            ->setMethods(['getPage', 'getMenu'])
            ->disableOriginalConstructor()
            ->getMock();
        $subject->method('getPage')->willReturn($pageFromRepository);
        $subject->method('getMenu')->willReturn($menuFromRepository);

        self::assertSame($expected, $subject->getShortcutTargetPage($page));
    }

    public function getGetShortcutTargetPageTestValues(): array
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
                    'shortcut_mode' => PageRepository::SHORTCUT_MODE_RANDOM_SUBPAGE,
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
                    'shortcut_mode' => PageRepository::SHORTCUT_MODE_RANDOM_SUBPAGE,
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
            ->setMethods(['typoLink'])
            ->disableOriginalConstructor()
            ->getMock();
        $contentObjectRenderer->method('typoLink')->willReturn('link');
        $GLOBALS['TSFE'] = (object) ['cObj' => $contentObjectRenderer];

        $pageRepository = $this->createPageRepositoryMock(['getExtURL']);
        $pageRepository->method('getExtURL')->willReturn('http://external');

        $subject = $this->getMockBuilder(PageService::class)
            ->setMethods(['getPageRepository'])
            ->disableOriginalConstructor()
            ->getMock();
        $subject->method('getPageRepository')->willReturn($pageRepository);

        // value "3" is PageRepositoty::DOKTYPE_LINK
        self::assertSame('link', $subject->getItemLink(['uid' => 1, 'doktype' => 3]));
    }

    public function testGetItemLinkWithInternalPage(): void
    {
        $contentObjectRenderer = $this->getMockBuilder(ContentObjectRenderer::class)
            ->setMethods(['typoLink'])
            ->disableOriginalConstructor()
            ->getMock();
        $contentObjectRenderer->method('typoLink')->willReturn('link');
        $GLOBALS['TSFE'] = (object) ['cObj' => $contentObjectRenderer];

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
        return $this->getMockBuilder($class)->setMethods($methods)->disableOriginalConstructor()->getMock();
    }
}
