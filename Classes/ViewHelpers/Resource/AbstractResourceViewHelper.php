<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ResourceViewHelperTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Base class for resource related view helpers
 *
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Resource
 */
abstract class AbstractResourceViewHelper extends AbstractTagBasedViewHelper {

	use ResourceViewHelperTrait;

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		$this->registerArgument('identifier', 'mixed', 'The FAL combined identifiers (either CSV, array or implementing Traversable).', FALSE, NULL);
		$this->registerArgument('categories', 'mixed', 'The sys_category records to select the resources from (either CSV, array or implementing Traversable).', FALSE, NULL);
		$this->registerArgument('treatIdAsUid', 'boolean', 'If TRUE, the identifier argument is treated as resource uids.', FALSE, FALSE);
		$this->registerArgument('treatIdAsReference', 'boolean', 'If TRUE, the identifier argument is treated as reference uids and will be resolved to resources via sys_file_reference.', FALSE, FALSE);
	}

}
