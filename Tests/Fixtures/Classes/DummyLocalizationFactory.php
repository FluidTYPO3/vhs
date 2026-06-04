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
 * Minimal localization factory test double for TYPO3 >=14 where
 * the original factory class is readonly and cannot be mocked.
 */
final class DummyLocalizationFactory
{
    /**
     * @param string $fileReference
     * @param Locale|string|null $locale
     * @param bool $renewCache
     * @return array<string, mixed>
     */
    public function getParsedData(string $fileReference, Locale|string|null $locale, bool $renewCache = false): array
    {
        return [];
    }
}
