<?php
namespace FluidTYPO3\Vhs\Proxy;

use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\SingletonInterface;

class FileRepositoryProxy implements SingletonInterface
{
    private FileRepository $fileRepository;

    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    public function findByRelation(string $tableName, string $fieldName, int $uid, ?int $workspaceId = null): array
    {
        return $this->fileRepository->findByRelation($tableName, $fieldName, $uid, $workspaceId);
    }
}
