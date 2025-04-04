<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Uri;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */


use FluidTYPO3\Vhs\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Renders Gravatar URI.
 */
class GravatarViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

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
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('email', 'string', 'Email address', true);
        $this->registerArgument('size', 'integer', 'Size in pixels, defaults to 80px [ 1 - 2048 ]');
        $this->registerArgument(
            'imageSet',
            'string',
            'Default image set to use. Possible values [ 404 | mm | identicon | monsterid | wavatar ] '
        );
        $this->registerArgument('maximumRating', 'string', 'Maximum rating (inclusive) [ g | pg | r | x ]');
        $this->registerArgument(
            'secure',
            'boolean',
            'If it is FALSE will return the un secure Gravatar domain (www.gravatar.com)',
            false,
            true
        );
    }

    /**
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        /** @var string $email */
        $email = $arguments['email'];
        $size = $arguments['size'];
        $imageSet = $arguments['imageSet'];
        $maximumRating = $arguments['maximumRating'];
        $secure = (boolean) $arguments['secure'];

        $url = $secure ? static::GRAVATAR_SECURE_BASEURL : static::GRAVATAR_BASEURL;
        $url .= md5(strtolower(trim($email)));
        $query = http_build_query(['s' => $size, 'd' => $imageSet, 'r' => $maximumRating]);
        $url .= !empty($query) ? '?' . $query : '';

        return $url;
    }
}
