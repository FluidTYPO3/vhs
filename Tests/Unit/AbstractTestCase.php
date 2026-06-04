<?php
namespace FluidTYPO3\Vhs\Tests\Unit;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Cache\Backend\TransientMemoryBackend;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\CMS\Core\Charset\CharsetConverter;
use TYPO3\CMS\Core\Charset\CharsetProvider;
use TYPO3\CMS\Core\Core\ApplicationContext;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Localization\Locale;
use TYPO3\CMS\Core\Localization\Locales;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use FluidTYPO3\Vhs\Tests\Fixtures\Classes\DummyLanguageService;
use FluidTYPO3\Vhs\Tests\Fixtures\Classes\DummyLanguageServiceFactory;
use TYPO3Fluid\Fluid\Core\Parser\Interceptor\Escape;

/**
 * AbstractTestCase
 */
abstract class AbstractTestCase extends TestCase
{
    private array $singletonInstancesBackup = [];
    private array $classAliasBackup = [];
    protected array $singletonInstances = [];

    /**
     * @return void
     */
    protected function setUp(): void
    {
        if (!defined('LF')) {
            define('LF', PHP_EOL);
        }
        if (!defined('TYPO3_MODE')) {
            define('TYPO3_MODE', 'FE');
        }
        if (!defined('TYPO3_REQUESTTYPE')) {
            define('TYPO3_REQUESTTYPE', 1);
        }
        if (!defined('TYPO3_REQUESTTYPE_FE')) {
            define('TYPO3_REQUESTTYPE_FE', 1);
        }
        if (!defined('TYPO3_REQUESTTYPE_CLI')) {
            define('TYPO3_REQUESTTYPE_CLI', 3);
        }
        if (!defined('TYPO3_version')) {
            // phpcs:disable
            define('TYPO3_version', '9.5.0');
            // phpcs:enable
        }

        $pwd = realpath(__DIR__ . '/../../');
        if (!is_string($pwd)) {
            throw new \RuntimeException('Unable to resolve test project path.', 1780000278);
        }

        Environment::initialize(
            new ApplicationContext('Development'),
            true,
            false,
            $pwd,
            $pwd . '/public',
            $pwd . '/var',
            $pwd . '/typo3conf',
            $pwd . '/index.php',
            'linux',
        );

        $GLOBALS['EXEC_TIME'] = time();
        if (!isset($GLOBALS['LANG'])) {
            $GLOBALS['LANG'] = (object) [
                'csConvObj' => version_compare(VersionNumberUtility::getCurrentTypo3Version(), '14.0', '>=')
                    ? new CharsetConverter(new CharsetProvider())
                    : new CharsetConverter()
            ];
        }
        $GLOBALS['TYPO3_CONF_VARS']['BE']['versionNumberInFilename'] = false;
        $GLOBALS['TYPO3_CONF_VARS']['FE']['versionNumberInFilename'] = false;
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['preProcessors'] = [];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['interceptors'] = [
            Escape::class
        ];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['fluid_template'] = [
            'frontend' => VariableFrontend::class,
            'backend' => TransientMemoryBackend::class,
        ];

        $this->singletonInstancesBackup = GeneralUtility::getSingletonInstances();

        foreach ($this->singletonInstances as $className => $instance) {
            if (!is_string($className) || (!class_exists($className) && !interface_exists($className))) {
                continue;
            }
            GeneralUtility::setSingletonInstance($className, $instance);
        }
    }

