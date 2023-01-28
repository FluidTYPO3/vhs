<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Security;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use FluidTYPO3\Vhs\ViewHelpers\Security\AbstractSecurityViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\BackendUser;
use TYPO3\CMS\Extbase\Domain\Model\BackendUserGroup;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3Fluid\Fluid\Core\ViewHelper\ArgumentDefinition;

class AbstractSecurityViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     */
    public function canCreateViewHelperInstance()
    {
        $instance = $this->getMockBuilder($this->getViewHelperClassName())
            ->disableOriginalConstructor()
            ->getMock();
        $this->assertInstanceOf($this->getViewHelperClassName(), $instance);
    }

    protected function createInstance(): AbstractSecurityViewHelper
    {
        $instance = $this->getMockBuilder($this->getViewHelperClassName())
            ->setMethods(['dummy'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        return $instance;
    }

    /**
     * @dataProvider getEvaluateArgumentsTestValues
     */
    public function testEvaluateArguments(array $arguments, array $expectedMethods, bool $expectedReturn): void
    {
        $node = $this->getMockBuilder(ViewHelperNode::class)
            ->setMethods(['getChildNodes'])
            ->disableOriginalConstructor()
            ->getMock();
        $node->expects($this->any())->method('getChildNodes')->willReturn([]);
        $instance = $this->getMockBuilder($this->getViewHelperClassName())
            ->setMethods($expectedMethods)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        /** @var ArgumentDefinition[] $argumentDefinitions */
        $argumentDefinitions = $instance->prepareArguments();
        $preparedArguments = [];
        foreach ($argumentDefinitions as $argumentName => $argumentDefinition) {
            $preparedArguments[$argumentName] = $argumentDefinition->getDefaultValue();
        }
        foreach ($arguments as $argumentName => $value) {
            $preparedArguments[$argumentName] = $value;
        }
        $instance->setViewHelperNode($node);
        foreach ($expectedMethods as $expectedMethod) {
            $instance->expects($this->once())->method($expectedMethod)->willReturn(true);
        }
        $instance->setArguments($preparedArguments);
        $result = $this->callInaccessibleMethod($instance, 'evaluateArguments');
        $this->assertEquals($expectedReturn, $result);
    }

    public function getEvaluateArgumentsTestValues(): array
    {
        if (!class_exists(FrontendUser::class)) {
            self::markTestSkipped('Skipping test with FrontendUser dependency');
        }
        $frontendUser = new FrontendUser();
        $frontendUser->_setProperty('uid', 1);
        $frontendUsers = new ObjectStorage();
        $frontendUsers->attach($frontendUser);
        $frontendUserGroup = new FrontendUserGroup();
        $frontendUserGroup->_setProperty('uid', 2);
        $frontendUserGroups = new ObjectStorage();
        $frontendUserGroups->attach($frontendUserGroup);
        $backendUser = new BackendUser();
        $backendUser->_setProperty('uid', 3);
        $backendUsers = new ObjectStorage();
        $backendUsers->attach($backendUser);
        $backendUserGroup = new BackendUserGroup();
        $backendUserGroup->_setProperty('uid', 4);
        $backendUserGroups = new ObjectStorage();
        $backendUserGroups->attach($backendUserGroup);
        return [
            'any frontend user' => [
                ['anyFrontendUser' => true],
                ['assertFrontendUserLoggedIn'],
                true
            ],
            'any frontend user group' => [
                ['anyFrontendUserGroup' => true],
                ['assertFrontendUserGroupLoggedIn'],
                true
            ],
            'specific frontend user' => [
                ['frontendUser' => $frontendUser],
                ['assertFrontendUserLoggedIn'],
                true
            ],
            'one of provided frontend users' =>
                [
                ['frontendUsers' => $frontendUsers],
                ['assertFrontendUsersLoggedIn'],
                true
            ],
            'specific frontend uer group' => [
                ['frontendUserGroup' => true],
                ['assertFrontendUserGroupLoggedIn'],
                true
            ],
            'one of provided frontend user groups' => [
                ['frontendUserGroups' => true],
                ['assertFrontendUserGroupLoggedIn'],
                true
            ],
            'any backend user' => [
                ['anyBackendUser' => true],
                ['assertBackendUserLoggedIn'],
                true
            ],
            'any backend user group' => [
                ['anyBackendUserGroup' => true],
                ['assertBackendUserGroupLoggedIn'],
                true
            ],
            'specific backend user' => [
                ['backendUser' => $backendUser],
                ['assertBackendUserLoggedIn'],
                true
            ],
            'one of provided backend users' => [
                ['backendUsers' => $backendUsers],
                ['assertBackendUserLoggedIn'],
                true
            ],
            'specific backend user group' => [
                ['backendUserGroup' => $backendUserGroup],
                ['assertBackendUserGroupLoggedIn'],
                true
            ],
            'one of provided backend user groups' => [
                ['backendUserGroups' => $backendUserGroups],
                ['assertBackendUserGroupLoggedIn'],
                true
            ],
            'admin' => [
                ['admin' => true],
                ['assertAdminLoggedIn'],
                true
            ],
            'frontend user and admin' => [
                ['admin' => true, 'anyFrontendUser' => true, 'evaluationMode' => 'AND'],
                ['assertAdminLoggedIn', 'assertFrontendUserLoggedIn'],
                true
            ],
            'frontned user or admin' => [
                ['admin' => true, 'anyFrontendUser' => true, 'evaluationMode' => 'OR'],
                ['assertAdminLoggedIn', 'assertFrontendUserLoggedIn'],
                true
            ],
        ];
    }

    /**
     * @dataProvider getAssertFrontendUserLoggedInTestValues
     */
    public function testAssertFrontendUserLoggedIn(
        ?FrontendUser $user,
        ?FrontendUser $resolvedUser,
        bool $expected
    ): void {
        $instance = $this->getMockBuilder($this->getViewHelperClassName())
            ->setMethods(['getCurrentFrontendUser'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $instance->expects($this->once())->method('getCurrentFrontendUser')->willReturn($resolvedUser);
        $result = $this->callInaccessibleMethod($instance, 'assertFrontendUserLoggedIn', $user);
        $this->assertEquals($expected, $result);
    }

    public function getAssertFrontendUserLoggedInTestValues(): array
    {
        if (!class_exists(FrontendUser::class)) {
            self::markTestSkipped('Skipping test with FrontendUser dependency');
        }
        $user1 = new FrontendUser();
        $property = new \ReflectionProperty($user1, 'uid');
        $property->setAccessible(true);
        $property->setValue($user1, 1);
        $user2 = new FrontendUser();
        $property = new \ReflectionProperty($user2, 'uid');
        $property->setAccessible(true);
        $property->setValue($user2, 2);

        return [
            [null, null, false],
            [$user1, null, false],
            [null, $user1, true],
            [$user1, $user1, true],
            [$user1, $user2, false]
        ];
    }

    /**
     * @dataProvider getAssertFrontendUserGroupLoggedInTestValues
     * @param FrontendUserGroup|ObjectStorage
     */
    public function testAssertFrontendUserGroupLoggedIn(
        $group,
        ?FrontendUser $resolvedUser,
        bool $expected
    ): void {
        $instance = $this->getMockBuilder($this->getViewHelperClassName())
            ->setMethods(['getCurrentFrontendUser'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $instance->expects($this->once())->method('getCurrentFrontendUser')->willReturn($resolvedUser);
        $result = $this->callInaccessibleMethod($instance, 'assertFrontendUserGroupLoggedIn', $group);
        $this->assertEquals($expected, $result);
    }

    public function getAssertFrontendUserGroupLoggedInTestValues(): array
    {
        if (!class_exists(FrontendUser::class)) {
            self::markTestSkipped('Skipping test with FrontendUser dependency');
        }
        $frontendUserGroup = new FrontendUserGroup();
        $frontendUserGroups = new ObjectStorage();
        $frontendUserGroups->attach($frontendUserGroup);
        $user1 = new FrontendUser();
        $this->setInaccessiblePropertyValue($user1, 'uid', 1);
        $user2 = new FrontendUser();
        $user2->setUsergroup($frontendUserGroups);
        $this->setInaccessiblePropertyValue($user2, 'uid', 2);
        return [
            [null, null, false],
            [null, $user1, false],
            [null, $user2, true],
            [$frontendUserGroup, $user1, false],
            [$frontendUserGroup, $user2, true],
            [$frontendUserGroups, $user1, false],
            [$frontendUserGroups, $user2, true],
            ['unsupportedtype', $user1, false]
        ];
    }

    /**
     * @dataProvider getAssertFrontendUsersLoggedInTestValues
     */
    public function testAssertFrontendUsersLoggedIn(
        ObjectStorage $users,
        FrontendUser $currentUser,
        bool $expected
    ): void {
        $instance = $this->getMockBuilder($this->getViewHelperClassName())
            ->setMethods(['getCurrentFrontendUser'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $instance->expects($this->exactly($users->count()))->method('getCurrentFrontendUser')->willReturn($currentUser);
        $result = $instance->assertFrontendUsersLoggedIn($users);
        $this->assertEquals($expected, $result);
    }

    public function getAssertFrontendUsersLoggedInTestValues(): array
    {
        if (!class_exists(FrontendUser::class)) {
            self::markTestSkipped('Skipping test with FrontendUser dependency');
        }
        $user1 = new FrontendUser();
        $this->setInaccessiblePropertyValue($user1, 'uid', 1);
        $user2 = new FrontendUser();
        $this->setInaccessiblePropertyValue($user2, 'uid', 2);
        $user3 = new FrontendUser();
        $this->setInaccessiblePropertyValue($user3, 'uid', 3);

        $users = new ObjectStorage();
        $users->attach($user1);
        $users->attach($user2);
        $contained = $user2;
        $notContained = $user3;
        return [
            [$users, $notContained, false],
            [$users, $contained, true],
        ];
    }

    /**
     * @dataProvider getAssertBackendUserLoggedInTestValues
     */
    public function testAssertBackendUserLoggedIn(?int $user, ?int $currentUser, bool $expected): void
    {
        $GLOBALS['BE_USER'] = (object) ['user' => ['uid' => $currentUser]];
        $instance = $this->getMockBuilder($this->getViewHelperClassName())
            ->setMethods(['dummy'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $result = $instance->assertBackendUserLoggedIn($user);
        unset($GLOBALS['BE_USER']->user);
        $this->assertEquals($expected, $result);
    }

    public function getAssertBackendUserLoggedInTestValues(): array
    {
        return [
            [1, 0, false],
            [2, 4, false],
            [3, 3, true],
            [2, 2, true],
            [null, 1, true],
            [1, null, false]
        ];
    }

    /**
     * @dataProvider getAssertBackendUserGroupLoggedInTestValues
     * @param null|string|array $group
     */
    public function testAssertBackendUserGroupLoggedIn($group, ?array $currentUser, bool $expected): void
    {
        $instance = $this->getMockBuilder($this->getViewHelperClassName())
            ->setMethods(['getCurrentBackendUser'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $instance->method('getCurrentBackendUser')->willReturn($currentUser);
        $result = $instance->assertBackendUserGroupLoggedIn($group);
        $this->assertEquals($expected, $result);
    }

    public function getAssertBackendUserGroupLoggedInTestValues(): array
    {
        return [
            [null, null, false],
            [[], ['uid' => 1, 'usergroup' => '1,2,3'], false],
            [[1], ['uid' => 1, 'usergroup' => ''], false],
            [[1], ['uid' => 1, 'usergroup' => '1,2,3'], true],
            [[1,9], ['uid' => 1, 'usergroup' => '1,2,3'], true],
            [[4,5], ['uid' => 1, 'usergroup' => '1,2,3'], false],
            [[1,7], ['uid' => 1, 'usergroup' => '1,2,3'], true],
            [[4,8], ['uid' => 1, 'usergroup' => '1,2,3'], false],
            ['1,7', ['uid' => 1, 'usergroup' => '1,2,3'], true],
            ['4,8,', ['uid' => 1, 'usergroup' => '1,2,3'], false]
        ];
    }

    /**
     * @dataProvider getAssertAdminLoggedInTestValues
     */
    public function testAssertAdminLoggedIn(?array $currentUser, bool $expected): void
    {
        $instance = $this->getMockBuilder($this->getViewHelperClassName())
            ->setMethods(['getCurrentBackendUser'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $instance->expects($this->atLeastOnce())->method('getCurrentBackendUser')->willReturn($currentUser);
        $result = $instance->assertAdminLoggedIn();
        $this->assertEquals($expected, $result);
    }

    public function getAssertAdminLoggedInTestValues(): array
    {
        return [
            [null, false],
            [['uid' => 1, 'admin' => 0], false],
            [['uid' => 1, 'admin' => 1], true]
        ];
    }

    public function testGetCurrentFrontendUserReturnsNullIfNoFrontendUserRecordIsSetInFrontendController(): void
    {
        $GLOBALS['TSFE'] = (object) ['loginUser' => ''];
        $instance = $this->getMockBuilder($this->getViewHelperClassName())
            ->setMethods(['dummy'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $result = $instance->getCurrentFrontendUser();
        $this->assertNull($result);
        unset($GLOBALS['TSFE']);
    }

    public function testGetCurrentFrontendUserFetchesFromFrontendUserRepository(): void
    {
        if (!class_exists(FrontendUser::class)) {
            self::markTestSkipped('Skipping test with FrontendUser dependency');
        }
        $GLOBALS['TSFE'] = (object) ['loginUser' => 1, 'fe_user' => (object) ['user' => ['uid' => 1]]];

        $frontendUser = new FrontendUser();

        $querySettings = $this->getMockBuilder(Typo3QuerySettings::class)->disableOriginalConstructor()->getMock();

        $query = $this->getMockBuilder(Query::class)
            ->setMethods(['getQuerySettings'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->method('getQuerySettings')->willReturn($querySettings);

        $repository = $this->getMockBuilder(FrontendUserRepository::class)
            ->setMethods(['findByUid', 'createQuery', 'setDefaultQuerySettings'])
            ->disableOriginalConstructor()
            ->getMock();
        $repository->expects($this->once())->method('setDefaultQuerySettings')->with($querySettings);
        $repository->expects($this->once())->method('createQuery')->willReturn($query);
        $repository->expects($this->once())->method('findByUid')->with(1)->willReturn($frontendUser);
        GeneralUtility::setSingletonInstance(FrontendUserRepository::class, $repository);

        $instance = $this->getMockBuilder($this->getViewHelperClassName())
            ->setMethods(['dummy'])
            ->getMockForAbstractClass();

        $result = $instance->getCurrentFrontendUser();
        $this->assertEquals($frontendUser, $result);
    }

    public function testRenderThenChildDisablesCacheInFrontendContext(): void
    {
        $GLOBALS['TSFE'] = (object) ['no_cache' => 0];
        $node = $this->getMockBuilder(ViewHelperNode::class)
            ->setMethods(['getChildNodes'])
            ->disableOriginalConstructor()
            ->getMock();
        $node->expects($this->any())->method('getChildNodes')->willReturn([]);
        $instance = $this->getMockBuilder($this->getViewHelperClassName())
            ->setMethods(['isFrontendContext', 'renderChildren'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $instance->setViewHelperNode($node);
        $instance->expects($this->once())->method('renderChildren')->willReturn('test');
        $instance->expects($this->once())->method('isFrontendContext')->willReturn(true);
        $this->callInaccessibleMethod($instance, 'renderThenChild');
        $this->assertEquals(1, $GLOBALS['TSFE']->no_cache);
        unset($GLOBALS['TSFE']);
    }
}
