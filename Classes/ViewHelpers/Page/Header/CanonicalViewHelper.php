<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page\Header;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\PageRendererTrait;
use FluidTYPO3\Vhs\Traits\TagViewHelperCompatibility;
use FluidTYPO3\Vhs\Utility\RequestResolver;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Frontend\Page\PageInformation;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Returns the current canonical url in a link tag.
 */
class CanonicalViewHelper extends AbstractTagBasedViewHelper
{
    use PageRendererTrait;
    use TagViewHelperCompatibility;

    /**
     * @var string
     */
    protected $tagName = 'link';

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerArgument('pageUid', 'integer', 'The page uid to check', false, 0);
        $this->registerArgument(
            'queryStringMethod',
            'string',
            'From which place to add parameters. Values: "GET", "POST" and "GET,POST". See ' .
            'https://docs.typo3.org/typo3cms/TyposcriptReference/Functions/Typolink/Index.html, addQueryString.method',
            false,
            'GET'
        );
        $this->registerArgument(
            'normalWhenNoLanguage',
            'boolean',
            'DEPRECATED: Visibility is now handled by core\'s typolink function.'
        );
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $request = RequestResolver::tryResolveRequestFromRenderingContext($this->renderingContext, false);
        if (!$request instanceof ServerRequestInterface) {
            return '';
        }

        if (ApplicationType::fromRequest($request)->isBackend()) {
            return '';
        }

        /** @var int $pageUid */
        $pageUid = $this->arguments['pageUid'];
        $pageUid = (int) $pageUid;
        if (0 === $pageUid) {
            $pageUid = $this->getCurrentPageUid($request);
        }

        /** @var string $queryStringMethod */
        $queryStringMethod = $this->arguments['queryStringMethod'];
        if (!in_array($queryStringMethod, ['GET', 'POST', 'GET,POST'], true)) {
            throw new \InvalidArgumentException(
                'The parameter "queryStringMethods" must be one of "GET", "POST" or "GET,POST".',
                1475337546
            );
        }

        /** @var UriBuilder $uriBuilder */
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $uriBuilder->setRequest($request);

        $uriBuilder = $uriBuilder->reset()
            ->setTargetPageUid($pageUid)
            ->setCreateAbsoluteUri(true)
            ->setAddQueryString(true)
            ->setArgumentsToBeExcludedFromQueryString(['id']);
        if (method_exists($uriBuilder, 'setAddQueryStringMethod')) {
            $uriBuilder->setAddQueryStringMethod($queryStringMethod);
        }
        if (method_exists($uriBuilder, 'setUseCacheHash')) {
            $uriBuilder->setUseCacheHash(true);
        }

        $uri = $uriBuilder->build();

        if (empty($uri)) {
            return '';
        }

        $this->tag->addAttribute('rel', 'canonical');
        $this->tag->addAttribute('href', $uri, false);

        $renderedTag = $this->tag->render();

        if ($this->isAllHeaderCodeDisabled($request)) {
            return $renderedTag;
        }

        static::getPageRenderer()->addHeaderData($renderedTag);
        return '';
    }

    private function getCurrentPageUid(ServerRequestInterface $request): int
    {
        $pageInformation = $request->getAttribute('frontend.page.information');
        if ($pageInformation instanceof PageInformation) {
            return $pageInformation->getId();
        }

        $routing = $request->getAttribute('routing');
        if ($routing instanceof PageArguments) {
            return $routing->getPageId();
        }

        throw new \RuntimeException('Unable to resolve current page uid from frontend request.', 1774448270);
    }

    private function isAllHeaderCodeDisabled(ServerRequestInterface $request): bool
    {
        $frontendTypoScript = $request->getAttribute('frontend.typoscript');
        if (!is_object($frontendTypoScript)
            || !method_exists($frontendTypoScript, 'hasConfig')
            || !method_exists($frontendTypoScript, 'getConfigArray')
            || !$frontendTypoScript->hasConfig()
        ) {
            return false;
        }

        $config = $frontendTypoScript->getConfigArray();
        return 1 === (int) ($config['disableAllHeaderCode'] ?? 0);
    }
}
