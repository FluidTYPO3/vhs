<?php

namespace FluidTYPO3\Vhs\Tests\Fixtures\Classes;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class DummyTypoScriptFrontendController extends TypoScriptFrontendController
{
    public function __construct()
    {
        $this->id = 1;
    }
}
