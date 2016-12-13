<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class MarkdownViewHelperTest
 */
class MarkdownViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function supportsHtmlEntities()
    {
        if (trim(shell_exec('which markdown')) === '') {
            $this->expectViewHelperException('Use of Markdown requires the "markdown" shell utility to be installed');
        }
        $this->executeViewHelper(['text' => 'test < test', 'trim' => true, 'htmlentities' => true]);
    }

    /**
     * @test
     */
    public function rendersMarkdown()
    {
        if (trim(shell_exec('which markdown')) === '') {
            $this->expectViewHelperException('Use of Markdown requires the "markdown" shell utility to be installed');
        }
        $this->executeViewHelper(['text' => 'test', 'trim' => true, 'htmlentities' => false]);
    }
}
