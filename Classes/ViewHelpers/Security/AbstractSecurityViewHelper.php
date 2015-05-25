<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Security;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * ### Base class: Security ViewHelpers
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Security
 */
abstract class AbstractSecurityViewHelper extends AbstractConditionViewHelper {

	/**
	 * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
	 */
	protected $frontendUserRepository;

	/**
	 * @return NULL
	 */
	public function render() {
		return NULL;
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository $frontendUserRepository
	 * @return void
	 */
	public function injectFrontendUserRepository(FrontendUserRepository $frontendUserRepository) {
		$this->frontendUserRepository = $frontendUserRepository;
		$query = $this->frontendUserRepository->createQuery();
		$querySettings = $query->getQuerySettings();
		$querySettings->setRespectStoragePage(FALSE);
		$querySettings->setRespectSysLanguage(FALSE);
		$this->frontendUserRepository->setDefaultQuerySettings($querySettings);
	}

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('anyFrontendUser', 'boolean', 'If TRUE, allows any FrontendUser unless other arguments disallows each specific FrontendUser', FALSE, FALSE);
		$this->registerArgument('anyFrontendUserGroup', 'boolean', 'If TRUE, allows any FrontendUserGroup unless other arguments disallows each specific FrontendUser', FALSE, FALSE);
		$this->registerArgument('frontendUser', 'TYPO3\CMS\Extbase\Domain\Model\FrontendUser', 'The FrontendUser to allow/deny');
		$this->registerArgument('frontendUsers', '<TYPO3\CMS\Extbase\Persistence\ObjectStorage>\TYPO3\CMS\Extbase\Domain\Model\FrontendUser', 'The FrontendUsers ObjectStorage to allow/deny');
		$this->registerArgument('frontendUserGroup', 'TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup', 'The FrontendUserGroup to allow/deny');
		$this->registerArgument('frontendUserGroups', '<TYPO3\CMS\Extbase\Persistence\ObjectStorage>\TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup', 'The FrontendUserGroups ObjectStorage to allow/deny');
		$this->registerArgument('anyBackendUser', 'boolean', 'If TRUE, allows any backend user unless other arguments disallows each specific backend user', FALSE, FALSE);
		$this->registerArgument('backendUser', 'integer', 'The uid of a backend user to allow/deny');
		$this->registerArgument('backendUsers', 'mixed', 'The backend users list to allow/deny. If string, CSV of uids is assumed, if array, array of uids is assumed');
		$this->registerArgument('backendUserGroup', 'integer', 'The uid of the backend user group to allow/deny');
		$this->registerArgument('backendUserGroups', 'mixed', 'The backend user groups list to allow/deny. If string, CSV of uids is assumed, if array, array of uids is assumed');
		$this->registerArgument('admin', 'boolean', 'If TRUE, a backend user which is also an admin is required');
		$this->registerArgument('evaluationType', 'string', 'Specify AND or OR (case sensitive) to determine how arguments must be processed. Default is AND, requiring all arguments to be satisfied if used', FALSE, 'AND');
	}

