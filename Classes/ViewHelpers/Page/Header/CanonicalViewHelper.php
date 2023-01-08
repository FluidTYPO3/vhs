<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page\Header;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\PageRendererTrait;
use FluidTYPO3\Vhs\Utility\ContextUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Returns the current canonical url in a link tag.
 */
class CanonicalViewHelper extends AbstractTagBasedViewHelper
{
    use PageRendererTrait;

    /**
     * @var string
     */
    protected $tagName = 'link';

    /**
     * @return void
     */
    public function initializeArguments()
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
    public function render()
    {
        if (ContextUtility::isBackend()) {
            return '';
        }

        $pageUid = (integer) $this->arguments['pageUid'];
        if (0 === $pageUid) {
            $pageUid = $GLOBALS['TSFE']->id;
        }

        $queryStringMethod = $this->arguments['queryStringMethod'];
        if (!in_array($queryStringMethod, ['GET', 'POST', 'GET,POST'], true)) {
            throw new \InvalidArgumentException(
                'The parameter "queryStringMethods" must be one of "GET", "POST" or "GET,POST".',
                1475337546
            );
        }

        /** @var UriBuilder $uriBuilder */
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

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

        if (true === empty($uri)) {
            return '';
        }

        $uri = $GLOBALS['TSFE']->baseUrlWrap($uri);

        $this->tag->addAttribute('rel', 'canonical');
        $this->tag->addAttribute('href', $uri, false);

        $renderedTag = $this->tag->render();

        if (1 === (integer) $GLOBALS['TSFE']->config['config']['disableAllHeaderCode']) {
            return $renderedTag;
        }

        static::getPageRenderer()->addHeaderData($renderedTag);
        return '';
    }
}
