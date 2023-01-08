<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Doctrine\DBAL\Result;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * ### Condition: Is current language
 *
 * A condition ViewHelper which renders the `then` child if
 * current language matches the provided language uid or language
 * title. When using language titles like 'de' it is required to
 * provide a default title to distinguish between the standard
 * and a non existing language.
 */
class IsLanguageViewHelper extends AbstractConditionViewHelper
{
    /**
     * Initialize arguments
     *
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('language', 'string', 'language to check', true);
        $this->registerArgument('defaultTitle', 'string', 'title of the default language', false, 'en');
    }

    /**
     * @param array $arguments
     * @return bool
     */
    protected static function evaluateCondition($arguments = null)
    {
        if (!is_array($arguments)) {
            return false;
        }
        $language = $arguments['language'];
        $defaultTitle = $arguments['defaultTitle'];

        if (class_exists(LanguageAspect::class)) {
            /** @var Context $context */
            $context = GeneralUtility::makeInstance(Context::class);
            /** @var LanguageAspect $languageAspect */
            $languageAspect = $context->getAspect('language');
            $currentLanguageUid = $languageAspect->getId();
        } else {
            $currentLanguageUid = $GLOBALS['TSFE']->sys_language_uid;
        }

        if (true === is_numeric($language)) {
            $languageUid = intval($language);
        } else {
            /** @var ConnectionPool $connectionPool */
            $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
            $queryBuilder = $connectionPool->getQueryBuilderForTable('sys_language');

            $queryBuilder->createNamedParameter($language, \PDO::PARAM_STR, ':title');

            /** @var Result $result */
            $result = $queryBuilder
                ->select('uid')
                ->from('sys_language')
                ->where(
                    $queryBuilder->expr()->eq('title', ':title')
                )
                ->execute();
            $row = $result->fetchAssociative();

            if (is_array($row)) {
                $languageUid = intval($row['uid']);
            } else {
                if ((string) $language === $defaultTitle) {
                    $languageUid = $currentLanguageUid;
                } else {
                    $languageUid = -1;
                }
            }
        }
        return $languageUid === $currentLanguageUid;
    }
}
