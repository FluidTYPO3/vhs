<?php

namespace FluidTYPO3\Vhs\Tests\Fixtures\Classes;

use TYPO3\CMS\Frontend\Controller\FrontendController;

if (!class_exists(
    '\\TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController'
) && class_exists(FrontendController::class)) {
    class_alias(FrontendController::class, '\\TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController');
}

if (class_exists('\\TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController')) {
    /** @noinspection PhpMultipleClassDeclarationsInspection */
    class DummyTypoScriptFrontendController extends \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
    {
        public function __construct()
        {
            $this->id = 1;
        }
    }
} else {
    class DummyTypoScriptFrontendController
    {
        public int $id = 1;
        public mixed $cObj = null;
        public mixed $currentRecord = [];
        public string $absRefPrefix = '';
        public array $register = [];
        public array $workspaceRec = [];
        public mixed $sys_page = null;
        public mixed $fe_user = null;
        public mixed $config = null;
    }
}
