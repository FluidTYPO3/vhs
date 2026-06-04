<?php
namespace FluidTYPO3\Vhs\Utility;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Imaging\ImageResource;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Frontend Simulation Utility
 *
 * Utility to simulate frontend enviroment in backend enviroment.
 */
class FrontendSimulationUtility
{
    /**
     * @var ServerRequestInterface[]
     */
    protected static array $requestBackupStack = [];

    /**
     * Creates a backend-safe frontend-like request context and stores the
     * previous frontend state so it can be restored with resetFrontendEnvironment().
     *
     * @return null Kept for compatibility with older callers that pass the return value to resetFrontendEnvironment().
     */
    public static function simulateFrontendEnvironment(): null
    {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        if (!$request instanceof ServerRequestInterface || !ApplicationType::fromRequest($request)->isBackend()) {
            return null;
        }

        $requestBackup = $request;

        $contentObjectRenderer = self::getContentObjectRenderer();
        $routing = $request->getAttribute('routing');
        $frontendPageId = 0;
        if (is_object($routing) && method_exists($routing, 'getPageId')) {
            $frontendPageId = (int) $routing->getPageId();
        }
        $frontendController = new \stdClass();
        $frontendController->id = $frontendPageId;
        $frontendController->cObj = $contentObjectRenderer;
        $frontendController->fe_user = GeneralUtility::makeInstance(FrontendUserAuthentication::class);
        $frontendController->sys_page = self::getPageRepository();
        $frontendController->sys_language_uid = 0;
        $frontendController->sys_language_content = 0;
        $frontendController->sys_language_contentOL = 0;
        $frontendController->absRefPrefix = '/';
        $frontendController->lastImageInfo = null;
        $frontendController->imagesOnPage = [];
        $frontendController->tmpl = (object) [
            'setup' => [
                'plugin.' => [
                    'tx_vhs.' => [
                        'settings.' => []
                    ]
                ]
            ]
        ];
        $frontendController->currentRecord = '';

        $context = GeneralUtility::makeInstance(Context::class);
        $languageAspect = $context->getAspect('language');
        if (method_exists($languageAspect, 'getId')) {
            $frontendController->sys_language_uid = (int) $languageAspect->getId();
        }

        if (method_exists($contentObjectRenderer, 'setRequest')) {
            $contentObjectRenderer->setRequest($request);
        }

        $request = $request->withAttribute('currentContentObject', $contentObjectRenderer);
        $request = $request->withAttribute('frontend.controller', $frontendController);

        self::$requestBackupStack[] = $requestBackup;

        $GLOBALS['TYPO3_REQUEST'] = $request;

        return null;
    }

    /**
     * Restores the previous frontend context created by simulateFrontendEnvironment().
     */
    public static function resetFrontendEnvironment(mixed $tsfeBackup = null): void
    {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        $isBackendContext = $request instanceof ServerRequestInterface
            && ApplicationType::fromRequest($request)->isBackend();

        $hasRequestBackup = !empty(self::$requestBackupStack);
        $requestBackup = null;
        if ($hasRequestBackup) {
            $requestBackup = array_pop(self::$requestBackupStack);
        }

        if (!$isBackendContext || !$requestBackup instanceof ServerRequestInterface) {
            return;
        }

        $GLOBALS['TYPO3_REQUEST'] = $requestBackup;
    }

    /**
     * @return ContentObjectRenderer
     */
    protected static function getContentObjectRenderer(): ContentObjectRenderer
    {
        try {
            /** @var ContentObjectRenderer $contentObjectRenderer */
            $contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
            return $contentObjectRenderer;
        } catch (\Throwable) {
            return new class () extends ContentObjectRenderer {
                public function __construct()
                {
                }

                /**
                 * TYPO3 13.4 compatibility: keep $fileArray untyped because the
                 * parent ContentObjectRenderer method accepts mixed there.
                 */
                public function getImgResource($file, $fileArray): ?ImageResource
                {
                    return null;
                }
            };
        }
    }

    /**
     * @return object
     */
    protected static function getPageRepository(): object
    {
        try {
            return GeneralUtility::makeInstance(PageRepository::class);
        } catch (\Throwable) {
            return new class {
                public function getRecordOverlay(
                    string $table,
                    array $record,
                    int $languageUid,
                    int $languageContentOL = 0
                ): ?array {
                    return null;
                }

                public function getPage(int $pageUid): ?array
                {
                    return null;
                }

                public function __call(string $name, array $arguments): mixed
                {
                    return null;
                }
            };
        }
    }
}
