<?php
namespace FluidTYPO3\Vhs\Utility;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * In-memory replacement for the TSFE `register` array, which TYPO3 core
 * removed in v14 (TypoScriptFrontendController no longer has a `register`
 * property and `$GLOBALS['TSFE']` is no longer populated). Used by the
 * Variable\Register Set/Get ViewHelpers so they keep working independent
 * of TSFE availability, on every supported core version.
 */
class VariableRegister
{
    /**
     * @var array<string, mixed>
     */
    private static array $storage = [];

    public static function set(string $name, mixed $value): void
    {
        self::$storage[$name] = $value;
    }

    public static function get(string $name): mixed
    {
        return self::$storage[$name] ?? null;
    }
}
