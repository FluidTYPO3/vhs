<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Security;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use TYPO3\CMS\Extbase\Domain\Model\BackendUser;
use TYPO3\CMS\Extbase\Domain\Model\BackendUserGroup;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * Class AbstractSecurityViewHelperTest
 */
class AbstractSecurityViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @return void
     */
    public function testRenderOnAbstractClassReturnsNullByDefault()
    {
        $instance = $this->getMockForAbstractClass('FluidTYPO3\\Vhs\\ViewHelpers\\Security\\AbstractSecurityViewHelper');
        $result = $instance->render();
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function canCreateViewHelperInstance()
    {
        $instance = $this->getMockForAbstractClass($this->getViewHelperClassName());
        $instance->injectReflectionService($this->objectManager->get('TYPO3\\CMS\\Extbase\\Reflection\\ReflectionService'));
        $this->assertInstanceOf($this->getViewHelperClassName(), $instance);
    }

    /**
     * @test
     */
    public function canPrepareArguments()
    {
        $instance = $this->getMockForAbstractClass(
            $this->getViewHelperClassName(),
            array(),
            '',
            false,
            false,
            false,
            array('registerRenderMethodArguments')
        );
        $instance->expects($this->any())->method('registerRenderMethodArguments');
        $instance->injectReflectionService($this->objectManager->get('TYPO3\\CMS\\Extbase\\Reflection\\ReflectionService'));
        $this->assertNotEmpty($instance->prepareArguments());
    }

    /**
     * @dataProvider getEvaluateArgumentsTestValues
     * @param array $arguments
     * @param array $expectedMethods
     * @param boolean $expectedReturn
     */
    public function testEvaluateArguments(array $arguments, array $expectedMethods, $expectedReturn)
    {
        $instance = $this->getMock($this->getViewHelperClassName(), $expectedMethods);
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
        return array(
            array(
                array('anyFrontendUser' => true),
                array('assertFrontendUserLoggedIn'),
                true
            ),
            array(
                array('anyFrontendUserGroup' => true),
                array('assertFrontendUserGroupLoggedIn'),
                true
            ),
            array(
                array('frontendUser' => $frontendUser),
                array('assertFrontendUserLoggedIn'),
                true
            ),
            array(
                array('frontendUsers' => $frontendUsers),
                array('assertFrontendUsersLoggedIn'),
                true
            ),
            array(
                array('frontendUserGroup' => true),
                array('assertFrontendUserGroupLoggedIn'),
                true
            ),
            array(
                array('frontendUserGroups' => true),
                array('assertFrontendUserGroupLoggedIn'),
                true
            ),
            array(
                array('anyBackendUser' => true),
                array('assertBackendUserLoggedIn'),
                true
            ),
            array(
                array('anyBackendUserGroup' => true),
                array('assertBackendUserGroupLoggedIn'),
                true
            ),
            array(
                array('backendUser' => $backendUser),
                array('assertBackendUserLoggedIn'),
                true
            ),
            array(
                array('backendUsers' => $backendUsers),
                array('assertBackendUserLoggedIn'),
                true
            ),
            array(
                array('backendUserGroup' => $backendUserGroup),
                array('assertBackendUserGroupLoggedIn'),
                true
            ),
            array(
                array('backendUserGroups' => $backendUserGroups),
                array('assertBackendUserGroupLoggedIn'),
                true
            ),
            array(
                array('admin' => true),
                array('assertAdminLoggedIn'),
                true
            ),
            array(
                array('admin' => true, 'anyFrontendUser' => true, 'evaluationMode' => 'AND'),
                array('assertAdminLoggedIn', 'assertFrontendUserLoggedIn'),
                true
            ),
            array(
                array('admin' => true, 'anyFrontendUser' => true, 'evaluationMode' => 'OR'),
                array('assertAdminLoggedIn', 'assertFrontendUserLoggedIn'),
                true
            ),
        );
    }

    /**
     * @dataProvider getAssertFrontendUserLoggedInTestValues
     * @param FrontendUser|NULL $user
     * @param FrontendUser|NULL $resolvedUser
     * @param boolean $expected
     */
    public function testAssertFrontendUserLoggedIn($user, $resolvedUser, $expected)
    {
        $instance = $this->getMock($this->getViewHelperClassName(), array('getCurrentFrontendUser'));
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

        return array(
            array(null, null, false),
            array($user1, null, false),
            array(null, $user1, true),
            array($user1, $user1, true),
            array($user1, $user2, false)
        );
    }

    /**
     * @dataProvider getAssertFrontendUserGroupLoggedInTestValues
     * @param FrontendUserGroup|NULL $group
     * @param FrontendUser|NULL $resolvedUser
     * @param boolean $expected
     */
    public function testAssertFrontendUserGroupLoggedIn($group, $resolvedUser, $expected)
    {
        $instance = $this->getMock($this->getViewHelperClassName(), array('getCurrentFrontendUser'));
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
        return array(
            array(null, null, false),
            array(null, $user1, false),
            array(null, $user2, true),
            array($frontendUserGroup, $user1, false),
            array($frontendUserGroup, $user2, true),
            array($frontendUserGroups, $user1, false),
            array($frontendUserGroups, $user2, true),
            array('unsupportedtype', $user1, false)
        );
    }

    /**
     * @dataProvider getAssertFrontendUsersLoggedInTestValues
     * @param ObjectStorage $users
     * @param FrontendUser $currentUser
     * @param boolean $expected
     */
    public function testAssertFrontendUsersLoggedIn(ObjectStorage $users, FrontendUser $currentUser, $expected)
    {
        $instance = $this->getMock($this->getViewHelperClassName(), array('getCurrentFrontendUser'));
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
        return array(
            array($users, $notContained, false),
            array($users, $contained, true),
        );
    }

    /**
     * @dataProvider getAssertBackendUserLoggedInTestValues
     * @param ingeger $user
     * @param integer $currentUser
     * @param boolean $expected
     */
    public function testAssertBackendUserLoggedIn($user, $currentUser, $expected)
    {
        $GLOBALS['BE_USER'] = (object) array('user' => array('uid' => $currentUser));
        $instance = $this->getMock($this->getViewHelperClassName(), array('dummy'));
        $result = $instance->assertBackendUserLoggedIn($user);
        unset($GLOBALS['BE_USER']->user);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getAssertBackendUserLoggedInTestValues()
    {
        return array(
            array(1, 0, false),
            array(2, 4, false),
            array(3, 3, true),
            array(2, 2, true),
            array(null, 1, true),
            array(1, null, false)
        );
    }

    /**
     * @dataProvider getAssertBackendUserGroupLoggedInTestValues
     * @param ingeger $group
     * @param array|NULL $currentUser
     * @param boolean $expected
     */
    public function testAssertBackendUserGroupLoggedIn($group, $currentUser, $expected)
    {
        $GLOBALS['BE_USER'] = (object) array('user' => $currentUser);
        $instance = $this->getMock($this->getViewHelperClassName(), array('dummy'));
        $result = $instance->assertBackendUserGroupLoggedIn($group);
        unset($GLOBALS['BE_USER']);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getAssertBackendUserGroupLoggedInTestValues()
    {
        return array(
            array(null, null, false),
            array(array(), array('uid' => 1, 'usergroup' => '1,2,3'), false),
            array(array(1), array('uid' => 1, 'usergroup' => ''), false),
            array(array(1), array('uid' => 1, 'usergroup' => '1,2,3'), true),
            array(array(1,9), array('uid' => 1, 'usergroup' => '1,2,3'), true),
            array(array(4,5), array('uid' => 1, 'usergroup' => '1,2,3'), false),
            array(array(1,7), array('uid' => 1, 'usergroup' => '1,2,3'), true),
            array(array(4,8), array('uid' => 1, 'usergroup' => '1,2,3'), false),
            array('1,7', array('uid' => 1, 'usergroup' => '1,2,3'), true),
            array('4,8,', array('uid' => 1, 'usergroup' => '1,2,3'), false)
        );
    }

    /**
     * @dataProvider getAssertAdminLoggedInTestValues
     * @param array|NULL $currentUser
     * @param boolean $expected
     */
    public function testAssertAdminLoggedIn($currentUser, $expected)
    {
        $instance = $this->getMock($this->getViewHelperClassName(), array('getCurrentBackendUser'));
        $instance->expects($this->atLeastOnce())->method('getCurrentBackendUser')->willReturn($currentUser);
        $result = $instance->assertAdminLoggedIn();
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getAssertAdminLoggedInTestValues()
    {
        return array(
            array(null, false),
            array(array('uid' => 1, 'admin' => 0), false),
            array(array('uid' => 1, 'admin' => 1), true)
        );
    }

    /**
     * @return void
     */
    public function testGetCurrentFrontendUserReturnsNullIfNoFrontendUserRecordIsSetInFrontendController()
    {
        $GLOBALS['TSFE'] = (object) array('loginUser' => '');
        $instance = $this->getMock($this->getViewHelperClassName(), array('dummy'));
        $result = $instance->getCurrentFrontendUser();
        $this->assertNull($result);
        unset($GLOBALS['TSFE']);
    }

    /**
     * @return void
     */
    public function testGetCurrentFrontendUserFetchesFromFrontendUserRepository()
    {
        $GLOBALS['TSFE'] = (object) array('loginUser' => 1, 'fe_user' => (object) array('user' => array('uid' => 1)));
        $instance = $this->getMock($this->getViewHelperClassName(), array('dummy'));
        $query = new Query('TYPO3\\CMS\\Extbase\\Domain\\Model\\FrontendUser');
        $querySettings = new Typo3QuerySettings();
        $query->setQuerySettings($querySettings);
        $repository = $this->getMock(
            'TYPO3\\CMS\\Extbase\\Domain\\Repository\\FrontendUserRepository',
            array('findByUid', 'createQuery', 'setDefaultQuerySettings'),
            array(),
            '',
            false
        );
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
        $GLOBALS['TSFE'] = (object) array('no_cache' => 0);
        $instance = $this->getMock($this->getViewHelperClassName(), array('isFrontendContext', 'renderChildren'));
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
        $instance = $this->getMock($this->getViewHelperClassName(), array('dummy'));
        $result = $this->callInaccessibleMethod($instance, 'isFrontendContext');
        $this->assertFalse($result);
    }
}
