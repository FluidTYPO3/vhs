<?php
namespace FluidTYPO3\Vhs\Tests\Fixtures\Classes;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Localization\Locale;

/**
 * Minimal language service test double for TYPO3 >=14 readonly class compatibility.
 */
final class DummyLanguageService
{
    private array $translationMap;
    private array $arguments = [];
    public function __construct(array $translationMap = [])
    {
        $this->translationMap = $translationMap;
    }

    public function translate(string $id, string $domain, array $arguments = [], ?string $default = null, Locale|string|null $locale = null): string|\Stringable|null
    {
        $this->arguments = $arguments;
        return $this->translationMap[$id] ?? $this->translationMap[$domain . ':' . $id] ?? $default;
    }

    public function getLastArguments(): array
    {
        return $this->arguments;
    }
}
