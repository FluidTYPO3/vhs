<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\ViewHelpers\Resource\RecordViewHelper;

/**
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Page
 */
class ResourcesViewHelper extends RecordViewHelper {

	const defaultTable = 'pages';
	const defaultField = 'media';

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
	public function initializeArguments() {
		parent::initializeArguments();

		$this->overrideArgument('table', 'string', 'The table to lookup records.', FALSE, self::defaultTable);
		$this->overrideArgument('field', 'string', 'The field of the table associated to resources.', FALSE, self::defaultField);
	}

}
