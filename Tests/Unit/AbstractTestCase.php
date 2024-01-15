<?php
namespace FluidTYPO3\Vhs\Tests\Unit;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Flux\Form;
use FluidTYPO3\Flux\Form\Field\Custom;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Cache\Backend\TransientMemoryBackend;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\CMS\Core\Charset\CharsetConverter;
use TYPO3\CMS\Core\Core\ApplicationContext;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Localization\Locale;
use TYPO3\CMS\Core\Localization\Locales;
use TYPO3\CMS\Core\Localization\LocalizationFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3Fluid\Fluid\Core\Parser\Interceptor\Escape;

/**
 * AbstractTestCase
 */
abstract class AbstractTestCase extends TestCase
{
    private array $singletonInstancesBackup = [];
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
            define('TYPO3_version', '9.5.0');
        }

        $pwd = realpath(__DIR__ . '/../../');

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
        $GLOBALS['LANG'] = (object) ['csConvObj' => new CharsetConverter()];
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
            GeneralUtility::setSingletonInstance($className, $instance);
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        GeneralUtility::resetSingletonInstances($this->singletonInstancesBackup);
        GeneralUtility::purgeInstances();

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
     * @param mixed $expectsChaining
     * @return void
     */
    protected function assertGetterAndSetterWorks($propertyName, $value, $expectedValue = null, $expectsChaining = false)
    {
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
     * Asserts that a variable is of type array.
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     *
     * @psalm-assert array $actual
     */
    public static function assertIsArray($actual, string $message = ''): void
    {
        $constraint = new IsType(IsType::TYPE_ARRAY);
        static::assertThat(
            $actual,
            $constraint,
            $message
        );
    }

    /**
     * @param mixed $value
     * @return void
     */
    protected function assertIsInteger($value)
    {
        $isIntegerConstraint = new IsType(IsType::TYPE_INT);
        $this->assertThat($value, $isIntegerConstraint);
    }

    /**
     * @param mixed $value
     * @return void
     */
    protected function assertIsBoolean($value)
    {
        $isBooleanConstraint = new IsType(IsType::TYPE_BOOL);
        $this->assertThat($value, $isBooleanConstraint);
    }

    /**
     * @param mixed $value
     */
    protected function assertIsValidAndWorkingFormObject($value)
    {
        $this->assertInstanceOf(Form::class, $value);
        $this->assertInstanceOf(Form\FormInterface::class, $value);
        $this->assertInstanceOf(Form\ContainerInterface::class, $value);
        /** @var Form $value */
        $structure = $value->build();
        $this->assertIsArray($structure);
        // scan for and attempt building of closures in structure
        foreach ($value->getFields() as $field) {
            if (true === $field instanceof Custom) {
                $closure = $field->getClosure();
                $output = $closure($field->getArguments());
                $this->assertNotEmpty($output);
            }
        }
    }

    /**
     * @param mixed $value
     */
    protected function assertIsValidAndWorkingGridObject($value)
    {
        $this->assertInstanceOf(Form\Container\Grid::class, $value);
        $this->assertInstanceOf(Form\ContainerInterface::class, $value);
        /** @var Form $value */
        $structure = $value->build();
        $this->assertIsArray($structure);
    }

    /**
     * @param string $shorthandTemplatePath
     * @return string
     */
    protected function getAbsoluteFixtureTemplatePathAndFilename($shorthandTemplatePath)
    {
        return realpath(str_replace('EXT:vhs/', './', $shorthandTemplatePath));
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

    protected function mockForLocalizationUtilityCalls(array $returnValeMap): void
    {
        $languageService = $this->getMockBuilder(LanguageService::class)->disableOriginalConstructor()->getMock();

        $this->singletonInstances[LocalizationFactory::class] = $this->getMockBuilder(LocalizationFactory::class)
            ->setMethods(['getParsedData'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->singletonInstances[LocalizationFactory::class]->method('getParsedData')->willReturn([]);

        if (class_exists(LanguageServiceFactory::class)) {
            $languageServiceFactory = $this->getMockBuilder(LanguageServiceFactory::class)
                ->onlyMethods(['create'])
                ->disableOriginalConstructor()
                ->getMock();
            $languageServiceFactory->method('create')->willReturn($languageService);

            GeneralUtility::addInstance(LanguageServiceFactory::class, $languageServiceFactory);
        }

        if (class_exists(Locales::class)) {
            $locale = $this->getMockBuilder(Locale::class)->disableOriginalConstructor()->getMock();
            $locales = $this->getMockBuilder(Locales::class)
                ->onlyMethods(['createLocaleFromRequest'])
                ->disableOriginalConstructor()
                ->getMock();
            $locales->method('createLocaleFromRequest')->willReturn($locale);

            $this->singletonInstances[Locales::class] = $locales;
        }

        $GLOBALS['LANG'] = $languageService;
    }
}
