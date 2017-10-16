<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media\Image;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\ResourceUtility;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

/**
 * Base class: Media\Image view helpers.
 */
abstract class AbstractImageInfoViewHelper extends AbstractViewHelper
{

    /**
     * @var \TYPO3\CMS\Core\Resource\ResourceFactory
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
        $this->resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
    }

    /**
     * Initialize arguments.
     *
     * @return void
     * @api
     */
    public function initializeArguments()
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

    /**
     * @throws Exception
     * @return array
     */
    public function getInfo()
    {
        $src = $this->arguments['src'];
        $treatIdAsUid = (boolean) $this->arguments['treatIdAsUid'];
        $treatIdAsReference = (boolean) $this->arguments['treatIdAsReference'];

        if (null === $src) {
            $src = $this->renderChildren();
            if (null === $src) {
                return [];
            }
        }

        if (is_object($src) && $src instanceof FileReference) {
            $src = $src->getUid();
            $treatIdAsUid = false;
            $treatIdAsReference = true;
        }

        if (true === $treatIdAsUid || true === $treatIdAsReference) {
            $id = (integer) $src;
            $info = [];
            if (true === $treatIdAsUid) {
                $info = $this->getInfoByUid($id);
            } elseif (true === $treatIdAsReference) {
                $info = $this->getInfoByReference($id);
            }
        } else {
            $file = GeneralUtility::getFileAbsFileName($src);
            if (false === file_exists($file) || true === is_dir($file)) {
                throw new Exception(
                    'Cannot determine info for "' . $file . '". File does not exist or is a directory.',
                    1357066532
                );
            }
            $imageSize = getimagesize($file);
            $info = [
                'width'  => $imageSize[0],
                'height' => $imageSize[1],
                'type'   => $imageSize['mime'],
            ];
        }

        return $info;
    }

    /**
     * @param integer $id
     * @return array
     */
    public function getInfoByReference($id)
    {
        $fileReference = $this->resourceFactory->getFileReferenceObject($id);
        $file = $fileReference->getOriginalFile();
        return ResourceUtility::getFileArray($file);
    }

    /**
     * @param integer $uid
     * @return array
     */
    public function getInfoByUid($uid)
    {
        $file = $this->resourceFactory->getFileObject($uid);
        return ResourceUtility::getFileArray($file);
    }
}
