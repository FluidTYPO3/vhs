<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource\Record;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\ViewHelpers\Resource\ResourceViewHelperInterface;

interface RecordResourceViewHelperInterface extends ResourceViewHelperInterface
{
    public function getResources(array $record): array;
    public function getTable(): string;
    public function getField(): string;
    public function getRecord(int $id): ?array;
    public function getActiveRecord(): array;
}
