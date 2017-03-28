<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Security;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * Base class: Security ViewHelpers
 */
abstract class AbstractSecurityViewHelper extends AbstractConditionViewHelper
{

    /**
     * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
     */
    protected $frontendUserRepository;

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository $frontendUserRepository
     * @return void
     */
    public function injectFrontendUserRepository(FrontendUserRepository $frontendUserRepository)
    {
        $this->frontendUserRepository = $frontendUserRepository;
        $query = $this->frontendUserRepository->createQuery();
        $querySettings = $query->getQuerySettings();
        $querySettings->setRespectStoragePage(false);
        $querySettings->setRespectSysLanguage(false);
        $this->frontendUserRepository->setDefaultQuerySettings($querySettings);
    }

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument(
            'anyFrontendUser',
            'boolean',
            'If TRUE, allows any FrontendUser unless other arguments disallows each specific FrontendUser',
            false,
            false
        );
        $this->registerArgument(
            'anyFrontendUserGroup',
            'boolean',
            'If TRUE, allows any FrontendUserGroup unless other arguments disallows each specific FrontendUser',
            false,
            false
        );
        $this->registerArgument(
            'frontendUser',
            FrontendUser::class,
            'The FrontendUser to allow/deny'
        );
        $this->registerArgument(
            'frontendUsers',
            '<TYPO3\CMS\Extbase\Persistence\ObjectStorage>\TYPO3\CMS\Extbase\Domain\Model\FrontendUser',
            'The FrontendUsers ObjectStorage to allow/deny'
        );
        $this->registerArgument(
            'frontendUserGroup',
            FrontendUserGroup::class,
            'The FrontendUserGroup to allow/deny'
        );
        $this->registerArgument(
            'frontendUserGroups',
            '<TYPO3\CMS\Extbase\Persistence\ObjectStorage>\TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup',
            'The FrontendUserGroups ObjectStorage to allow/deny'
        );
        $this->registerArgument(
            'anyBackendUser',
            'boolean',
            'If TRUE, allows any backend user unless other arguments disallows each specific backend user',
            false,
            false
        );
        $this->registerArgument(
            'backendUser',
            'integer',
            'The uid of a backend user to allow/deny'
        );
        $this->registerArgument(
            'backendUsers',
            'mixed',
            'The backend users list to allow/deny. If string, CSV of uids assumed, if array, array of uids assumed'
        );
        $this->registerArgument(
            'backendUserGroup',
            'integer',
            'The uid of the backend user group to allow/deny'
        );
        $this->registerArgument(
            'backendUserGroups',
            'mixed',
            'The backend user groups list to allow/deny. If string, CSV of uids is assumed, if array, ' .
            'array of uids is assumed'
        );
        $this->registerArgument(
            'admin',
            'boolean',
            'If TRUE, a backend user which is also an admin is required'
        );
        $this->registerArgument(
            'evaluationType',
            'string',
            'Specify AND or OR (case sensitive) to determine how arguments must be processed. Default is AND, ' .
            'requiring all arguments to be satisfied if used',
            false,
            'AND'
        );
    }

    /**
     * @param array|null $arguments
     * @return bool
     */
    protected static function evaluateCondition($arguments = null)
    {
        $proxy = GeneralUtility::makeInstance(ObjectManager::class)->get(static::class);
        $proxy->setArguments((array) $arguments);
        return $proxy->evaluateArguments();
    }


    /**
     * Returns TRUE if all conditions from arguments are satisfied. The
     * type of evaluation (AND or OR) can be set using argument "evaluationType"
     *
     * @return boolean
     */
    public function evaluateArguments()
    {
        $evaluationType = $this->arguments['evaluationType'];
        $evaluations = [];
        if (true === (boolean) $this->arguments['anyFrontendUser']) {
            $evaluations['anyFrontendUser'] = intval($this->assertFrontendUserLoggedIn());
        }
        if (true === (boolean) $this->arguments['anyFrontendUserGroup']) {
            $evaluations['anyFrontendUserGroup'] = intval($this->assertFrontendUserGroupLoggedIn());
        }
        if (true === isset($this->arguments['frontendUser'])) {
            $evaluations['frontendUser'] =
                intval($this->assertFrontendUserLoggedIn($this->arguments['frontendUser']));
        }
        if (true === isset($this->arguments['frontendUsers'])) {
            $evaluations['frontendUsers'] =
                intval($this->assertFrontendUsersLoggedIn($this->arguments['frontendUsers']));
        }
        if (true === isset($this->arguments['frontendUserGroup'])) {
            $evaluations['frontendUserGroup'] =
                intval($this->assertFrontendUserGroupLoggedIn($this->arguments['frontendUserGroup']));
        }
        if (true === isset($this->arguments['frontendUserGroups'])) {
            $evaluations['frontendUserGroups'] =
                intval($this->assertFrontendUserGroupLoggedIn($this->arguments['frontendUserGroups']));
        }
        if (true === (boolean) $this->arguments['anyBackendUser']) {
            $evaluations['anyBackendUser'] = intval($this->assertBackendUserLoggedIn());
        }
        if (true === (boolean) $this->arguments['anyBackendUserGroup']) {
            $evaluations['anyBackendUserGroup'] = intval($this->assertBackendUserGroupLoggedIn());
        }
        if (true === isset($this->arguments['backendUser'])) {
            $evaluations['backendUser'] = intval($this->assertBackendUserLoggedIn($this->arguments['backendUser']));
        }
        if (true === isset($this->arguments['backendUsers'])) {
            $evaluations['backendUsers'] = intval($this->assertBackendUserLoggedIn($this->arguments['backendUsers']));
        }
        if (true === isset($this->arguments['backendUserGroup'])) {
            $evaluations['backendUserGroup'] =
                intval($this->assertBackendUserGroupLoggedIn($this->arguments['backendUserGroup']));
        }
        if (true === isset($this->arguments['backendUserGroups'])) {
            $evaluations['backendUserGroups'] =
                intval($this->assertBackendUserGroupLoggedIn($this->arguments['backendUserGroups']));
        }
        if (true === (boolean) $this->arguments['admin']) {
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
    public function assertFrontendUserLoggedIn(FrontendUser $frontendUser = null)
    {
        $currentFrontendUser = $this->getCurrentFrontendUser();
        if (false === $currentFrontendUser instanceof FrontendUser) {
            return false;
        }
        if (true === $frontendUser instanceof FrontendUser && true === $currentFrontendUser instanceof FrontendUser) {
            if ($currentFrontendUser->getUid() === $frontendUser->getUid()) {
                return true;
            } else {
                return false;
            }
        }
        return (boolean) (true === is_object($currentFrontendUser));
    }

    /**
     * Returns TRUE only if currently logged in frontend user is in list.
     *
     * @param ObjectStorage $frontendUsers
     * @return boolean
     * @api
     */
    public function assertFrontendUsersLoggedIn(ObjectStorage $frontendUsers = null)
    {
        foreach ($frontendUsers as $frontendUser) {
            if (true === $this->assertFrontendUserLoggedIn($frontendUser)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns TRUE if a FrontendUserGroup (specific given argument, else not) is logged in
     *
     * @param mixed $groups One \TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup or ObjectStorage containing same
     * @return boolean
     * @api
     */
    public function assertFrontendUserGroupLoggedIn($groups = null)
    {
        $currentFrontendUser = $this->getCurrentFrontendUser();
        if (false === $currentFrontendUser instanceof FrontendUser) {
            return false;
        }
        $currentFrontendUserGroups = $currentFrontendUser->getUsergroup();
        if (null === $groups) {
            return (boolean) (0 < $currentFrontendUserGroups->count());
        } elseif (true === $groups instanceof FrontendUserGroup) {
            return $currentFrontendUserGroups->contains($groups);
        } elseif (true === $groups instanceof ObjectStorage) {
            $currentFrontendUserGroupsClone = clone $currentFrontendUserGroups;
            $currentFrontendUserGroupsClone->removeAll($groups);
            return ($currentFrontendUserGroups->count() !== $currentFrontendUserGroupsClone->count());
        } else {
            return false;
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
    public function assertBackendUserLoggedIn($backendUser = null)
    {
        $currentBackendUser = $this->getCurrentBackendUser();
        if (null !== $backendUser) {
            if ((integer) $currentBackendUser['uid'] === (integer) $backendUser) {
                return true;
            } else {
                return false;
            }
        }
        return (boolean) (true === is_array($currentBackendUser));
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
    public function assertBackendUserGroupLoggedIn($groups = null)
    {
        if (false === $this->assertBackendUserLoggedIn()) {
            return false;
        }
        $currentBackendUser = $this->getCurrentBackendUser();
        $currentUserGroups = trim($currentBackendUser['usergroup'], ',');
        $userGroups = false === empty($currentUserGroups) ? explode(',', $currentUserGroups) : [];
        if (0 === count($userGroups)) {
            return false;
        }
        if (true === is_string($groups)) {
            $groups = trim($groups, ',');
            $groups = false === empty($groups) ? explode(',', $groups) : [];
        }
        if (0 < count($groups)) {
            return (boolean) (0 < count(array_intersect($userGroups, $groups)));
        }
        return false;
    }

    /**
     * Returns TRUE only if there is a current user logged in and this user
     * is an admin class backend user
     *
     * @return boolean
     * @api
     */
    public function assertAdminLoggedIn()
    {
        if (false === $this->assertBackendUserLoggedIn()) {
            return false;
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
    public function getCurrentFrontendUser()
    {
        if (true === empty($GLOBALS['TSFE']->loginUser)) {
            return null;
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
    public function getCurrentBackendUser()
    {
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
    protected function renderThenChild()
    {
        if (true === $this->isFrontendContext()) {
            $GLOBALS['TSFE']->no_cache = 1;
        }
        return parent::renderThenChild();
    }

    /**
     * @return boolean
     */
    protected function isFrontendContext()
    {
        return 'FE' === TYPO3_MODE;
    }
}
