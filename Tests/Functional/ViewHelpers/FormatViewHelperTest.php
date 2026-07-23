<?php

namespace FluidTYPO3\Vhs\Tests\Functional\ViewHelpers;

class FormatViewHelperTest extends AbstractFunctionalViewHelperCase
{
    /**
     * @dataProvider formatTemplates
     */
    public function testFormatViewHelpers(string $source, string $expected, array $variables = []): void
    {
        $this->assertTemplateRenders($expected, $source, $variables);
    }

    public function formatTemplates(): array
    {
        return [
            'append argument' => ['<v:format.append subject="before" add="after" />', 'beforeafter'],
            'append child content' => ['<v:format.append add="after">before</v:format.append>', 'beforeafter'],
            'date range default' => [
                '<v:format.dateRange start="1" end="86401" startFormat="Y-m-d" endFormat="Y-m-d" />',
                '1970-01-01 - 1970-01-02',
            ],
            'date range interval' => [
                '<v:format.dateRange start="1" intervalFormat="P3M" startFormat="Y-m-d" endFormat="Y-m-d" />',
                '1970-01-01 - 1970-04-01',
            ],
            'date range return component' => [
                '<v:format.dateRange start="1" end="86401" return="d" />',
                '1',
            ],
            'eliminate non ascii' => ['<v:format.eliminate nonAscii="1">foo bar</v:format.eliminate>', 'foo bar'],
            'eliminate letters' => ['<v:format.eliminate letters="1">foo123bar</v:format.eliminate>', '123'],
            'eliminate digits' => ['<v:format.eliminate digits="1">foo123bar</v:format.eliminate>', 'foobar'],
            'eliminate whitespace' => ['<v:format.eliminate whitespace="1"> trimmed </v:format.eliminate>', 'trimmed'],
            'eliminate characters' => ['<v:format.eliminate characters="abc">abcdef</v:format.eliminate>', 'def'],
            'hash md5' => [
                '<v:format.hash algorithm="md5">please hash me</v:format.hash>',
                '50f6980d1002ddfdeb3d8e40bc634d43',
            ],
            'hash sha1' => [
                '<v:format.hash algorithm="sha1">please hash me</v:format.hash>',
                '8355145bb9c38cf4d829ca3e183f1092313dd55c',
            ],
            'hide default' => ['before<v:format.hide>hidden</v:format.hide>after', 'beforeafter'],
            'hide disabled' => ['<v:format.hide disabled="1">shown</v:format.hide>', 'shown'],
            'json decode' => [
                '<v:variable.set name="decoded" value="{v:format.json.decode(json: json)}" />{decoded.foo}:{decoded.bar}:{decoded.baz}',
                'bar:1:1',
                ['json' => '{"foo":"bar","bar":true,"baz":1,"foobar":null}'],
            ],
            'json encode array' => [
                '<v:format.json.encode value="{value}" />',
                '{"foo":"bar","bar":true,"baz":1,"foobar":null}',
                ['value' => ['foo' => 'bar', 'bar' => true, 'baz' => 1, 'foobar' => null]],
            ],
            'placeholder image' => [
                '<v:format.placeholder.image width="100" height="100" />',
                '<img src="https://via.placeholder.com/100/333333/FFFFFF" alt="https://via.placeholder.com/100/333333/FFFFFF" width="100" height="100" />',
            ],
            'placeholder image with text' => [
                '<v:format.placeholder.image width="100" height="100" text="test" />',
                '<img src="https://via.placeholder.com/100/333333/FFFFFF/?text=test" alt="https://via.placeholder.com/100/333333/FFFFFF/?text=test" width="100" height="100" />',
            ],
            'plaintext' => [
                '<v:format.plaintext content="{content}" />',
                "This string\nis plain-text formatted",
                ['content' => "\tThis string\n\tis plain-text formatted"],
            ],
            'preg replace' => [
                '<v:format.pregReplace subject="foo123bar" pattern="{pattern}" replacement="baz" />',
                'foobazbar',
                ['pattern' => '/[0-9]{3}/'],
            ],
            'prepend' => ['<v:format.prepend add="after">before</v:format.prepend>', 'afterbefore'],
            'replace' => [
                '<v:format.replace content="foobar" substring="foo" replacement="" />',
                'bar',
            ],
            'replace child content' => [
                '<v:format.replace substring="foo" replacement="">foobar</v:format.replace>',
                'bar',
            ],
            'replace returns count' => [
                '<v:format.replace content="foobar" substring="foo" replacement="" returnCount="1" />',
                '1',
            ],
            'sanitize string' => [
                '<v:format.sanitizeString string="THIS SHOULD BE LOWERCASE" />',
                'this-should-be-lowercase',
            ],
            'substring offset' => [
                '<v:format.substring content="foobar" start="3" />',
                'bar',
            ],
            'substring length' => [
                '<v:format.substring content="foobar" start="2" length="3" />',
                'oba',
            ],
            'trim default' => [
                '<v:format.trim content="{content}" />',
                'trimmed',
                ['content' => ' trimmed '],
            ],
            'trim characters' => [
                '<v:format.trim content="abc trimmed abc" characters="abc " />',
                'trimmed',
            ],
            'url decode' => [
                '<v:format.url.decode content="this%20is%20url%20encoded" />',
                'this is url encoded',
            ],
            'url encode' => [
                '<v:format.url.encode content="this is url decoded" />',
                'this%20is%20url%20decoded',
            ],
            'word wrap' => [
                '<v:format.wordWrap subject="one two three four" limit="7" break="|" glue="|" />',
                'one two|three|four|',
            ],
        ];
    }

    public function testCaseFormatting(): void
    {
        $source = '<v:format.case string="lots of words" case="ucwords" />'
            . '|<v:format.case string="lowercase_underscored" case="CamelCase" />'
            . '|<v:format.case string="lowercase_underscored" case="lowerCamelCase" />'
            . '|<v:format.case string="CamelCase" case="lowercase_underscored" />'
            . '|<v:format.case string="unknown format MIXED WITH All Cases" case="unsupported" />';

        self::assertSame(
            'Lots Of Words|LowercaseUnderscored|lowercaseUnderscored|camel_case|unknown format MIXED WITH All Cases',
            $this->executeTemplateWithRequest($source, $this->createFrontendRequest())
        );
    }
}
