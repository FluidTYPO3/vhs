<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Content\Resources;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 */
class FalViewHelper extends \FluidTYPO3\Vhs\ViewHelpers\Resource\Record\FalViewHelper
{

    const defaultTable = 'tt_content';
    const defaultField = 'image';

    /**
     * @var string
     */
    protected $table = self::defaultTable;

    /**
     * @var string
     */
    protected $field = self::defaultField;

    /**
     * Initialize arguments.
     *
     * @return void
     * @api
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->overrideArgument('table', 'string', 'The table to lookup records.', false, self::defaultTable);
        $this->overrideArgument('field', 'string', 'The field of the table associated to resources.', false, self::defaultField);
    }
}