    protected function setClassAlias(string $className, string $implementationClassName): void
    {
        if (!array_key_exists($className, $this->classAliasBackup)) {
            $this->classAliasBackup[$className] = $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][$className]['className']
                ?? null;
        }

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][$className] = [
            'className' => $implementationClassName,
        ];
        GeneralUtility::flushInternalRuntimeCaches();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        GeneralUtility::resetSingletonInstances($this->singletonInstancesBackup);
        GeneralUtility::purgeInstances();

        foreach ($this->classAliasBackup as $className => $previousAlias) {
            if ($previousAlias === null) {
                unset($GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][$className]);
            } else {
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][$className] = ['className' => $previousAlias];
            }
        }
        $this->classAliasBackup = [];
        DummyLanguageServiceFactory::reset();
        GeneralUtility::flushInternalRuntimeCaches();

        unset($GLOBALS['TSFE']);
    }

    /**
     * Helper function to call protected or private methods
     *
     * @param object $object The object to be invoked
     * @param string $name the name of the method to call
     * @param mixed $arguments
     * @return mixed
     */
    protected function callInaccessibleMethod($object, $name, ...$arguments)
    {
        $reflectionObject = new \ReflectionObject($object);
        $reflectionMethod = $reflectionObject->getMethod($name);
        $reflectionMethod->setAccessible(true);
        return $reflectionMethod->invokeArgs($object, $arguments);
    }

    /**
     * @param object $object
     * @param string $propertyName
     * @param mixed $value
     * @return void
     */
    protected function setInaccessiblePropertyValue(object $object, string $propertyName, $value): void
    {
        $reflectionProperty = new \ReflectionProperty($object, $propertyName);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
    }

    /**
     * @param object $object
     * @param string $propertyName
     * @return mixed
     */
    protected function getInaccessiblePropertyValue(object $object, string $propertyName)
    {
        $reflectionProperty = new \ReflectionProperty($object, $propertyName);
        $reflectionProperty->setAccessible(true);
        return $reflectionProperty->getValue($object);
    }

    /**
     * @param string $propertyName
     * @param mixed $value
     * @param mixed $expectedValue
     * @param bool $expectsChaining
     * @return void
     */
    protected function assertGetterAndSetterWorks(
        string $propertyName,
        mixed $value,
        mixed $expectedValue = null,
        bool $expectsChaining = false
    ): void {
        $instance = $this->createInstance();
        $setter = 'set' . ucfirst($propertyName);
        $getter = 'get' . ucfirst($propertyName);
        $chained = $instance->$setter($value);
        $expectedValue = $expectedValue ?? $value;
        if (true === $expectsChaining) {
            $this->assertSame($instance, $chained);
        } else {
            $this->assertNull($chained);
        }
        $this->assertEquals($expectedValue, $instance->$getter());
    }

    /**
     * @param mixed $value
     */
    protected function assertIsValidAndWorkingFormObject($value): void
    {
        if (!is_object($value) || !method_exists($value, 'build') || !method_exists($value, 'getFields')) {
            self::fail('Flux form fixture does not expose the expected API.');
        }
        $build = \Closure::fromCallable([$value, 'build']);
        $getFields = \Closure::fromCallable([$value, 'getFields']);
        $structure = $build();
        $this->assertIsArray($structure);
        // scan for and attempt building of closures in structure
        $customFieldClassName = 'FluidTYPO3\\Flux\\Form\\Field\\Custom';
        foreach ($getFields() as $field) {
            if (is_object($field) && is_a($field, $customFieldClassName)) {
                if (!method_exists($field, 'getClosure') || !method_exists($field, 'getArguments')) {
                    self::fail('Flux custom field fixture does not expose the expected API.');
                }
                $getClosure = \Closure::fromCallable([$field, 'getClosure']);
                $getArguments = \Closure::fromCallable([$field, 'getArguments']);
                $closure = $getClosure();
                $output = $closure($getArguments());
                $this->assertNotEmpty($output);
            }
        }
    }

    /**
     * @param mixed $value
     */
    protected function assertIsValidAndWorkingGridObject($value): void
    {
        if (!is_object($value) || !method_exists($value, 'build')) {
            self::fail('Flux grid fixture does not expose the expected API.');
        }
        $build = \Closure::fromCallable([$value, 'build']);
        $structure = $build();
        $this->assertIsArray($structure);
    }

    /**
     * @param string $shorthandTemplatePath
     * @return string
     */
    protected function getAbsoluteFixtureTemplatePathAndFilename($shorthandTemplatePath)
    {
        $path = realpath(str_replace('EXT:vhs/', './', $shorthandTemplatePath));
        if (!is_string($path)) {
            throw new \RuntimeException('Unable to resolve fixture template path.', 1780000279);
        }
        return $path;
    }

    /**
     * @return string
     */
    protected function createInstanceClassName()
    {
        return str_replace('Tests\\Unit\\', '', substr(get_class($this), 0, -4));
    }

    /**
     * @return object
     */
    protected function createInstance()
    {
        $instanceClassName = $this->createInstanceClassName();
        return new $instanceClassName();
    }

    protected function mockForLocalizationUtilityCalls(array $returnValueMap): void
    {
        $languageService = new DummyLanguageService($returnValueMap);
        DummyLanguageServiceFactory::setService($languageService);
        $this->setClassAlias(LanguageServiceFactory::class, DummyLanguageServiceFactory::class);

        if (class_exists(Locales::class)) {
            if (method_exists(Locales::class, 'createLocaleFromRequest')) {
                $methods = ['createLocaleFromRequest'];
            } else {
                $methods = ['getLanguages'];
            }
            $locale = $this->getMockBuilder(Locale::class)->disableOriginalConstructor()->getMock();
            $locales = $this->getMockBuilder(Locales::class)
                ->onlyMethods($methods)
                ->disableOriginalConstructor()
                ->getMock();
            if (method_exists(Locales::class, 'createLocaleFromRequest')) {
                $locales->method('createLocaleFromRequest')->willReturn($locale);
            }

            $this->singletonInstances[Locales::class] = $locales;
        }

        $GLOBALS['LANG'] = $languageService;
    }
}
