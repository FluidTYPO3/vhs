<?php

declare(strict_types=1);

namespace FluidTYPO3\Vhs\Tests\Functional\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextFactory;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use TYPO3Fluid\Fluid\View\TemplateView;

final class TrimViewHelperTest extends FunctionalTestCase
{
    protected bool $initializeDatabase = false;

    protected array $testExtensionsToLoad = [
        'typo3conf/ext/vhs',
    ];

    #[Test]
    public function renderTrimsChildContent(): void
    {
        $context = $this->get(RenderingContextFactory::class)->create();
        $context->getTemplatePaths()->setTemplateSource(
            '{namespace v=FluidTYPO3\\Vhs\\ViewHelpers}' .
            '<v:format.trim characters=" -"> -- VHS -- </v:format.trim>'
        );

        self::assertSame('VHS', (new TemplateView($context))->render());
    }

    #[Test]
    public function renderTrimsInlineContentArgument(): void
    {
        $context = $this->get(RenderingContextFactory::class)->create();
        $context->getTemplatePaths()->setTemplateSource(
            '{namespace v=FluidTYPO3\\Vhs\\ViewHelpers}' .
            '{value -> v:format.trim(characters: " -")}'
        );

        $view = new TemplateView($context);
        $view->assign('value', ' -- TYPO3 -- ');

        self::assertSame('TYPO3', $view->render());
    }
}
