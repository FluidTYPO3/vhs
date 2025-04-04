<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media\Image;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Proxy\ResourceFactoryProxy;
use FluidTYPO3\Vhs\Utility\ResourceUtility;
use TYPO3\CMS\Core\Resource\FileReference as CoreFileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference as ExtbaseFileReference;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

/**
 * Base class: Media\Image view helpers.
 */
abstract class AbstractImageInfoViewHelper extends AbstractViewHelper
{
    /**
     * @var ResourceFactoryProxy
     */
    protected $resourceFactory;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * Construct resource factory
     */
    public function __construct()
    {
        /** @var ResourceFactoryProxy $resourceFactory */
        $resourceFactory = GeneralUtility::makeInstance(ResourceFactoryProxy::class);
        $this->resourceFactory = $resourceFactory;
    }

    public function initializeArguments(): void
    {
        $this->registerArgument(
            'src',
            'mixed',
            'Path to or id of the image file to determine info for. In case a FileReference is supplied, ' .
            'treatIdAsUid and treatIdAsReference will automatically be activated.',
            true
        );
        $this->registerArgument(
            'treatIdAsUid',
            'boolean',
            'If TRUE, the path argument is treated as a resource uid.'
        );
        $this->registerArgument(
            'treatIdAsReference',
            'boolean',
            'If TRUE, the path argument is treated as a reference uid and will be resolved to a resource via ' .
            'sys_file_reference.',
            false,
            false
        );
    }

    public function getInfo(): array
    {
        /** @var string|int|CoreFileReference|ExtbaseFileReference|null $src */
        $src = $this->arguments['src'];
        $treatIdAsUid = (boolean) $this->arguments['treatIdAsUid'];
        $treatIdAsReference = (boolean) $this->arguments['treatIdAsReference'];

        if (null === $src) {
            /** @var string|int|CoreFileReference|ExtbaseFileReference|null $src */
            $src = $this->renderChildren();
            if (null === $src) {
                return [];
            }
        }

        if ($src instanceof CoreFileReference || $src instanceof ExtbaseFileReference) {
            $src = $src->getUid();
            $treatIdAsUid = false;
            $treatIdAsReference = true;
        }

        if ($treatIdAsUid || $treatIdAsReference) {
            $id = (integer) $src;
            $info = [];
            if ($treatIdAsUid) {
                $info = $this->getInfoByUid($id);
            } elseif ($treatIdAsReference) {
                $info = $this->getInfoByReference($id);
            }
        } else {
            $file = GeneralUtility::getFileAbsFileName((string) $src);
            if (!file_exists($file) || is_dir($file)) {
                throw new Exception(
                    'Cannot determine info for "' . $file . '". File does not exist or is a directory.',
                    1357066532
                );
            }
            $imageSize = getimagesize($file);
            $info = [
                'width'  => $imageSize[0] ?? '',
                'height' => $imageSize[1] ?? '',
                'type'   => $imageSize['mime'] ?? '',
            ];
        }

        return $info;
    }

    public function getInfoByReference(int $id): array
    {
        $fileReference = $this->resourceFactory->getFileReferenceObject($id);
        $file = $fileReference->getOriginalFile();
        return ResourceUtility::getFileArray($file);
    }

    public function getInfoByUid(int $uid): array
    {
        $file = $this->resourceFactory->getFileObject($uid);
        return ResourceUtility::getFileArray($file);
    }
}
