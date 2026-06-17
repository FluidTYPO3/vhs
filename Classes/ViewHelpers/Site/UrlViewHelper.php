<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Site;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\CompileWithRenderStatic;
use FluidTYPO3\Vhs\Utility\RequestResolver;
use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use FluidTYPO3\Vhs\Core\ViewHelper\AbstractViewHelper;

/**
 * ### Site: URL
 *
 * Returns the website URL determined from the current request.
 */
class UrlViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $request = RequestResolver::resolveRequestFromRenderingContext($renderingContext);
        $normalizedParams = method_exists($request, 'getAttribute')
            ? $request->getAttribute('normalizedParams')
            : null;
        if ($normalizedParams instanceof NormalizedParams) {
            return $normalizedParams->getSiteUrl();
        }

        if (method_exists($request, 'getUri')) {
            try {
                $uri = $request->getUri();
                if (method_exists($uri, 'getScheme') && method_exists($uri, 'getHost')) {
                    $path = (string) $uri->getPath();
                    if ('' === $path || '/' === $path) {
                        $path = '/';
                    }
                    $path = rtrim(dirname($path), '/');
                    return $uri->withPath($path . '/')->withQuery('')->withFragment('')->__toString();
                }
            } catch (\Throwable $exception) {
            }
        }

        return '';
    }
}
