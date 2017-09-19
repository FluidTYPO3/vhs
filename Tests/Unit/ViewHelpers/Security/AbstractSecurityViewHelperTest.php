<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Security;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\ViewHelpers\Security\AbstractSecurityViewHelper;
use TYPO3\CMS\Extbase\Domain\Model\BackendUser;
use TYPO3\CMS\Extbase\Domain\Model\BackendUserGroup;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;
use TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;

/**
 * Class AbstractSecurityViewHelperTest
 */
class AbstractSecurityViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function canCreateViewHelperInstance()
    {
        $instance = $this->getMockBuilder($this->getViewHelperClassName())->getMock();
        $this->assertInstanceOf($this->getViewHelperClassName(), $instance);
    }

    /**
     * @test
     */
    public function canPrepareArguments()
    {
    }

    /**
     * @dataProvider getEvaluateArgumentsTestValues
     * @param array $arguments
     * @param array $expectedMethods
     * @param boolean $expectedReturn
     */
    public function testEvaluateArguments(array $arguments, array $expectedMethods, $expectedReturn)
    {
        $node = $this->getMockBuilder(ViewHelperNode::class)->setMethods(['getChildNodes'])->disableOriginalConstructor()->getMock();
        $node->expects($this->any())->method('getChildNodes')->willReturn([]);
        $instance = $this->getMockBuilder($this->getViewHelperClassName())->setMethods($expectedMethods)->getMockForAbstractClass();
        $instance->setViewHelperNode($node);
        foreach ($expectedMethods as $expectedMethod) {
            $instance->expects($this->once())->method($expectedMethod)->willReturn(true);
        }
        $instance->setArguments($arguments);
        $result = $this->callInaccessibleMethod($instance, 'evaluateArguments');
        $this->assertEquals($expectedReturn, $result);
    }

    /**
     * @return array
     */
    public function getEvaluateArgumentsTestValues()
    {
        $frontendUser = new FrontendUser();
        $frontendUsers = new ObjectStorage();
        $frontendUsers->attach($frontendUser);
        $frontendUserGroup = new FrontendUserGroup();
        $frontendUserGroups = new ObjectStorage();
        $frontendUserGroups->attach($frontendUserGroup);
        $backendUser = new BackendUser();
        $backendUsers = new ObjectStorage();
        $backendUsers->attach($backendUser);
        $backendUserGroup = new BackendUserGroup();
        $backendUserGroups = new ObjectStorage();
        $backendUserGroups->attach($backendUserGroup);
        return [
            [
                ['anyFrontendUser' => true],
                ['assertFrontendUserLoggedIn'],
                true
            ],
            [
                ['anyFrontendUserGroup' => true],
                ['assertFrontendUserGroupLoggedIn'],
                true
            ],
            [
                ['frontendUser' => $frontendUser],
                ['assertFrontendUserLoggedIn'],
                true
            ],
            [
                ['frontendUsers' => $frontendUsers],
                ['assertFrontendUsersLoggedIn'],
                true
            ],
            [
                ['frontendUserGroup' => true],
                ['assertFrontendUserGroupLoggedIn'],
                true
            ],
            [
                ['frontendUserGroups' => true],
                ['assertFrontendUserGroupLoggedIn'],
                true
            ],
            [
                ['anyBackendUser' => true],
                ['assertBackendUserLoggedIn'],
                true
            ],
            [
                ['anyBackendUserGroup' => true],
                ['assertBackendUserGroupLoggedIn'],
                true
            ],
            [
                ['backendUser' => $backendUser],
                ['assertBackendUserLoggedIn'],
                true
            ],
            [
                ['backendUsers' => $backendUsers],
                ['assertBackendUserLoggedIn'],
                true
            ],
            [
                ['backendUserGroup' => $backendUserGroup],
                ['assertBackendUserGroupLoggedIn'],
                true
            ],
            [
                ['backendUserGroups' => $backendUserGroups],
                ['assertBackendUserGroupLoggedIn'],
                true
            ],
            [
                ['admin' => true],
                ['assertAdminLoggedIn'],
                true
            ],
            [
                ['admin' => true, 'anyFrontendUser' => true, 'evaluationMode' => 'AND'],
                ['assertAdminLoggedIn', 'assertFrontendUserLoggedIn'],
                true
            ],
            [
                ['admin' => true, 'anyFrontendUser' => true, 'evaluationMode' => 'OR'],
                ['assertAdminLoggedIn', 'assertFrontendUserLoggedIn'],
                true
            ],
        ];
    }

    /**
     * @dataProvider getAssertFrontendUserLoggedInTestValues
     * @param FrontendUser|NULL $user
     * @param FrontendUser|NULL $resolvedUser
     * @param boolean $expected
     */
    public function testAssertFrontendUserLoggedIn($user, $resolvedUser, $expected)
    {
        $instance = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['getCurrentFrontendUser'])->getMockForAbstractClass();
        $instance->expects($this->once())->method('getCurrentFrontendUser')->willReturn($resolvedUser);
        $result = $this->callInaccessibleMethod($instance, 'assertFrontendUserLoggedIn', $user);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getAssertFrontendUserLoggedInTestValues()
    {
        $user1 = new FrontendUser();
        ObjectAccess::setProperty($user1, 'uid', 1, true);
        $user2 = new FrontendUser();
        ObjectAccess::setProperty($user2, 'uid', 2, true);

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
     * @param FrontendUserGroup|NULL $group
     * @param FrontendUser|NULL $resolvedUser
     * @param boolean $expected
     */
    public function testAssertFrontendUserGroupLoggedIn($group, $resolvedUser, $expected)
    {
        $instance = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['getCurrentFrontendUser'])->getMockForAbstractClass();
        $instance->expects($this->once())->method('getCurrentFrontendUser')->willReturn($resolvedUser);
        $result = $this->callInaccessibleMethod($instance, 'assertFrontendUserGroupLoggedIn', $group);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getAssertFrontendUserGroupLoggedInTestValues()
    {
        $frontendUserGroup = new FrontendUserGroup();
        $frontendUserGroups = new ObjectStorage();
        $frontendUserGroups->attach($frontendUserGroup);
        $user1 = new FrontendUser();
        ObjectAccess::setProperty($user1, 'uid', 1, true);
        $user2 = new FrontendUser();
        $user2->setUsergroup($frontendUserGroups);
        ObjectAccess::setProperty($user2, 'uid', 2, true);
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
     * @param ObjectStorage $users
     * @param FrontendUser $currentUser
     * @param boolean $expected
     */
    public function testAssertFrontendUsersLoggedIn(ObjectStorage $users, FrontendUser $currentUser, $expected)
    {
        $instance = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['getCurrentFrontendUser'])->getMockForAbstractClass();
        $instance->expects($this->exactly($users->count()))->method('getCurrentFrontendUser')->willReturn($currentUser);
        $result = $instance->assertFrontendUsersLoggedIn($users);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getAssertFrontendUsersLoggedInTestValues()
    {
        $user1 = new FrontendUser();
        ObjectAccess::setProperty($user1, 'uid', 1, true);
        $user2 = new FrontendUser();
        ObjectAccess::setProperty($user2, 'uid', 2, true);
        $user3 = new FrontendUser();
        ObjectAccess::setProperty($user3, 'uid', 3, true);

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
     * @param ingeger $user
     * @param integer $currentUser
     * @param boolean $expected
     */
    public function testAssertBackendUserLoggedIn($user, $currentUser, $expected)
    {
        $GLOBALS['BE_USER'] = (object) ['user' => ['uid' => $currentUser]];
        $instance = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['dummy'])->getMockForAbstractClass();
        $result = $instance->assertBackendUserLoggedIn($user);
        unset($GLOBALS['BE_USER']->user);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getAssertBackendUserLoggedInTestValues()
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
     * @param ingeger $group
     * @param array|NULL $currentUser
     * @param boolean $expected
     */
    public function testAssertBackendUserGroupLoggedIn($group, $currentUser, $expected)
    {
        $GLOBALS['BE_USER'] = (object) ['user' => $currentUser];
        $instance = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['dummy'])->getMockForAbstractClass();
        $result = $instance->assertBackendUserGroupLoggedIn($group);
        unset($GLOBALS['BE_USER']);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getAssertBackendUserGroupLoggedInTestValues()
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
     * @param array|NULL $currentUser
     * @param boolean $expected
     */
    public function testAssertAdminLoggedIn($currentUser, $expected)
    {
        $instance = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['getCurrentBackendUser'])->getMockForAbstractClass();
        $instance->expects($this->atLeastOnce())->method('getCurrentBackendUser')->willReturn($currentUser);
        $result = $instance->assertAdminLoggedIn();
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getAssertAdminLoggedInTestValues()
    {
        return [
            [null, false],
            [['uid' => 1, 'admin' => 0], false],
            [['uid' => 1, 'admin' => 1], true]
        ];
    }

    /**
     * @return void
     */
    public function testGetCurrentFrontendUserReturnsNullIfNoFrontendUserRecordIsSetInFrontendController()
    {
        $GLOBALS['TSFE'] = (object) ['loginUser' => ''];
        $instance = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['dummy'])->getMockForAbstractClass();
        $result = $instance->getCurrentFrontendUser();
        $this->assertNull($result);
        unset($GLOBALS['TSFE']);
    }

    /**
     * @return void
     */
    public function testGetCurrentFrontendUserFetchesFromFrontendUserRepository()
    {
        $GLOBALS['TSFE'] = (object) ['loginUser' => 1, 'fe_user' => (object) ['user' => ['uid' => 1]]];
        $instance = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['dummy'])->getMockForAbstractClass();
        $query = new Query(FrontendUser::class);
        $querySettings = new Typo3QuerySettings();
        $query->setQuerySettings($querySettings);
        $repository = $this->getMockBuilder(FrontendUserRepository::class)->setMethods(['findByUid', 'createQuery', 'setDefaultQuerySettings'])->disableOriginalConstructor()->getMock();
        $repository->expects($this->once())->method('setDefaultQuerySettings')->with($querySettings);
        $repository->expects($this->once())->method('createQuery')->willReturn($query);
        $repository->expects($this->once())->method('findByUid')->with(1)->willReturn('test');
        $instance->injectFrontendUserRepository($repository);
        $result = $instance->getCurrentFrontendUser();
        $this->assertEquals('test', $result);
    }

    /**
     * @return void
     */
    public function testRenderThenChildDisablesCacheInFrontendContext()
    {
        $GLOBALS['TSFE'] = (object) ['no_cache' => 0];
        $node = $this->getMockBuilder(ViewHelperNode::class)->setMethods(['getChildNodes'])->disableOriginalConstructor()->getMock();
        $node->expects($this->any())->method('getChildNodes')->willReturn([]);
        $instance = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['isFrontendContext', 'renderChildren'])->getMockForAbstractClass();
        $instance->setViewHelperNode($node);
        $instance->expects($this->once())->method('renderChildren')->willReturn('test');
        $instance->expects($this->once())->method('isFrontendContext')->willReturn(true);
        $this->callInaccessibleMethod($instance, 'renderThenChild');
        $this->assertEquals(1, $GLOBALS['TSFE']->no_cache);
        unset($GLOBALS['TSFE']);
    }

    /**
     * @return void
     */
    public function testIsFrontendContextReturnsFalse()
    {
        $instance = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['dummy'])->getMockForAbstractClass();
        $result = $this->callInaccessibleMethod($instance, 'isFrontendContext');
        $this->assertFalse($result);
    }
}
