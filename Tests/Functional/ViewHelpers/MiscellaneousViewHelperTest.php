<?php

namespace FluidTYPO3\Vhs\Tests\Functional\ViewHelpers;

use FluidTYPO3\Vhs\Tests\Fixtures\Domain\Model\Foo;

class MiscellaneousViewHelperTest extends AbstractFunctionalViewHelperCase
{
    /**
     * @dataProvider miscellaneousTemplates
     */
    public function testMiscellaneousViewHelpers(string $source, string $expected, array $variables = []): void
    {
        $this->assertTemplateRenders($expected, $source, $variables);
    }

    public function miscellaneousTemplates(): array
    {
        return [
            'call object method' => [
                '<v:call object="{object}" method="getBar" />',
                'baz',
                ['object' => new Foo()],
            ],
            'const' => ['<v:const name="PHP_INT_SIZE" />', (string) PHP_INT_SIZE],
            'iterator for' => ['<v:iterator.for from="1" to="3">x</v:iterator.for>', 'xxx'],
            'iterator loop' => ['<v:iterator.loop count="3">x</v:iterator.loop>', 'xxx'],
            'tag' => ['<v:tag name="div" class="test">content</v:tag>', '<div class="test">content</div>'],
            'tag hides empty content' => ['<v:tag name="div" hideIfEmpty="1"></v:tag>', ''],
            'form field name' => ['<v:form.fieldName name="test" />', 'test'],
            'render ascii scalar' => ['<v:render.ascii ascii="64" />', '@'],
            'render ascii array' => ['<v:render.ascii ascii="{ascii}" />', 'ABC', ['ascii' => [65, 66, 67]]],
            'uri gravatar' => [
                '<v:uri.gravatar email="test@example.com" />',
                'https://secure.gravatar.com/avatar/55502f40dc8b7c769880b10874abc9d0',
            ],
            'media gravatar' => [
                '<v:media.gravatar email="test@example.com" />',
                '<img src="https://secure.gravatar.com/avatar/55502f40dc8b7c769880b10874abc9d0"></img>',
            ],
            'media youtube' => [
                '<v:media.youtube videoId="M7lc1UVf-VE" hideInfo="1" start="30" />',
                '<iframe width="640" height="385" src="//www.youtube-nocookie.com/embed/M7lc1UVf-VE?rel=0&amp;showinfo=0&amp;start=30" frameborder="0" allowFullScreen="allowFullScreen"></iframe>',
            ],
            'media spotify' => [
                '<v:media.spotify spotifyUri="spotify:track:test" />',
                '<iframe src="https://embed.spotify.com/?uri=spotify:track:test&amp;theme=black&amp;view=list" width="300" height="380" allowtransparancy="true" frameborder="0"></iframe>',
            ],
            'or keeps content' => ['<v:or content="content" alternative="alternative" />', 'content'],
            'or uses alternative' => ['<v:or content="" alternative="alternative" />', 'alternative'],
            'variable convert integer' => ['<v:variable.convert value="12" type="integer" />', '12'],
            'variable get direct' => ['<v:variable.get name="test" />', 'value', ['test' => 'value']],
            'variable get nested' => ['<v:variable.get name="test.nested" />', 'value', ['test' => ['nested' => 'value']]],
            'variable preg match' => [
                '<v:variable.pregMatch subject="foo123bar" pattern="/[0-9]+/" as="matches">{matches.0}</v:variable.pregMatch>',
                '123',
            ],
            'variable set argument' => [
                '<v:variable.set name="test" value="changed" />{test}',
                'changed',
                ['test' => 'original'],
            ],
            'variable set child content' => [
                '<v:variable.set name="test">changed</v:variable.set>{test}',
                'changed',
            ],
            'variable unset' => [
                '<v:variable.unset name="test" /><f:if condition="{test}"><f:then>set</f:then><f:else>unset</f:else></f:if>',
                'unset',
                ['test' => 'value'],
            ],
        ];
    }

    public function testMediaVimeoRendersPlayer(): void
    {
        $result = $this->executeTemplate('<v:media.vimeo videoId="123" />');
        self::assertStringContainsString('src="//player.vimeo.com/video/123?', (string) $result);
        self::assertStringContainsString('width="640"', (string) $result);
        self::assertStringContainsString('height="360"', (string) $result);
    }

    public function testFormFieldNameUsesFormViewHelperContext(): void
    {
        $result = $this->executeTemplateWithRenderingContext(
            '<v:form.fieldName property="test" />',
            [],
            static function ($renderingContext): void {
                $container = $renderingContext->getViewHelperVariableContainer();
                $container->add(\TYPO3\CMS\Fluid\ViewHelpers\FormViewHelper::class, 'formObjectName', 'object');
                $container->add(\TYPO3\CMS\Fluid\ViewHelpers\FormViewHelper::class, 'fieldNamePrefix', 'prefix');
            }
        );

        self::assertSame('prefix[object][test]', $result);
    }
}
