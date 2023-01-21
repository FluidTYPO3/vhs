<?php
namespace FluidTYPO3\Vhs\Tests\Unit\Traits;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Fixtures\Classes\DummyTagViewHelper;
use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;

class TagViewHelperTraitTest extends AbstractTestCase
{
    /**
     * @dataProvider getRenderTestValues
     */
    public function testRenderTag(
        string $expected,
        string $tagName,
        string $value,
        array $arguments
    ): void {
        $subject = new DummyTagViewHelper();
        $subject->arguments = $arguments;
        $subject->registerArguments();

        self::assertSame($expected, $subject->testRenderTag($tagName, $value));
    }

    public function getRenderTestValues(): array
    {
        return [
            'with empty tag name with value w/o forceClosingTag w/o hideIfEmpty' => ['value', '', 'value', ['forceClosingTag' => false, 'hideIfEmpty' => false]],
            'with tag name with value w/o forceClosingTag w/o hideIfEmpty' => ['<tag>value</tag>', 'tag', 'value', ['forceClosingTag' => false, 'hideIfEmpty' => false]],
            'with tag name without value w/o forceClosingTag w/o hideIfEmpty' => ['<tag />', 'tag', '', ['forceClosingTag' => false, 'hideIfEmpty' => false]],
            'with tag name without value with forceClosingTag w/o hideIfEmpty' => ['<tag></tag>', 'tag', '', ['forceClosingTag' => true, 'hideIfEmpty' => false]],
            'with tag name with value w/o forceClosingTag with hideIfEmpty' => ['', 'tag', '', ['forceClosingTag' => false, 'hideIfEmpty' => true]],
        ];
    }

    /**
     * @dataProvider getRenderChildTagTestValues
     */
    public function testRenderChildTag(
        string $expected,
        string $tagName,
        string $value,
        array $arguments,
        string $mode,
        bool $forceClosingTag
    ): void {
        $subject = new DummyTagViewHelper();
        $subject->arguments = $arguments;
        $subject->registerArguments();

        $subject->testRenderTag($tagName, $value);
        $subject->testRenderChildTag($tagName, [], $forceClosingTag, $mode);
        self::assertSame($expected, $subject->tag->render());
    }

    public function getRenderChildTagTestValues(): array
    {
        return [
            'mode append with tag name with value w/o forceClosingTag w/o hideIfEmpty' => ['<tag>value<tag /></tag>', 'tag', 'value', ['forceClosingTag' => false, 'hideIfEmpty' => false], 'append', false],
            'mode append with tag name without value w/o forceClosingTag w/o hideIfEmpty' => ['<tag><tag /></tag>', 'tag', '', ['forceClosingTag' => false, 'hideIfEmpty' => false], 'append', false],
            'mode append with tag name without value with forceClosingTag w/o hideIfEmpty' => ['<tag><tag></tag></tag>', 'tag', '', ['forceClosingTag' => true, 'hideIfEmpty' => false], 'append', true],
            'mode prepend with empty tag name with value w/o forceClosingTag w/o hideIfEmpty' => ['<div />', '', 'value', ['forceClosingTag' => false, 'hideIfEmpty' => false], 'prepend', false],
            'mode prepend with tag name with value w/o forceClosingTag w/o hideIfEmpty' => ['<tag><tag />value</tag>', 'tag', 'value', ['forceClosingTag' => false, 'hideIfEmpty' => false], 'prepend', false],
            'mode prepend with tag name without value w/o forceClosingTag w/o hideIfEmpty' => ['<tag><tag /></tag>', 'tag', '', ['forceClosingTag' => false, 'hideIfEmpty' => false], 'prepend', false],
            'mode prepend with tag name without value with forceClosingTag w/o hideIfEmpty' => ['<tag><tag></tag></tag>', 'tag', '', ['forceClosingTag' => true, 'hideIfEmpty' => false], 'prepend', true],
            'mode invalid with empty tag name with value w/o forceClosingTag w/o hideIfEmpty' => ['<div />', '', 'value', ['forceClosingTag' => false, 'hideIfEmpty' => false], 'invalid', false],
            'mode invalid with tag name with value w/o forceClosingTag w/o hideIfEmpty' => ['<tag>value</tag>', 'tag', 'value', ['forceClosingTag' => false, 'hideIfEmpty' => false], 'invalid', false],
            'mode invalid with tag name without value w/o forceClosingTag w/o hideIfEmpty' => ['<tag />', 'tag', '', ['forceClosingTag' => false, 'hideIfEmpty' => false], 'invalid', false],
            'mode invalid with tag name without value with forceClosingTag w/o hideIfEmpty' => ['<div />', 'tag', '', ['forceClosingTag' => true, 'hideIfEmpty' => true], 'invalid', true],
        ];
    }
}
