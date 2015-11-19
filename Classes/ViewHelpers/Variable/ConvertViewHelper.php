<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### Convert ViewHelper
 *
 * Converts $value to $type which can be one of 'string', 'integer',
 * 'float', 'boolean', 'array' or 'ObjectStorage'. If $value is NULL
 * sensible defaults are assigned or $default which obviously has to
 * be of $type as well.
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Var
 */
class ConvertViewHelper extends AbstractViewHelper {

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		$this->registerArgument('value', 'mixed', 'Value to convert into a different type', FALSE, NULL);
		$this->registerArgument('type', 'string', 'Data type to convert the value into. Can be one of "string", "integer", "float", "boolean", "array" or "ObjectStorage".', TRUE);
		$this->registerArgument('default', 'mixed', 'Optional default value to assign to the converted variable in case it is NULL.', FALSE, NULL);
	}

	/**
	 * Render method
	 *
	 * @throws \RuntimeException
	 * @return mixed
	 */
	public function render() {
		if (TRUE === isset($this->arguments['value'])) {
			$value = $this->arguments['value'];
		} else {
			$value = $this->renderChildren();
		}
		$type = $this->arguments['type'];
		if (gettype($value) === $type) {
			return $value;
		}
		if (NULL !== $value) {
			if ('ObjectStorage' === $type && 'array' === gettype($value)) {
				/** @var ObjectManager $objectManager */
				$objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
				/** @var ObjectStorage $storage */
				$storage = $objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage');
				foreach ($value as $item) {
					$storage->attach($item);
				}
				$value = $storage;
			} elseif ('array' === $type && TRUE === $value instanceof \Traversable) {
				$value = iterator_to_array($value, FALSE);
			} elseif ('array' === $type) {
				$value = array($value);
			} else {
				settype($value, $type);
			}
		} else {
			if (TRUE === isset($this->arguments['default'])) {
				$default = $this->arguments['default'];
				if (gettype($default) !== $type) {
					throw new \RuntimeException('Supplied argument "default" is not of the type "' . $type .'"', 1364542576);
				}
				$value = $default;
			} else {
				switch ($type) {
					case 'string':
						$value = '';
						break;
					case 'integer':
						$value = 0;
						break;
					case 'boolean':
						$value = FALSE;
						break;
					case 'float':
						$value = 0.0;
						break;
					case 'array':
						$value = array();
						break;
					case 'ObjectStorage':
						$objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
						$value = $objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage');
						break;
					default:
						throw new \RuntimeException('Provided argument "type" is not valid', 1364542884);
				}
			}
		}
		return $value;
	}

}
