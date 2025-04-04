<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\TagViewHelperCompatibility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Renders Gravatar <img/> tag.
 */
class GravatarViewHelper extends AbstractTagBasedViewHelper
{
    use TagViewHelperCompatibility;

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
     * @var string
     */
    protected $tagName = 'img';

    /**
     * Initialize arguments.
     * Size argument has no default value to prevent the creation of an unnecessary URI parameter.
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
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
     * Render method
     *
     * @return string
     */
    public function render()
    {
        /** @var string $email */
        $email = $this->arguments['email'];
        $size = $this->checkArgument('size');
        $imageSet = $this->checkArgument('imageSet');
        $maximumRating = $this->checkArgument('maximumRating');
        $secure = (boolean) $this->arguments['secure'];

        $url = $secure ? static::GRAVATAR_SECURE_BASEURL : static::GRAVATAR_BASEURL;
        $url .= md5(strtolower(trim($email)));
        $query = http_build_query(['s' => $size, 'd' => $imageSet, 'r' => $maximumRating]);
        $url .= !empty($query) ? '?' . $query : '';
        $this->tag->addAttribute('src', $url);
        $this->tag->forceClosingTag(true);

        return $this->tag->render();
    }

    /**
     * Check if an argument is passed
     *
     * @return mixed
     */
    private function checkArgument(string $argument)
    {
        return $this->arguments[$argument] ?? null;
    }
}
