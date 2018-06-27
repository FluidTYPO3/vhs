<?php

namespace MatthiasMullie\PathConverter\Tests;

use MatthiasMullie\PathConverter\Converter;

/**
 * Converter test case.
 */
class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Converter, provided by dataProvider.
     *
     * @test
     * @dataProvider dataProvider
     */
    public function convert($relative, $from, $to, $expected)
    {
        $converter = new Converter($from, $to);
        $result = $converter->convert($relative);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array [relative, from, to, expected result]
     */
    public function dataProvider()
    {
        $tests = array();

        $tests[] = array(
            '../images/img.jpg',
            '/home/forkcms/frontend/core/layout/css',
            '/home/forkcms/frontend/cache/minified_css',
            '../../core/layout/images/img.jpg',
        );

        $tests[] = array(
            '../../images/icon.gif',
            '/css/imports/',
            '/css/',
            '../images/icon.gif',
        );

        // absolute path - doesn't make sense :)
        $tests[] = array(
            '/home/username/file.txt',
            '/css/imports',
            '/css',
            '/home/username/file.txt',
        );

        $tests[] = array(
            'image.jpg',
            'tests/css/sample/convert_relative_path/source',
            'tests/css/sample/convert_relative_path/source',
            'image.jpg',
        );

        $tests[] = array(
            '../images/img.jpg',
            'C:/My Documents/forkcms/frontend/core/layout/css',
            'C:/My Documents/forkcms/frontend/cache/minified_css',
            '../../core/layout/images/img.jpg',
        );

        // https://github.com/forkcms/forkcms/issues/1186
        $tests[] = array(
            '../images/img.jpg',
            '/Users/mathias/Documents/— Projecten/PROJECT_NAAM/Web/src/Backend/Core/Layout/Css/',
            '/Users/mathias/Documents/— Projecten/PROJECT_NAAM/Web/src/Backend/Cache/MinifiedCss/',
            '../../Core/Layout/images/img.jpg',
        );

        // https://github.com/matthiasmullie/path-converter/issues/1
        $tests[] = array(
            'image.jpg',
            '/var/www/mysite.com/assets/some_random_folder_name/',
            '/var/www/mysite.com/assets/some_other_random_folder_name/',
            '../some_random_folder_name/image.jpg',
        );

        return $tests;
    }
}
