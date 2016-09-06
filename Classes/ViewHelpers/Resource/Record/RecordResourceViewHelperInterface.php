<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource\Record;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\ViewHelpers\Resource\ResourceViewHelperInterface;

/**
 * Interface for Record Resource ViewHelpers
 */
interface RecordResourceViewHelperInterface extends ResourceViewHelperInterface
{

    /**
     * @param array $record
     * @return array
     */
    public function getResources($record);

    /**
     * @return string
     */
    public function getTable();

    /**
     * @return string
     */
    public function getField();

    /**
     * @param mixed $id
     * @return array
     */
    public function getRecord($id);

    /**
     * @return array
     */
    public function getActiveRecord();
}
