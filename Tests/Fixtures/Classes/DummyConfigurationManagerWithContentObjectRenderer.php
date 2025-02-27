<?php

namespace FluidTYPO3\Vhs\Tests\Fixtures\Classes;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class DummyConfigurationManagerWithContentObjectRenderer implements ConfigurationManagerInterface
{
    private ContentObjectRenderer $contentObjectRenderer;

    public function __construct(ContentObjectRenderer $contentObjectRenderer)
    {
        $this->contentObjectRenderer = $contentObjectRenderer;
    }

    public function getConfiguration(
        string $configurationType,
        ?string $extensionName = null,
        ?string $pluginName = null
    ): array {
    }

    public function setConfiguration(array $configuration = []): void
    {
    }

    public function setRequest(ServerRequestInterface $request): void
    {
    }

    public function getContentObject(): ContentObjectRenderer
    {
        return $this->contentObjectRenderer;
    }

    public function isFeatureEnabled(string $featureName): bool
    {
        return false;
    }

    public function setContentObject(ContentObjectRenderer $contentObjectRenderer): void
    {
        $this->contentObjectRenderer = $contentObjectRenderer;
    }
}
