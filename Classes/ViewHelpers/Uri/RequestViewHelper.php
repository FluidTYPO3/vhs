<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Uri;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Core\ViewHelper\AbstractViewHelper;
use FluidTYPO3\Vhs\Traits\CompileWithRenderStatic;
use FluidTYPO3\Vhs\Utility\RequestResolver;
use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * ### Uri: Request
 *
 * Returns the URI of the requested page (site_url + all the GET params)
 * Uses request normalized params and falls back to request URL from available PSR-7 request objects.
 */
class RequestViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @return string
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {
        $request = RequestResolver::resolveRequestFromRenderingContext($renderingContext);
        $normalizedParams = method_exists($request, 'getAttribute')
            ? $request->getAttribute('normalizedParams')
            : null;
        if ($normalizedParams instanceof NormalizedParams) {
            return $normalizedParams->getRequestUrl();
        }

        if (method_exists($request, 'getUri')) {
            try {
                return (string) $request->getUri();
            } catch (\Throwable $exception) {
                // In unit-test contexts, mock request objects may not have a
                // request URI initialized yet.
            }
        }

        return '';
    }
}
