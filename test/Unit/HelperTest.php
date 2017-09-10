<?php

declare(strict_types=1);

/**
 * Copyright (c) 2017 Andreas Möller.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @link https://github.com/localheinz/test-util
 */

namespace Localheinz\Test\Util\Test\Unit;

use Faker\Factory;
use Faker\Generator;
use Faker\Provider;
use Localheinz\Test\Util\Helper;
use PHPUnit\Framework;

final class HelperTest extends Framework\TestCase
{
    use Helper;

    public function testFakerWithoutLocaleReturnsFakerWithDefaultLocale()
    {
        $faker = $this->faker();

        $this->assertHasOnlyProvidersWithLocale(Factory::DEFAULT_LOCALE, $faker);
    }

    /**
     * @dataProvider providerLocale
     *
     * @param string $locale
     */
    public function testFakerWithLocaleReturnsFakerWithSpecifiedLocale(string $locale)
    {
        $faker = $this->faker($locale);

        $this->assertHasOnlyProvidersWithLocale($locale, $faker);
    }

    /**
     * @dataProvider providerLocale
     *
     * @param string $locale
     */
    public function testFakerReturnsSameFaker(string $locale)
    {
        $faker = $this->faker($locale);

        $this->assertSame($faker, $this->faker($locale));
    }

    public function providerLocale(): \Generator
    {
        /**
         * Note that \Faker\Factory::getProviderClassname() will fall back to using the default locale if it cannot find
         * a localized provider class name for one of the default providers - that's why the selection of locales here
         * is a bit limited.
         *
         * @see \Faker\Factory::$defaultProviders
         * @see \Faker\Factory::getProviderClassname()
         */
        $locales = [
            'de_DE',
            'en_US',
            'fr_FR',
        ];

        foreach ($locales as $locale) {
            yield $locale => [
                $locale,
            ];
        }
    }

    /**
     * @dataProvider providerNotAClass
     *
     * @param string $className
     */
    public function testAssertClassExistsFailsWhenClassIsNotAClass(string $className)
    {
        $this->expectException(Framework\AssertionFailedError::class);
        $this->expectExceptionMessage(\sprintf(
            'Failed to assert that a class "%s" exists.',
            $className
        ));

        $this->assertClassExists($className);
    }

    public function providerNotAClass(): \Generator
    {
        $classNames = [
            'class-non-existent' => __NAMESPACE__ . '\Fixture\NonExistentClass',
            'interface' => Fixture\ExampleInterface::class,
            'trait' => Fixture\ExampleTrait::class,
        ];

        foreach ($classNames as $className) {
            yield [
                $className,
            ];
        }
    }

    public function testAssertClassExistsSucceedsWhenClassExists()
    {
        $className = Fixture\ExampleClass::class;

        $this->assertClassExists($className);
    }

    /**
     * @dataProvider providerNotAClass
     *
     * @param string $parentClassName
     */
    public function testAssertClassExtendsFailsWhenParentClassIsNotAClass(string $parentClassName)
    {
        $className = Fixture\ExampleClass::class;

        $this->expectException(Framework\AssertionFailedError::class);
        $this->expectExceptionMessage(\sprintf(
            'Failed to assert that a class "%s" exists.',
            $parentClassName
        ));

        $this->assertClassExtends(
            $parentClassName,
            $className
        );
    }

    /**
     * @dataProvider providerNotAClass
     *
     * @param string $className
     */
    public function testAssertClassExtendsFailsWhenClassIsNotAClass(string $className)
    {
        $parentClassName = Fixture\AbstractClass::class;

        $this->expectException(Framework\AssertionFailedError::class);
        $this->expectExceptionMessage(\sprintf(
            'Failed to assert that a class "%s" exists.',
            $className
        ));

        $this->assertClassExtends(
            $parentClassName,
            $className
        );
    }

    public function testClassExtendsFailsWhenClassDoesNotExtendParentClass()
    {
        $parentClassName = Fixture\AbstractClass::class;
        $className = Fixture\ClassNotExtendingParentClass::class;

        $this->expectException(Framework\AssertionFailedError::class);
        $this->expectExceptionMessage(\sprintf(
            'Failed to assert that class "%s" extends "%s".',
            $className,
            $parentClassName
        ));

        $this->assertClassExtends(
            $parentClassName,
            $className
        );
    }

    public function testAssertClassExtendsSucceedsWhenClassExtendsParentClass()
    {
        $parentClassName = Fixture\AbstractClass::class;
        $className = Fixture\ClassExtendingParentClass::class;

        $this->assertClassExtends(
            $parentClassName,
            $className
        );
    }

