<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page\Header;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\PageRendererTrait;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

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
     * @return mixed
     */
    public function render()
    {
        if ('BE' === TYPO3_MODE) {
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

        $uriBuilder = $this->controllerContext->getUriBuilder();
        $uri = $uriBuilder->reset()
            ->setTargetPageUid($pageUid)
            ->setUseCacheHash(true)
            ->setCreateAbsoluteUri(true)
            ->setAddQueryString(true)
            ->setAddQueryStringMethod($queryStringMethod)
            ->setArgumentsToBeExcludedFromQueryString(['id'])
            ->build();

        if (true === empty($uri)) {
            return null;
        }

        $uri = $GLOBALS['TSFE']->baseUrlWrap($uri);

        $this->tag->addAttribute('rel', 'canonical');
        $this->tag->addAttribute('href', $uri, false);

        $renderedTag = $this->tag->render();

        if (1 === (integer) $GLOBALS['TSFE']->config['config']['disableAllHeaderCode']) {
            return $renderedTag;
        }

        static::getPageRenderer()->addMetaTag($renderedTag);
    }
}
