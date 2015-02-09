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
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * Class AbstractSecurityViewHelperTest
 */
class AbstractSecurityViewHelperTest extends AbstractViewHelperTest {

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
			$this->getViewHelperClassName(), array(), '', FALSE, FALSE, FALSE, array('registerRenderMethodArguments')
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
		return array(
			array(
				array('anyFrontendUser' => TRUE),
				array('assertFrontendUserLoggedIn'),
				TRUE
			),
			array(
				array('anyFrontendUserGroup' => TRUE),
				array('assertFrontendUserGroupLoggedIn'),
				TRUE
			),
			array(
				array('frontendUser' => $frontendUser),
				array('assertFrontendUserLoggedIn'),
				TRUE
			),
			array(
				array('frontendUsers' => $frontendUsers),
				array('assertFrontendUsersLoggedIn'),
				TRUE
			),
			array(
				array('frontendUserGroup' => TRUE),
				array('assertFrontendUserGroupLoggedIn'),
				TRUE
			),
			array(
				array('frontendUserGroups' => TRUE),
				array('assertFrontendUserGroupLoggedIn'),
				TRUE
			),
			array(
				array('anyBackendUser' => TRUE),
				array('assertBackendUserLoggedIn'),
				TRUE
			),
			array(
				array('anyBackendUserGroup' => TRUE),
				array('assertBackendUserGroupLoggedIn'),
				TRUE
			),
			array(
				array('backendUser' => $backendUser),
				array('assertBackendUserLoggedIn'),
				TRUE
			),
			array(
				array('backendUsers' => $backendUsers),
				array('assertBackendUserLoggedIn'),
				TRUE
			),
			array(
				array('backendUserGroup' => $backendUserGroup),
				array('assertBackendUserGroupLoggedIn'),
				TRUE
			),
			array(
				array('backendUserGroups' => $backendUserGroups),
				array('assertBackendUserGroupLoggedIn'),
				TRUE
			),
			array(
				array('admin' => TRUE),
				array('assertAdminLoggedIn'),
				TRUE
			),
			array(
				array('admin' => TRUE, 'anyFrontendUser' => TRUE, 'evaluationMode' => 'AND'),
				array('assertAdminLoggedIn', 'assertFrontendUserLoggedIn'),
				TRUE
			),
			array(
				array('admin' => TRUE, 'anyFrontendUser' => TRUE, 'evaluationMode' => 'OR'),
				array('assertAdminLoggedIn', 'assertFrontendUserLoggedIn'),
				TRUE
			),
		);
	}

	/**
	 * @dataProvider getAssertFrontendUserLoggedInTestValues
	 * @param FrontendUser|NULL $user
	 * @param FrontendUser|NULL $resolvedUser
	 * @param boolean $expected
	 */
	public function testAssertFrontendUserLoggedIn($user, $resolvedUser, $expected) {
		$instance = $this->getMock($this->getViewHelperClassName(), array('getCurrentFrontendUser'));
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

		return array(
			array(NULL, NULL, FALSE),
			array($user1, NULL, FALSE),
			array(NULL, $user1, FALSE),
			array($user1, $user1, TRUE),
			array($user1, $user2, FALSE)
		);
	}

	/**
	 * @dataProvider getAssertFrontendUserGroupLoggedInTestValues
	 * @param FrontendUserGroup|NULL $group
	 * @param FrontendUser|NULL $resolvedUser
	 * @param boolean $expected
	 */
	public function testAssertFrontendUserGroupLoggedIn($group, $resolvedUser, $expected) {
		$instance = $this->getMock($this->getViewHelperClassName(), array('getCurrentFrontendUser'));
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
		return array(
			array(NULL, NULL, FALSE),
			array(NULL, $user1, FALSE),
			array(NULL, $user2, TRUE),
			array($frontendUserGroup, $user1, FALSE),
			array($frontendUserGroup, $user2, TRUE),
			array($frontendUserGroups, $user1, FALSE),
			array($frontendUserGroups, $user2, TRUE),
			array('unsupportedtype', $user1, FALSE)
		);
	}

}
