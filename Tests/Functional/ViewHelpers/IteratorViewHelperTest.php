<?php

namespace FluidTYPO3\Vhs\Tests\Functional\ViewHelpers;

class IteratorViewHelperTest extends AbstractFunctionalViewHelperCase
{
    /**
     * @dataProvider iteratorTemplates
     */
    public function testIteratorViewHelpers(string $source, string $expected, array $variables = []): void
    {
        $this->assertTemplateRenders($expected, $source, $variables);
    }

    public function iteratorTemplates(): array
    {
        return [
            'chunk' => [
                '<v:iterator.chunk subject="{items}" count="2" as="chunks"><f:for each="{chunks}" as="chunk">[<f:for each="{chunk}" as="item">{item}</f:for>]</f:for></v:iterator.chunk>',
                '[ab][cd][e]',
                ['items' => ['a', 'b', 'c', 'd', 'e']],
            ],
            'chunk fixed fills requested count' => [
                '<v:iterator.chunk subject="{items}" count="5" fixed="1" as="chunks"><f:for each="{chunks}" as="chunk">x</f:for></v:iterator.chunk>',
                'xxxxx',
                ['items' => ['a', 'b', 'c']],
            ],
            'column' => [
                '<v:iterator.column subject="{rows}" columnKey="name" indexKey="uid" as="names"><f:for each="{names}" as="name" key="uid">{uid}:{name};</f:for></v:iterator.column>',
                '1:One;2:Two;',
                ['rows' => [['uid' => 1, 'name' => 'One'], ['uid' => 2, 'name' => 'Two']]],
            ],
            'diff' => [
                '<f:for each="{v:iterator.diff(a: a, b: b)}" as="item">{item}</f:for>',
                'ac',
                ['a' => ['a', 'b', 'c'], 'b' => ['b']],
            ],
            'explode default glue' => [
                '<v:iterator.explode content="1,2,3" as="items"><f:for each="{items}" as="item">{item}</f:for></v:iterator.explode>',
                '123',
            ],
            'explode custom glue and limit' => [
                '<v:iterator.explode content="1;2;3" glue=";" limit="2" as="items"><f:for each="{items}" as="item">[{item}]</f:for></v:iterator.explode>',
                '[1][2;3]',
            ],
            'extract recursive' => [
                '<f:for each="{v:iterator.extract(content: rows, key: \'title\', recursive: 1, single: 0)}" as="title">{title}</f:for>',
                'OneTwo',
                ['rows' => [['title' => 'One'], ['child' => ['title' => 'Two']]]],
            ],
            'filter by value' => [
                '<f:for each="{v:iterator.filter(subject: items, filter: \'keep\')}" as="item">{item}</f:for>',
                'keepkeep',
                ['items' => ['keep', 'drop', 'keep']],
            ],
            'first' => ['<v:iterator.first haystack="{items}" />', 'a', ['items' => ['a', 'b', 'c']]],
            'implode default glue' => ['<v:iterator.implode content="{items}" />', '1,2,3', ['items' => ['1', '2', '3']]],
            'implode custom glue' => ['<v:iterator.implode content="{items}" glue=";" />', '1;2;3', ['items' => ['1', '2', '3']]],
            'index of found' => ['<v:iterator.indexOf haystack="{items}" needle="c" />', '2', ['items' => ['a', 'b', 'c']]],
            'index of missing' => ['<v:iterator.indexOf haystack="{items}" needle="z" />', '-1', ['items' => ['a', 'b', 'c']]],
            'intersect' => [
                '<f:for each="{v:iterator.intersect(a: a, b: b)}" as="item">{item}</f:for>',
                'greenred',
                ['a' => ['a' => 'green', 'red', 'blue'], 'b' => ['b' => 'green', 'yellow', 'red']],
            ],
            'keys' => [
                '<v:iterator.keys subject="{items}" as="keys"><f:for each="{keys}" as="key">{key}</f:for></v:iterator.keys>',
                'abc',
                ['items' => ['a' => 1, 'b' => 2, 'c' => 3]],
            ],
            'last' => ['<v:iterator.last haystack="{items}" />', 'c', ['items' => ['a', 'b', 'c']]],
            'merge without keys' => [
                '<f:for each="{v:iterator.merge(a: a, b: b, useKeys: 0)}" as="item">{item}</f:for>',
                'bar',
                ['a' => ['foo'], 'b' => ['bar']],
            ],
            'next' => ['<v:iterator.next haystack="{items}" needle="b" />', 'c', ['items' => ['a', 'b', 'c']]],
            'pop' => ['<v:iterator.pop subject="{items}" />', 'bar', ['items' => ['foo', 'bar']]],
            'previous' => ['<v:iterator.previous haystack="{items}" needle="c" />', 'b', ['items' => ['a', 'b', 'c']]],
            'push append' => [
                '<v:iterator.push subject="{items}" add="baz" as="pushed"><f:for each="{pushed}" as="item">{item}</f:for></v:iterator.push>',
                'foobarbaz',
                ['items' => ['foo', 'bar']],
            ],
            'random' => [
                '<v:iterator.random subject="{items}" />',
                'foo',
                ['items' => ['foo']],
            ],
            'range' => [
                '<v:iterator.range low="2" high="6" step="2" as="range"><f:for each="{range}" as="number">{number}</f:for></v:iterator.range>',
                '246',
            ],
            'reverse' => [
                '<v:iterator.reverse subject="{items}" as="reversed"><f:for each="{reversed}" as="item">{item}</f:for></v:iterator.reverse>',
                'barfoo',
                ['items' => ['foo', 'bar']],
            ],
            'shift' => ['<v:iterator.shift subject="{items}" />', 'foo', ['items' => ['foo', 'bar']]],
            'slice' => [
                '<v:iterator.slice haystack="{items}" start="1" length="2" as="slice"><f:for each="{slice}" as="item">{item}</f:for></v:iterator.slice>',
                'bc',
                ['items' => ['a', 'b', 'c', 'd']],
            ],
            'sort' => [
                '<v:iterator.sort subject="{items}" as="sorted"><f:for each="{sorted}" as="item">{item}</f:for></v:iterator.sort>',
                'abc',
                ['items' => ['b' => 'b', 'c' => 'c', 'a' => 'a']],
            ],
            'split' => [
                '<v:iterator.split subject="foobar" length="2" as="parts"><f:for each="{parts}" as="part">[{part}]</f:for></v:iterator.split>',
                '[fo][ob][ar]',
            ],
            'unique' => [
                '<v:iterator.unique subject="{items}" as="unique"><f:for each="{unique}" as="item">{item}</f:for></v:iterator.unique>',
                'bar',
                ['items' => ['foo' => 'bar', 'baz' => 'bar']],
            ],
            'values' => [
                '<v:iterator.values subject="{items}" as="values"><f:for each="{values}" as="item">{item}</f:for></v:iterator.values>',
                'bar',
                ['items' => ['foo' => 'bar']],
            ],
        ];
    }
}
