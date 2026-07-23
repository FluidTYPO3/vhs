<?php

namespace FluidTYPO3\Vhs\Proxy;

use TYPO3\CMS\Core\Localization\Locale;
use TYPO3\CMS\Core\Localization\LocalizationFactory;
use TYPO3\CMS\Core\SingletonInterface;

class LocalizationFactoryProxy implements SingletonInterface
{
    public function __construct(private LocalizationFactory $localizationFactory)
    {
    }

    public function getParsedData(string $fileReference, Locale|string|null $locale, bool $renewCache = false): array
    {
        return $this->localizationFactory->getParsedData($fileReference, $locale, $renewCache);
    }
}