	/**
	 * Returns TRUE if all conditions from arguments are satisfied. The
	 * type of evaluation (AND or OR) can be set using argument "evaluationType"
	 *
	 * @return boolean
	 */
	protected function evaluateArguments() {
		$evaluationType = $this->arguments['evaluationType'];
		$evaluations = array();
		if (TRUE === (boolean) $this->arguments['anyFrontendUser']) {
			$evaluations['anyFrontendUser'] = intval($this->assertFrontendUserLoggedIn());
		}
		if (TRUE === (boolean) $this->arguments['anyFrontendUserGroup']) {
			$evaluations['anyFrontendUserGroup'] = intval($this->assertFrontendUserGroupLoggedIn());
		}
		if (TRUE === isset($this->arguments['frontendUser'])) {
			$evaluations['frontendUser'] = intval($this->assertFrontendUserLoggedIn($this->arguments['frontendUser']));
		}
		if (TRUE === isset($this->arguments['frontendUsers'])) {
			$evaluations['frontendUsers'] = intval($this->assertFrontendUsersLoggedIn($this->arguments['frontendUsers']));
		}
		if (TRUE === isset($this->arguments['frontendUserGroup'])) {
			$evaluations['frontendUserGroup'] = intval($this->assertFrontendUserGroupLoggedIn($this->arguments['frontendUserGroup']));
		}
		if (TRUE === isset($this->arguments['frontendUserGroups'])) {
			$evaluations['frontendUserGroups'] = intval($this->assertFrontendUserGroupLoggedIn($this->arguments['frontendUserGroups']));
		}
		if (TRUE === (boolean) $this->arguments['anyBackendUser']) {
			$evaluations['anyBackendUser'] = intval($this->assertBackendUserLoggedIn());
		}
		if (TRUE === (boolean) $this->arguments['anyBackendUserGroup']) {
			$evaluations['anyBackendUserGroup'] = intval($this->assertBackendUserGroupLoggedIn());
		}
		if (TRUE === isset($this->arguments['backendUser'])) {
			$evaluations['backendUser'] = intval($this->assertBackendUserLoggedIn($this->arguments['backendUser']));
		}
		if (TRUE === isset($this->arguments['backendUsers'])) {
			$evaluations['backendUsers'] = intval($this->assertBackendUserLoggedIn($this->arguments['backendUsers']));
		}
		if (TRUE === isset($this->arguments['backendUserGroup'])) {
			$evaluations['backendUserGroup'] = intval($this->assertBackendUserGroupLoggedIn($this->arguments['backendUserGroup']));
		}
		if (TRUE === isset($this->arguments['backendUserGroups'])) {
			$evaluations['backendUserGroups'] = intval($this->assertBackendUserGroupLoggedIn($this->arguments['backendUserGroups']));
		}
		if (TRUE === (boolean) $this->arguments['admin']) {
			$evaluations['admin'] = intval($this->assertAdminLoggedIn());
		}
		$sum = array_sum($evaluations);
		return (boolean) ('AND' === $evaluationType ? count($evaluations) === $sum : $sum > 0);
	}

	/**
	 * Returns TRUE only if a FrontendUser is currently logged in. Use argument
	 * to return TRUE only if the FrontendUser logged in must be that specific user.
	 *
	 * @param FrontendUser $frontendUser
	 * @return boolean
	 * @api
	 */
	public function assertFrontendUserLoggedIn(FrontendUser $frontendUser = NULL) {
		$currentFrontendUser = $this->getCurrentFrontendUser();
		if (FALSE === $currentFrontendUser instanceof FrontendUser) {
			return FALSE;
		}
		if (TRUE === $frontendUser instanceof FrontendUser && TRUE === $currentFrontendUser instanceof FrontendUser) {
			if ($currentFrontendUser->getUid() === $frontendUser->getUid()) {
				return TRUE;
			} else {
				return FALSE;
			}
		}
		return (boolean) (TRUE === is_object($currentFrontendUser));
	}