    /**
     * @dataProvider providerNotAnInterface
     *
     * @param string $interfaceName
     */
    public function testAssertClassImplementsInterfaceFailsWhenInterfaceIsNotAnInterface(string $interfaceName)
    {
        $className = Fixture\ExampleClass::class;

        $this->expectException(Framework\AssertionFailedError::class);
        $this->expectExceptionMessage(\sprintf(
            'Failed to assert that an interface "%s" exists.',
            $interfaceName
        ));

        $this->assertClassImplementsInterface(
            $interfaceName,
            $className
        );
    }

    /**
     * @dataProvider providerNotAClass
     *
     * @param string $className
     */
    public function testAssertClassImplementsInterfaceFailsWhenClassIsNotAClass(string $className)
    {
        $interfaceName = Fixture\ExampleInterface::class;

        $this->expectException(Framework\AssertionFailedError::class);
        $this->expectExceptionMessage(\sprintf(
            'Failed to assert that a class "%s" exists.',
            $className
        ));

        $this->assertClassImplementsInterface(
            $interfaceName,
            $className
        );
    }

    public function testAssertClassImplementsInterfaceFailsWhenClassDoesNotImplementInterface()
    {
        $interfaceName = Fixture\ExampleInterface::class;
        $className = Fixture\ClassNotImplementingInterface::class;

        $this->expectException(Framework\AssertionFailedError::class);
        $this->expectExceptionMessage(\sprintf(
            'Failed to assert that class "%s" implements interface "%s".',
            $className,
            $interfaceName
        ));

        $this->assertClassImplementsInterface(
            $interfaceName,
            $className
        );
    }

    public function testAssertClassImplementsInterfaceSucceedsWhenClassImplementsInterface()
    {
        $interfaceName = Fixture\ExampleInterface::class;
        $className = Fixture\ClassImplementingInterface::class;

        $this->assertClassImplementsInterface(
            $interfaceName,
            $className
        );
    }

    /**
     * @dataProvider providerNotAClass
     *
     * @param string $className
     */
    public function testAssertClassIsAbstractOrFinalFailsWhenClassIsNotAClass(string $className)
    {
        $this->expectException(Framework\AssertionFailedError::class);
        $this->expectExceptionMessage(\sprintf(
            'Failed to assert that a class "%s" exists.',
            $className
        ));

        $this->assertClassIsAbstractOrFinal($className);
    }

    public function testAssertClassIsAbstractOrFinalFailsWhenClassIsNeitherAbstractNorFinal()
    {
        $className = Fixture\NonFinalClass::class;

        $this->expectException(Framework\AssertionFailedError::class);
        $this->expectExceptionMessage(\sprintf(
            'Failed to assert that class "%s" is abstract or final.',
            $className
        ));

        $this->assertClassIsAbstractOrFinal($className);
    }

    public function providerClassAbstractOrFinal(): \Generator
    {
        $classNames = [
            'class-abstract' => Fixture\AbstractClass::class,
            'class-final' => Fixture\FinalClass::class,
        ];

        foreach ($classNames as $key => $className) {
            yield $key => [
                $className,
            ];
        }
    }

    /**
     * @dataProvider providerNotATrait
     *
     * @param string $traitName
     */
    public function testAssertClassUsesTraitFailsWhenTraitIsNotATrait(string $traitName)
    {
        $className = Fixture\ExampleClass::class;

        $this->expectException(Framework\AssertionFailedError::class);
        $this->expectExceptionMessage(\sprintf(
            'Failed to assert that a trait "%s" exists.',
            $traitName
        ));

        $this->assertClassUsesTrait(
            $traitName,
            $className
        );
    }

    /**
     * @dataProvider providerNotAClass
     *
     * @param string $className
     */
    public function testAssertClassUsesTraitFailsWhenClassIsNotAClass(string $className)
    {
        $traitName = Fixture\ExampleTrait::class;

        $this->expectException(Framework\AssertionFailedError::class);
        $this->expectExceptionMessage(\sprintf(
            'Failed to assert that a class "%s" exists.',
            $className
        ));

        $this->assertClassUsesTrait(
            $traitName,
            $className
        );
    }

    public function testAssertClassUsesTraitFailsWhenClassDoesNotUseTrait()
    {
        $traitName = Fixture\ExampleTrait::class;
        $className = Fixture\ClassNotUsingTrait::class;

        $this->expectException(Framework\AssertionFailedError::class);
        $this->expectExceptionMessage(\sprintf(
            'Failed to assert that class "%s" uses trait "%s".',
            $className,
            $traitName
        ));

        $this->assertClassUsesTrait(
            $traitName,
            $className
        );
    }

    public function testAssertClassUsesTraitSucceedsWhenClassUsesTrait()
    {
        $traitName = Fixture\ExampleTrait::class;
        $className = Fixture\ClassUsingTrait::class;

        $this->assertClassUsesTrait(
            $traitName,
            $className
        );
    }

