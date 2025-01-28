<?php
namespace FluidTYPO3\Vhs\Proxy;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;

/**
 * Final/readonly classes are the worst decision in TYPO3 since Neos.
 *
 * @codeCoverageIgnore
 */
class SiteFinderProxy
{
    private SiteFinder $siteFinder;

    public function __construct(SiteFinder $siteFinder)
    {
        $this->siteFinder = $siteFinder;
    }
    public function getAllSites(bool $useCache = true): array
    {
        return $this->siteFinder->getAllSites($useCache);
    }

    public function getSiteByRootPageId(int $rootPageId): Site
    {
        return $this->siteFinder->getSiteByRootPageId($rootPageId);
    }

    public function getSiteByIdentifier(string $identifier): Site
    {
        return $this->siteFinder->getSiteByIdentifier($identifier);
    }

    public function getSiteByPageId(int $pageId, ?array $rootLine = null, ?string $mountPointParameter = null): Site
    {
        return $this->siteFinder->getSiteByPageId($pageId, $rootLine, $mountPointParameter);
    }
}
