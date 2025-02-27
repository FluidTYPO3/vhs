<?php
namespace FluidTYPO3\Vhs\Proxy;

/*
 * This file is part of the FluidTYPO3/Flux project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Final/readonly class is unnecessary coercion - and using it in shared libraries is arrogant and very disrespectful.
 *
 * @codeCoverageIgnore
 */
class ResourceFactoryProxy implements SingletonInterface
{
    private ResourceFactory $resourceFactory;

    public function __construct(ResourceFactory $resourceFactory)
    {
        $this->resourceFactory = $resourceFactory;
    }

    public function getFileReferenceObject(int $uid): FileReference
    {
        return $this->resourceFactory->getFileReferenceObject($uid);
    }

    /**
     * @param int $uid
     */
    public function getFileObject($uid, array $fileData = []): File
    {
        return $this->resourceFactory->getFileObject($uid, $fileData);
    }

    /**
     * @param string $identifier
     * @return File|ProcessedFile|null
     */
    public function getFileObjectFromCombinedIdentifier($identifier)
    {
        return $this->resourceFactory->getFileObjectFromCombinedIdentifier($identifier);
    }
}
