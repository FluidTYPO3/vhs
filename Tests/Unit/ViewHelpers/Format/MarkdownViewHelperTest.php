<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;

/**
 * Class MarkdownViewHelperTest
 */
class MarkdownViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     */
    public function supportsHtmlEntities()
    {
        if (trim(shell_exec('which markdown')) === '') {
            $this->expectException(NoSuchCacheException::class);
        }
        $this->executeViewHelper(['text' => 'test < test', 'trim' => true, 'htmlentities' => true]);
    }

    /**
     * @test
     */
    public function rendersMarkdown()
    {
        if (trim(shell_exec('which markdown')) === '') {
            $this->expectException(NoSuchCacheException::class);
        }
        $this->executeViewHelper(['text' => 'test', 'trim' => true, 'htmlentities' => false]);
    }
}
