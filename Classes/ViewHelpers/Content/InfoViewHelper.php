<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Content;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use FluidTYPO3\Vhs\Utility\DoctrineQueryProxy;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

/**
 * ViewHelper to access data of the current content element record.
 */
class InfoViewHelper extends AbstractViewHelper
{
    use TemplateVariableViewHelperTrait;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager): void
    {
        $this->configurationManager = $configurationManager;
    }

    public function initializeArguments(): void
    {
        $this->registerAsArgument();
        $this->registerArgument(
            'contentUid',
            'integer',
            'If specified, this UID will be used to fetch content element data instead of using the current ' .
            'content element.',
            false,
            0
        );
        $this->registerArgument(
            'field',
            'string',
            'If specified, only this field will be returned/assigned instead of the complete content element record.'
        );
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function render()
    {
        /** @var int $contentUid */
        $contentUid = $this->arguments['contentUid'];
        $record = false;

        if (0 === $contentUid) {
            /** @var ContentObjectRenderer $cObj */
            $cObj = $this->configurationManager->getContentObject();
            if ($cObj->getCurrentTable() !== 'tt_content') {
                throw new Exception(
                    'v:content.info must have contentUid argument outside tt_content context',
                    1690035521
                );
            }
            if (!empty($cObj->data)) {
                $record = $cObj->data;
            } else {
                $tsfe = $GLOBALS['TSFE'] ?? null;
                if (!$tsfe instanceof TypoScriptFrontendController) {
                    throw new Exception(
                        'v:content.info must have contentUid argument when no TypoScriptFrontendController exists',
                        1690035521
                    );
                }
                $recordReference = $tsfe->currentRecord;
                $contentUid = (int) substr($recordReference, strpos($recordReference, ':') + 1);
            }
        }

        /** @var string $field */
        $field = $this->arguments['field'];
        $selectFields = $field;

        if (!$record && 0 !== $contentUid) {
            if (!isset($GLOBALS['TCA']['tt_content']['columns'][$field])) {
                $selectFields = '*';
            }

            /** @var ConnectionPool $connectionPool */
            $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
            $queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
            $queryBuilder->createNamedParameter($contentUid, \PDO::PARAM_INT, ':uid');

            $queryBuilder
                ->select($selectFields)
                ->from('tt_content')
                ->where(
                    $queryBuilder->expr()->eq('uid', ':uid')
                );
            $result = DoctrineQueryProxy::executeQueryOnQueryBuilder($queryBuilder);
            $record = DoctrineQueryProxy::fetchAssociative($result);

            // Add the page overlay
            if (class_exists(LanguageAspect::class)) {
                /** @var Context $context */
                $context = GeneralUtility::makeInstance(Context::class);
                /** @var LanguageAspect $languageAspect */
                $languageAspect = $context->getAspect('language');
                $languageUid = $languageAspect->getId();
            } else {
                $languageUid = $GLOBALS['TSFE']->sys_language_uid;
            }

            if (0 !== $languageUid && $GLOBALS['TSFE']->sys_language_contentOL) {
                $record = $GLOBALS['TSFE']->sys_page->getRecordOverlay(
                    'tt_content',
                    $record,
                    $GLOBALS['TSFE']->sys_language_content,
                    $GLOBALS['TSFE']->sys_language_contentOL
                );
            }
        }

        if ($record === false) {
            throw new \Exception(
                sprintf('Either record with uid %d or field %s do not exist.', $contentUid, $selectFields),
                1358679983
            );
        }

        // Check if single field or whole record should be returned
        $content = null;
        if (null === $field) {
            $content = $record;
        } elseif (isset($record[$field])) {
            $content = $record[$field];
        }

        return $this->renderChildrenWithVariableOrReturnInput($content);
    }
}