    /**
     * @dataProvider providerNotAnInterface
     *
     * @param string $interfaceName
     */
    public function testAssertInterfaceExistsFailsWhenInterfaceIsNotAnInterface(string $interfaceName)
    {
        $this->expectException(Framework\AssertionFailedError::class);
        $this->expectExceptionMessage(\sprintf(
            'Failed to assert that an interface "%s" exists.',
            $interfaceName
        ));

        $this->assertInterfaceExists($interfaceName);
    }

    public function providerNotAnInterface(): \Generator
    {
        $interfaceNames = [
            'class' => Fixture\ExampleClass::class,
            'interface-non-existent' => __NAMESPACE__ . '\Fixture\NonExistentInterface',
            'trait' => Fixture\ExampleTrait::class,
        ];

        foreach ($interfaceNames as $key => $interfaceName) {
            yield $key => [
                $interfaceName,
            ];
        }
    }

    public function testAssertInterfaceExistsSucceedsWhenInterfaceExists()
    {
        $interfaceName = Fixture\ExampleInterface::class;

        $this->assertInterfaceExists($interfaceName);
    }

    /**
     * @dataProvider providerNotAnInterface
     *
     * @param string $parentInterfaceName
     */
    public function testInterfaceExtendsFailsWhenParentInterfaceIsNotAnInterface(string $parentInterfaceName)
    {
        $interfaceName = Fixture\ExampleInterface::class;

        $this->expectException(Framework\AssertionFailedError::class);
        $this->expectExceptionMessage(\sprintf(
            'Failed to assert that an interface "%s" exists.',
            $parentInterfaceName
        ));

        $this->assertInterfaceExtends(
            $parentInterfaceName,
            $interfaceName
        );
    }

    /**
     * @dataProvider providerNotAnInterface
     *
     * @param string $interfaceName
     */
    public function testAssertInterfaceExtendsFailsWhenInterfaceIsNotAnInterface(string $interfaceName)
    {
        $parentInterfaceName = Fixture\ExampleInterface::class;

        $this->expectException(Framework\AssertionFailedError::class);
        $this->expectExceptionMessage(\sprintf(
            'Failed to assert that an interface "%s" exists.',
            $interfaceName
        ));

        $this->assertInterfaceExtends(
            $parentInterfaceName,
            $interfaceName
        );
    }

    public function testAssertInterfaceExtendsFailsWhenInterfaceDoesNotExtendParentInterface()
    {
        $parentInterfaceName = Fixture\ExampleInterface::class;
        $interfaceName = Fixture\InterfaceNotExtendingParentInterface::class;

        $this->expectException(Framework\AssertionFailedError::class);
        $this->expectExceptionMessage(\sprintf(
            'Failed to assert that interface "%s" extends "%s".',
            $interfaceName,
            $parentInterfaceName
        ));

        $this->assertInterfaceExtends(
            $parentInterfaceName,
            $interfaceName
        );
    }

    public function testAssertInterfaceExtendsSucceedsWhenInterfaceExtendsParentInterface()
    {
        $parentInterfaceName = Fixture\ExampleInterface::class;
        $interfaceName = Fixture\InterfaceExtendingParentInterface::class;

        $this->assertInterfaceExtends(
            $parentInterfaceName,
            $interfaceName
        );
    }

    /**
     * @dataProvider providerNotATrait
     *
     * @param string $traitName
     */
    public function testAssertTraitExistsFailsWhenTraitIsNotATrait(string $traitName)
    {
        $this->expectException(Framework\AssertionFailedError::class);
        $this->expectExceptionMessage(\sprintf(
            'Failed to assert that a trait "%s" exists.',
            $traitName
        ));

        $this->assertTraitExists($traitName);
    }

    public function providerNotATrait(): \Generator
    {
        $traitNames = [
            'class' => Fixture\ExampleClass::class,
            'interface' => Fixture\ExampleInterface::class,
            'trait-non-existent' => __NAMESPACE__ . '\Fixture\NonExistentTrait',
        ];

        foreach ($traitNames as $key => $traitName) {
            yield $key => [
                $traitName,
            ];
        }
    }

    public function testAssertTraitExistsSucceedsWhenTraitExists()
    {
        $traitName = Fixture\ExampleTrait::class;

        $this->assertTraitExists($traitName);
    }

    private function assertHasOnlyProvidersWithLocale(string $locale, Generator $faker)
    {
        $providerClasses = \array_map(function (Provider\Base $provider) {
            return \get_class($provider);
        }, $faker->getProviders());

        $providerLocales = \array_map(function (string $providerClass) {
            if (0 === \preg_match('/^Faker\\\\Provider\\\\(?P<locale>[a-z]{2}_[A-Z]{2})\\\\/', $providerClass, $matches)) {
                return null;
            }

            return $matches['locale'];
        }, $providerClasses);

        $locales = \array_values(\array_unique(\array_filter($providerLocales)));

        $expected = [
            $locale,
        ];

        $this->assertEquals($expected, $locales);
    }
}
