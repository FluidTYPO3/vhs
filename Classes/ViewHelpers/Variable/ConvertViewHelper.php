<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### Convert ViewHelper
 *
 * Converts $value to $type which can be one of 'string', 'integer',
 * 'float', 'boolean', 'array' or 'ObjectStorage'. If $value is NULL
 * sensible defaults are assigned or $default which obviously has to
 * be of $type as well.
 */
class ConvertViewHelper extends AbstractViewHelper implements CompilableInterface
{

    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('value', 'mixed', 'Value to convert into a different type');
        $this->registerArgument(
            'type',
            'string',
            'Data type to convert the value into. Can be one of "string", "integer", "float", "boolean", "array" ' .
            'or "ObjectStorage".',
            true
        );
        $this->registerArgument(
            'default',
            'mixed',
            'Optional default value to assign to the converted variable in case it is NULL.',
            false,
            null
        );
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $value = $renderChildrenClosure();
        $type = $arguments['type'];
        if (gettype($value) === $type) {
            return $value;
        }
        if (null !== $value) {
            if ('ObjectStorage' === $type && 'array' === gettype($value)) {
                /** @var ObjectManager $objectManager */
                $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
                /** @var ObjectStorage $storage */
                $storage = $objectManager->get(ObjectStorage::class);
                foreach ($value as $item) {
                    $storage->attach($item);
                }
                $value = $storage;
            } elseif ('array' === $type && true === $value instanceof \Traversable) {
                $value = iterator_to_array($value, false);
            } elseif ('array' === $type) {
                $value = [$value];
            } else {
                settype($value, $type);
            }
        } else {
            if (true === isset($arguments['default'])) {
                $default = $arguments['default'];
                if (gettype($default) !== $type) {
                    throw new \RuntimeException(
                        'Supplied argument "default" is not of the type "' . $type .'"',
                        1364542576
                    );
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
                        $value = false;
                        break;
                    case 'float':
                        $value = 0.0;
                        break;
                    case 'array':
                        $value = [];
                        break;
                    case 'ObjectStorage':
                        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
                        $value = $objectManager->get(ObjectStorage::class);
                        break;
                    default:
                        throw new \RuntimeException('Provided argument "type" is not valid', 1364542884);
                }
            }
        }
        return $value;
    }
}
