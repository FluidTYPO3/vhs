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
class AbstractSecurityViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @return void
	 */
	public function testRenderOnAbstractClassReturnsNullByDefault() {
		$instance = $this->getMockForAbstractClass('FluidTYPO3\\Vhs\\ViewHelpers\\Security\\AbstractSecurityViewHelper');
		$result = $instance->render();
		$this->assertNull($result);
	}

	/**
	 * @test
	 */
	public function canCreateViewHelperInstance() {
		$instance = $this->getMockForAbstractClass($this->getViewHelperClassName());
		$instance->injectReflectionService($this->objectManager->get('TYPO3\\CMS\\Extbase\\Reflection\\ReflectionService'));
		$this->assertInstanceOf($this->getViewHelperClassName(), $instance);
	}

	/**
	 * @test
	 */
	public function canPrepareArguments() {
		$instance = $this->getMockForAbstractClass(
			$this->getViewHelperClassName(), [], '', FALSE, FALSE, FALSE, ['registerRenderMethodArguments']
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
	public function testEvaluateArguments(array $arguments, array $expectedMethods, $expectedReturn) {
		$instance = $this->getMock($this->getViewHelperClassName(), $expectedMethods);
		foreach ($expectedMethods as $expectedMethod) {
			$instance->expects($this->once())->method($expectedMethod)->willReturn(TRUE);
		}
		$instance->setArguments($arguments);
		$result = $this->callInaccessibleMethod($instance, 'evaluateArguments');
		$this->assertEquals($expectedReturn, $result);
	}

	/**
	 * @return array
	 */
	public function getEvaluateArgumentsTestValues() {
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
				['anyFrontendUser' => TRUE],
				['assertFrontendUserLoggedIn'],
				TRUE
			],
			[
				['anyFrontendUserGroup' => TRUE],
				['assertFrontendUserGroupLoggedIn'],
				TRUE
			],
			[
				['frontendUser' => $frontendUser],
				['assertFrontendUserLoggedIn'],
				TRUE
			],
			[
				['frontendUsers' => $frontendUsers],
				['assertFrontendUsersLoggedIn'],
				TRUE
			],
			[
				['frontendUserGroup' => TRUE],
				['assertFrontendUserGroupLoggedIn'],
				TRUE
			],
			[
				['frontendUserGroups' => TRUE],
				['assertFrontendUserGroupLoggedIn'],
				TRUE
			],
			[
				['anyBackendUser' => TRUE],
				['assertBackendUserLoggedIn'],
				TRUE
			],
			[
				['anyBackendUserGroup' => TRUE],
				['assertBackendUserGroupLoggedIn'],
				TRUE
			],
			[
				['backendUser' => $backendUser],
				['assertBackendUserLoggedIn'],
				TRUE
			],
			[
				['backendUsers' => $backendUsers],
				['assertBackendUserLoggedIn'],
				TRUE
			],
			[
				['backendUserGroup' => $backendUserGroup],
				['assertBackendUserGroupLoggedIn'],
				TRUE
			],
			[
				['backendUserGroups' => $backendUserGroups],
				['assertBackendUserGroupLoggedIn'],
				TRUE
			],
			[
				['admin' => TRUE],
				['assertAdminLoggedIn'],
				TRUE
			],
			[
				['admin' => TRUE, 'anyFrontendUser' => TRUE, 'evaluationMode' => 'AND'],
				['assertAdminLoggedIn', 'assertFrontendUserLoggedIn'],
				TRUE
			],
			[
				['admin' => TRUE, 'anyFrontendUser' => TRUE, 'evaluationMode' => 'OR'],
				['assertAdminLoggedIn', 'assertFrontendUserLoggedIn'],
				TRUE
			],
		];
	}

	/**
	 * @dataProvider getAssertFrontendUserLoggedInTestValues
	 * @param FrontendUser|NULL $user
	 * @param FrontendUser|NULL $resolvedUser
	 * @param boolean $expected
	 */
	public function testAssertFrontendUserLoggedIn($user, $resolvedUser, $expected) {
		$instance = $this->getMock($this->getViewHelperClassName(), ['getCurrentFrontendUser']);
		$instance->expects($this->once())->method('getCurrentFrontendUser')->willReturn($resolvedUser);
		$result = $this->callInaccessibleMethod($instance, 'assertFrontendUserLoggedIn', $user);
		$this->assertEquals($expected, $result);
	}

	/**
	 * @return array
	 */
	public function getAssertFrontendUserLoggedInTestValues() {
		$user1 = new FrontendUser();
		ObjectAccess::setProperty($user1, 'uid', 1, TRUE);
		$user2 = new FrontendUser();
		ObjectAccess::setProperty($user2, 'uid', 2, TRUE);

		return [
			[NULL, NULL, FALSE],
			[$user1, NULL, FALSE],
			[NULL, $user1, TRUE],
			[$user1, $user1, TRUE],
			[$user1, $user2, FALSE]
		];
	}

	/**
	 * @dataProvider getAssertFrontendUserGroupLoggedInTestValues
	 * @param FrontendUserGroup|NULL $group
	 * @param FrontendUser|NULL $resolvedUser
	 * @param boolean $expected
	 */
	public function testAssertFrontendUserGroupLoggedIn($group, $resolvedUser, $expected) {
		$instance = $this->getMock($this->getViewHelperClassName(), ['getCurrentFrontendUser']);
		$instance->expects($this->once())->method('getCurrentFrontendUser')->willReturn($resolvedUser);
		$result = $this->callInaccessibleMethod($instance, 'assertFrontendUserGroupLoggedIn', $group);
		$this->assertEquals($expected, $result);
	}

	/**
	 * @return array
	 */
	public function getAssertFrontendUserGroupLoggedInTestValues() {
		$frontendUserGroup = new FrontendUserGroup();
		$frontendUserGroups = new ObjectStorage();
		$frontendUserGroups->attach($frontendUserGroup);
		$user1 = new FrontendUser();
		ObjectAccess::setProperty($user1, 'uid', 1, TRUE);
		$user2 = new FrontendUser();
		$user2->setUsergroup($frontendUserGroups);
		ObjectAccess::setProperty($user2, 'uid', 2, TRUE);
		return [
			[NULL, NULL, FALSE],
			[NULL, $user1, FALSE],
			[NULL, $user2, TRUE],
			[$frontendUserGroup, $user1, FALSE],
			[$frontendUserGroup, $user2, TRUE],
			[$frontendUserGroups, $user1, FALSE],
			[$frontendUserGroups, $user2, TRUE],
			['unsupportedtype', $user1, FALSE]
		];
	}

	/**
	 * @dataProvider getAssertFrontendUsersLoggedInTestValues
	 * @param ObjectStorage $users
	 * @param FrontendUser $currentUser
	 * @param boolean $expected
	 */
	public function testAssertFrontendUsersLoggedIn(ObjectStorage $users, FrontendUser $currentUser, $expected) {
		$instance = $this->getMock($this->getViewHelperClassName(), ['getCurrentFrontendUser']);
		$instance->expects($this->exactly($users->count()))->method('getCurrentFrontendUser')->willReturn($currentUser);
		$result = $instance->assertFrontendUsersLoggedIn($users);
		$this->assertEquals($expected, $result);
	}

	/**
	 * @return array
	 */
	public function getAssertFrontendUsersLoggedInTestValues() {
		$user1 = new FrontendUser();
		ObjectAccess::setProperty($user1, 'uid', 1, TRUE);
		$user2 = new FrontendUser();
		ObjectAccess::setProperty($user2, 'uid', 2, TRUE);
		$user3 = new FrontendUser();
		ObjectAccess::setProperty($user3, 'uid', 3, TRUE);

		$users = new ObjectStorage();
		$users->attach($user1);
		$users->attach($user2);
		$contained = $user2;
		$notContained = $user3;
		return [
			[$users, $notContained, FALSE],
			[$users, $contained, TRUE],
		];
	}

	/**
	 * @dataProvider getAssertBackendUserLoggedInTestValues
	 * @param ingeger $user
	 * @param integer $currentUser
	 * @param boolean $expected
	 */
	public function testAssertBackendUserLoggedIn($user, $currentUser, $expected) {
		$GLOBALS['BE_USER'] = (object) ['user' => ['uid' => $currentUser]];
		$instance = $this->getMock($this->getViewHelperClassName(), ['dummy']);
		$result = $instance->assertBackendUserLoggedIn($user);
		unset($GLOBALS['BE_USER']->user);
		$this->assertEquals($expected, $result);
	}

	/**
	 * @return array
	 */
	public function getAssertBackendUserLoggedInTestValues() {
		return [
			[1, 0, FALSE],
			[2, 4, FALSE],
			[3, 3, TRUE],
			[2, 2, TRUE],
			[NULL, 1, TRUE],
			[1, NULL, FALSE]
		];
	}

	/**
	 * @dataProvider getAssertBackendUserGroupLoggedInTestValues
	 * @param ingeger $group
	 * @param array|NULL $currentUser
	 * @param boolean $expected
	 */
	public function testAssertBackendUserGroupLoggedIn($group, $currentUser, $expected) {
		$GLOBALS['BE_USER'] = (object) ['user' => $currentUser];
		$instance = $this->getMock($this->getViewHelperClassName(), ['dummy']);
		$result = $instance->assertBackendUserGroupLoggedIn($group);
		unset($GLOBALS['BE_USER']);
		$this->assertEquals($expected, $result);
	}

	/**
	 * @return array
	 */
	public function getAssertBackendUserGroupLoggedInTestValues() {
		return [
			[NULL, NULL, FALSE],
			[[], ['uid' => 1, 'usergroup' => '1,2,3'], FALSE],
			[[1], ['uid' => 1, 'usergroup' => ''], FALSE],
			[[1], ['uid' => 1, 'usergroup' => '1,2,3'], TRUE],
			[[1,9], ['uid' => 1, 'usergroup' => '1,2,3'], TRUE],
			[[4,5], ['uid' => 1, 'usergroup' => '1,2,3'], FALSE],
			[[1,7], ['uid' => 1, 'usergroup' => '1,2,3'], TRUE],
			[[4,8], ['uid' => 1, 'usergroup' => '1,2,3'], FALSE],
			['1,7', ['uid' => 1, 'usergroup' => '1,2,3'], TRUE],
			['4,8,', ['uid' => 1, 'usergroup' => '1,2,3'], FALSE]
		];
	}

	/**
	 * @dataProvider getAssertAdminLoggedInTestValues
	 * @param array|NULL $currentUser
	 * @param boolean $expected
	 */
	public function testAssertAdminLoggedIn($currentUser, $expected) {
		$instance = $this->getMock($this->getViewHelperClassName(), ['getCurrentBackendUser']);
		$instance->expects($this->atLeastOnce())->method('getCurrentBackendUser')->willReturn($currentUser);
		$result = $instance->assertAdminLoggedIn();
		$this->assertEquals($expected, $result);
	}

	/**
	 * @return array
	 */
	public function getAssertAdminLoggedInTestValues() {
		return [
			[NULL, FALSE],
			[['uid' => 1, 'admin' => 0], FALSE],
			[['uid' => 1, 'admin' => 1], TRUE]
		];
	}

	/**
	 * @return void
	 */
	public function testGetCurrentFrontendUserReturnsNullIfNoFrontendUserRecordIsSetInFrontendController() {
		$GLOBALS['TSFE'] = (object) ['loginUser' => ''];
		$instance = $this->getMock($this->getViewHelperClassName(), ['dummy']);
		$result = $instance->getCurrentFrontendUser();
		$this->assertNull($result);
		unset($GLOBALS['TSFE']);
	}

	/**
	 * @return void
	 */
	public function testGetCurrentFrontendUserFetchesFromFrontendUserRepository() {
		$GLOBALS['TSFE'] = (object) ['loginUser' => 1, 'fe_user' => (object) ['user' => ['uid' => 1]]];
		$instance = $this->getMock($this->getViewHelperClassName(), ['dummy']);
		$query = new Query('TYPO3\\CMS\\Extbase\\Domain\\Model\\FrontendUser');
		$querySettings = new Typo3QuerySettings();
		$query->setQuerySettings($querySettings);
		$repository = $this->getMock(
			'TYPO3\\CMS\\Extbase\\Domain\\Repository\\FrontendUserRepository',
			['findByUid', 'createQuery', 'setDefaultQuerySettings'], [], '', FALSE
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
	public function testRenderThenChildDisablesCacheInFrontendContext() {
		$GLOBALS['TSFE'] = (object) ['no_cache' => 0];
		$instance = $this->getMock($this->getViewHelperClassName(), ['isFrontendContext', 'renderChildren']);
		$instance->expects($this->once())->method('renderChildren')->willReturn('test');
		$instance->expects($this->once())->method('isFrontendContext')->willReturn(TRUE);
		$this->callInaccessibleMethod($instance, 'renderThenChild');
		$this->assertEquals(1, $GLOBALS['TSFE']->no_cache);
		unset($GLOBALS['TSFE']);
	}

	/**
	 * @return void
	 */
	public function testIsFrontendContextReturnsFalse() {
		$instance = $this->getMock($this->getViewHelperClassName(), ['dummy']);
		$result = $this->callInaccessibleMethod($instance, 'isFrontendContext');
		$this->assertFalse($result);
	}

}