	/**
	 * Returns TRUE only if currently logged in frontend user is in list.
	 *
	 * @param ObjectStorage $frontendUsers
	 * @return boolean
	 * @api
	 */
	public function assertFrontendUsersLoggedIn(ObjectStorage $frontendUsers = NULL) {
		foreach ($frontendUsers as $frontendUser) {
			if (TRUE === $this->assertFrontendUserLoggedIn($frontendUser)) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Returns TRUE if a FrontendUserGroup (specific given argument, else not) is logged in
	 *
	 * @param mixed $groups One \TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup or ObjectStorage containing same
	 * @return boolean
	 * @api
	 */
	public function assertFrontendUserGroupLoggedIn($groups = NULL) {
		$currentFrontendUser = $this->getCurrentFrontendUser();
		if (FALSE === $currentFrontendUser instanceof FrontendUser) {
			return FALSE;
		}
		$currentFrontendUserGroups = $currentFrontendUser->getUsergroup();
		if (NULL === $groups) {
			return (boolean) (0 < $currentFrontendUserGroups->count());
		} elseif (TRUE === $groups instanceof FrontendUserGroup) {
			return $currentFrontendUserGroups->contains($groups);
		} elseif (TRUE === $groups instanceof ObjectStorage) {
			$currentFrontendUserGroupsClone = clone $currentFrontendUserGroups;
			$currentFrontendUserGroupsClone->removeAll($groups);
			return ($currentFrontendUserGroups->count() !== $currentFrontendUserGroupsClone->count());
		} else {
			return FALSE;
		}
	}

	/**
	 * Returns TRUE only if a backend user is currently logged in. If used,
	 * argument specifies that the logged in user must be that specific user
	 *
	 * @param integer $backendUser
	 * @return boolean
	 * @api
	 */
	public function assertBackendUserLoggedIn($backendUser = NULL) {
		$currentBackendUser = $this->getCurrentBackendUser();
		if (NULL !== $backendUser) {
			if ((integer) $currentBackendUser['uid'] === (integer) $backendUser) {
				return TRUE;
			} else {
				return FALSE;
			}
		}
		return (boolean) (TRUE === is_array($currentBackendUser));
	}

	/**
	 * Returns TRUE only if a backend user is logged in and either has any group
	 * (if param left out) or is a member of the group $groups or a group in
	 * the array/CSV $groups
	 *
	 * @param mixed $groups Array of group uids or CSV of group uids or one group uid
	 * @return boolean
	 * @api
	 */
	public function assertBackendUserGroupLoggedIn($groups = NULL) {
		if (FALSE === $this->assertBackendUserLoggedIn()) {
			return FALSE;
		}
		$currentBackendUser = $this->getCurrentBackendUser();
		$currentUserGroups = trim($currentBackendUser['usergroup'], ',');
		$userGroups = FALSE === empty($currentUserGroups) ? explode(',', $currentUserGroups) : array();
		if (0 === count($userGroups)) {
			return FALSE;
		}
		if (TRUE === is_string($groups)) {
			$groups = trim($groups, ',');
			$groups = FALSE === empty($groups) ? explode(',', $groups) : array();
		}
		if (0 < count($groups)) {
			return (boolean) (0 < count(array_intersect($userGroups, $groups)));
		}
		return FALSE;
	}

	/**
	 * Returns TRUE only if there is a current user logged in and this user
	 * is an admin class backend user
	 *
	 * @return boolean
	 * @api
	 */
	public function assertAdminLoggedIn() {
		if (FALSE === $this->assertBackendUserLoggedIn()) {
			return FALSE;
		}
		$currentBackendUser = $this->getCurrentBackendUser();
		return (boolean) $currentBackendUser['admin'];
	}

	/**
	 * Gets the currently logged in Frontend User
	 *
	 * @return FrontendUser|NULL
	 * @api
	 */
	public function getCurrentFrontendUser() {
		if (TRUE === empty($GLOBALS['TSFE']->loginUser)) {
			return NULL;
		}
		return $this->frontendUserRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
	}

	/**
	 * Returns a be_user record as lowerCamelCase indexed array if a BE user is
	 * currently logged in.
	 *
	 * @return array
	 * @api
	 */
	public function getCurrentBackendUser() {
		return $GLOBALS['BE_USER']->user;
	}

	/**
	 * Override: forcibly disables page caching - a TRUE condition
	 * in this ViewHelper means page content would be depending on
	 * the current visitor's session/cookie/auth etc.
	 *
	 * Returns value of "then" attribute.
	 * If then attribute is not set, iterates through child nodes and renders ThenViewHelper.
	 * If then attribute is not set and no ThenViewHelper and no ElseViewHelper is found, all child nodes are rendered
	 *
	 * @return string rendered ThenViewHelper or contents of <f:if> if no ThenViewHelper was found
	 * @api
	 */
	protected function renderThenChild() {
		if (TRUE === $this->isFrontendContext()) {
			$GLOBALS['TSFE']->no_cache = 1;
		}
		return parent::renderThenChild();
	}

	/**
	 * @return boolean
	 */
	protected function isFrontendContext() {
		return 'FE' === TYPO3_MODE;
	}

}
