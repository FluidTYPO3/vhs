<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Uri;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */


use FluidTYPO3\Vhs\Traits\DefaultRenderMethodViewHelperTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Renders Gravatar URI
 *
 * @author Juan Manuel Vergés Solanas <juanmanuel@vergessolanas.es>
 * @package Vhs
 * @subpackage ViewHelpers\Uri
 */
class GravatarViewHelper extends AbstractViewHelper {

	use DefaultRenderMethodViewHelperTrait;

	/**
	 * Base url
	 *
	 * @var string
	 */
	const GRAVATAR_BASEURL = 'http://www.gravatar.com/avatar/';

	/**
	 * Base secure url
	 *
	 * @var string
	 */
	const GRAVATAR_SECURE_BASEURL = 'https://secure.gravatar.com/avatar/';

	/**
	 * Initialize arguments.
	 * Size argument has no default value to prevent the creation of an unnecessary URI parameter.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		$this->registerArgument('email', 'string', 'Email address', TRUE);
		$this->registerArgument('size', 'integer', 'Size in pixels, defaults to 80px [ 1 - 2048 ]', FALSE);
		$this->registerArgument('imageSet', 'string', 'Default image set to use. Possible values [ 404 | mm | identicon | monsterid | wavatar ] ', FALSE);
		$this->registerArgument('maximumRating', 'string', 'Maximum rating (inclusive) [ g | pg | r | x ]', FALSE);
		$this->registerArgument('secure', 'boolean', 'If it is FALSE will return the un secure Gravatar domain (www.gravatar.com)', FALSE, TRUE);
	}

	/**
	 * @param array $arguments
	 * @param \Closure $renderChildrenClosure
	 * @param RenderingContextInterface $renderingContext
	 * @return mixed
	 */
	public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext) {
		$email = $arguments['email'];
		$size = $arguments['size'];
		$imageSet = $arguments['imageSet'];
		$maximumRating = $arguments['maximumRating'];
		$secure = (boolean) $arguments['secure'];

		$url = (TRUE === $secure ? self::GRAVATAR_SECURE_BASEURL : self::GRAVATAR_BASEURL);
		$url .= md5(strtolower(trim($email)));
		$query = http_build_query(array('s' => $size, 'd' => $imageSet, 'r' => $maximumRating));
		$url .= (FALSE === empty($query) ? '?' . $query : '');

		return $url;
	}

}
