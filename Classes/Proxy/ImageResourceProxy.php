<?php

namespace FluidTYPO3\Vhs\Proxy;

use TYPO3\CMS\Core\Imaging\ImageResource;
use TYPO3\CMS\Core\Resource\ProcessedFile;

class ImageResourceProxy
{

    public function __construct(private ImageResource|array|null $subject)
    {
    }

    public function isValid(): bool
    {
        return $this->subject !== null;
    }

    public function getOriginalFilename(): ?string
    {
        if (!$this->subject) {
            return null;
        }
        if ($this->subject instanceof ImageResource) {
            return $this->subject->getOriginalFile()?->getName();
        }
        return $this->subject[3] ?? null;
    }

    public function getProcessedFile(): ?ProcessedFile
    {
        if ($this->subject instanceof ImageResource) {
            return $this->subject->getProcessedFile();
        }
        return null;
    }

    public function getPublicUrl(): ?string
    {
        if ($processedFile = $this->getProcessedFile()) {
            return $processedFile->getPublicUrl();
        } elseif ($this->subject instanceof ImageResource) {
            return $this->subject->getPublicUrl();
        } elseif ($this->subject === null) {
            return null;
        }

        return $this->subject[3] ?? null;
    }

    public function getWidth(): int
    {
        if ($this->subject instanceof ImageResource) {
            return $this->subject->getWidth();
        } elseif (is_array($this->subject)) {
            return $this->subject[0] ?? 0;
        }
        return 0;
    }

    public function getHeight(): int
    {
        if ($this->subject instanceof ImageResource) {
            return $this->subject->getHeight();
        } elseif (is_array($this->subject)) {
            return $this->subject[1] ?? 0;
        }
        return 0;
    }

    public function getExtension(): ?string
    {
        if ($this->subject instanceof ImageResource) {
            return $this->subject->getExtension();
        } elseif (is_array($this->subject)) {
            return $this->subject[2] ?? null;
        }
        return null;
    }

    public function toArray(): array
    {
        if (is_array($this->subject)) {
            return $this->subject;
        }
        if ($this->subject === null) {
            return [];
        }
        return [
            $this->getWidth(),
            $this->getHeight(),
            $this->getExtension(),
            $this->getOriginalFilename(),
            $this->getPublicUrl(),
            'origFile' => $this->subject->getOriginalFile(),
            'origFile_mtime' => $this->subject->getOriginalFile()?->getModificationTime(),
        ];
    }
}
