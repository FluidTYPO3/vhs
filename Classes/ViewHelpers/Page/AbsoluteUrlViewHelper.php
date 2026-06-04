<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

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
 * Returns a full, absolute URL to this page with all arguments.
 */
class AbsoluteUrlViewHelper extends AbstractViewHelper
{

    use CompileWithRenderStatic;

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
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
            $url = $normalizedParams->getRequestUrl();
            if (empty($url)) {
                return '';
            }
            return $url;
        }

        if (method_exists($request, 'getUri')) {
            try {
                return (string) $request->getUri();
            } catch (\Throwable $exception) {
            }
        }

        return '';
    }
}
