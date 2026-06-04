<?php
declare(strict_types=1);

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
 * Proxy wrapper for TYPO3's final ResourceFactory.
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

    public function getFileReferenceObject(
        int|string $uid,
        array $fileReferenceData = [],
        bool $raw = false
    ): FileReference {
        return $this->resourceFactory->getFileReferenceObject((int) $uid, $fileReferenceData, $raw);
    }

    /**
     * @param int $uid
     */
    public function getFileObject(int|string $uid, array $fileData = []): File
    {
        return $this->resourceFactory->getFileObject((int) $uid, $fileData);
    }

    /**
     * @param string $identifier
     * @return File|ProcessedFile|null
     */
    public function getFileObjectFromCombinedIdentifier(string $identifier): File|ProcessedFile|null
    {
        return $this->resourceFactory->getFileObjectFromCombinedIdentifier($identifier);
    }
}
