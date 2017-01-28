<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource\Record;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use FluidTYPO3\Vhs\Utility\ErrorUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Base class: Record Resource ViewHelpers
 */
abstract class AbstractRecordResourceViewHelper extends AbstractViewHelper implements RecordResourceViewHelperInterface
{

    use TemplateVariableViewHelperTrait;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $field;

    /**
     * @var string
     */
    protected $idField = 'uid';

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
     * @return void
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * Initialize arguments.
     *
     * @return void
     * @api
     */
    public function initializeArguments()
    {
        $this->registerArgument('table', 'string', 'The table to lookup records.', true);
        $this->registerArgument('field', 'string', 'The field of the table associated to resources.', true);
        $this->registerArgument(
            'record',
            'array',
            'The actual record. Alternatively you can use the "uid" argument.',
            false,
            null
        );
        $this->registerArgument(
            'uid',
            'integer',
            'The uid of the record. Alternatively you can use the "record" argument.',
            false,
            null
        );
        $this->registerArgument(
            'as',
            'string',
            'If specified, a template variable with this name containing the requested data will be inserted ' .
            'instead of returning it.',
            false,
            null
        );
    }

    /**
     * @param mixed $identity
     * @return mixed
     */
    public function getResource($identity)
    {
        return $identity;
    }

    /**
     * @param array $record
     * @return array
     */
    public function getResources($record)
    {
        $field = $this->getField();

        if (false === isset($record[$field])) {
            ErrorUtility::throwViewHelperException('The "field" argument was not found on the selected record.', 1384612728);
        }

        if (true === empty($record[$field])) {
            return [];
        }

        return GeneralUtility::trimExplode(',', $record[$field]);
    }

    /**
     * @return string
     */
    public function getTable()
    {
        $table = $this->arguments['table'];
        if (null === $table) {
            $table = $this->table;
        }

        if (true === empty($table) || false === is_string($table)) {
            ErrorUtility::throwViewHelperException('The "table" argument must be specified and must be a string.', 1384611336);
        }

        return $table;
    }

    /**
     * @return string
     */
    public function getField()
    {
        $field = $this->arguments['field'];
        if (null === $field) {
            $field = $this->field;
        }

        if (true === empty($field) || false === is_string($field)) {
            ErrorUtility::throwViewHelperException('The "field" argument must be specified and must be a string.', 1384611355);
        }

        return $field;
    }

    /**
     * @param mixed $id
     * @return array
     */
    public function getRecord($id)
    {
        $table = $this->getTable();
        $idField = $this->idField;

        $sqlIdField = $GLOBALS['TYPO3_DB']->quoteStr($idField, $table);
        $sqlId = $GLOBALS['TYPO3_DB']->fullQuoteStr($id, $table);

        return reset($GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', $table, $sqlIdField . ' = ' . $sqlId));
    }

    /**
     * @return array
     */
    public function getActiveRecord()
    {
        return $this->configurationManager->getContentObject()->data;
    }

    /**
     * @return mixed
     */
    public function render()
    {
        $record = $this->arguments['record'];
        $uid = $this->arguments['uid'];

        if (null === $record) {
            if (null === $uid) {
                $record = $this->getActiveRecord();
            } else {
                $record = $this->getRecord($uid);
            }
        }

        if (null === $record) {
            ErrorUtility::throwViewHelperException('No record was found. The "record" or "uid" argument must be specified.', 1384611413);
        }

        // attempt to load resources. If any Exceptions happen, transform them to
        // ViewHelperExceptions which render as an inline text error message.
        try {
            $resources = $this->getResources($record);
        } catch (\Exception $error) {
            // we are doing the pokemon-thing and catching the very top level
            // of Exception because the range of Exceptions that are possibly
            // thrown by the getResources() method in subclasses are not
            // extended from a shared base class like RuntimeException. Thus,
            // we are forced to "catch them all" - but we also output them.
            ErrorUtility::throwViewHelperException($error->getMessage(), $error->getCode());
        }
        return $this->renderChildrenWithVariableOrReturnInput($resources);
    }
}
