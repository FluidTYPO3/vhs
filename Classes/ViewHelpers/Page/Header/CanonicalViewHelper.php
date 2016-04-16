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
 *
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Page\Header
 */
class CanonicalViewHelper extends AbstractTagBasedViewHelper {

	use PageRendererTrait;

	/**
	 * @var string
	 */
	protected $tagName = 'link';

	public function initializeArguments() {
		$this->registerUniversalTagAttributes();
		$this->registerArgument('pageUid', 'integer', 'The page uid to check', FALSE, 0);
		$this->registerArgument('normalWhenNoLanguage', 'boolean', 'DEPRECATED: Visibility is now handled by core\'s typolink function.', FALSE);
	}

	/**
	 * @return mixed
	 */
	public function render() {
		if ('BE' === TYPO3_MODE) {
			return '';
		}

		$pageUid = (integer) $this->arguments['pageUid'];
		if (0 === $pageUid) {
			$pageUid = $GLOBALS['TSFE']->id;
		}

		$configuration = array(
			'parameter' => $pageUid,
			'forceAbsoluteUrl' => 1,
		);

		$uri = $GLOBALS['TSFE']->cObj->typoLink_URL($configuration);

		if (TRUE === empty($uri)) {
			return NULL;
		}

		$uri = $GLOBALS['TSFE']->baseUrlWrap($uri);

		$this->tag->addAttribute('rel', 'canonical');
		$this->tag->addAttribute('href', $uri);

		$renderedTag = $this->tag->render();

		if (1 === (integer) $GLOBALS['TSFE']->config['config']['disableAllHeaderCode']) {
			return $renderedTag;
		}

		static::getPageRenderer()->addMetaTag($renderedTag);
	}

}
