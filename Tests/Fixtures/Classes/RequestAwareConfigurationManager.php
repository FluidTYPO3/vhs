<?php
namespace FluidTYPO3\Vhs\Tests\Fixtures\Classes;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class RequestAwareConfigurationManager implements ConfigurationManagerInterface
{
    private ServerRequestInterface $request;
    private array $configuration;

    public function __construct(ServerRequestInterface $request, array $configuration = [])
    {
        $this->request = $request;
        $this->configuration = $configuration;
    }

    public function getConfiguration(
        string $configurationType,
        ?string $extensionName = null,
        ?string $pluginName = null
    ): array {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration = []): void
    {
        $this->configuration = $configuration;
    }

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }
}
