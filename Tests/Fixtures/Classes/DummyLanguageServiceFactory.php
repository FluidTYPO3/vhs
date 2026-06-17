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
 * Minimal language service factory test double for TYPO3 >=14 readonly class compatibility.
 */
final class DummyLanguageServiceFactory
{
    private static ?DummyLanguageService $service = null;

    public static function setService(?DummyLanguageService $languageService): void
    {
        self::$service = $languageService;
    }

    public function create(Locale|string $locale): DummyLanguageService
    {
        if (self::$service !== null) {
            return self::$service;
        }
        return new DummyLanguageService();
    }

    public static function reset(): void
    {
        self::$service = null;
    }
}
