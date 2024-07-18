<?php

declare(strict_types=1);

namespace PrInSt\ValidatorPolishPesel\Tests\Unit\Abstraction;

use JsonException;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;
use PrInSt\ValidatorPolishPesel\Abstraction\AbstractNumber;
use PrInSt\ValidatorPolishPesel\Tests\Feature\AbstractClassObjects\AbstractNumberTestObject;
use ReflectionClass;
use ReflectionException;
use Throwable;
use function json_encode;
use const JSON_THROW_ON_ERROR;

/**
 * AbstractNumber class test
 */
#[CoversMethod(AbstractNumber::class, 'getNumber')]
#[CoversMethod(AbstractNumber::class, 'setNumber')]
#[CoversMethod(AbstractNumber::class, '__toString')]
#[CoversMethod(AbstractNumber::class, 'jsonSerialize')]
class AbstractNumberTest extends TestCase
{
    /**
     * @param string $value
     *
     * @return void
     * @throws ReflectionException
     */
    #[DataProvider('dataProvider_testSetGetNumber')]
    public function testSetGetNumber(string $value): void
    {
        $abstractNumber = new AbstractNumberTestObject();
        $reflectionClass = new ReflectionClass($abstractNumber);
        $setNumberMethod = $reflectionClass->getMethod('setNumber');

        $setResult = $setNumberMethod->invoke($abstractNumber, $value);
        $getResult = $abstractNumber->getNumber();

        self::assertInstanceOf(AbstractNumber::class, $setResult);
        self::assertIsString($getResult);
        self::assertSame($value, $getResult);
    }

    /**
     * @return array<string, string[]>
     */
    public static function dataProvider_testSetGetNumber(): array
    {
        $values = [
            '',
            'a',
            '1',
            'abc',
            '123',
            'abcde',
            '12345',
            'abcdefg',
            '1234567',
            'abcdefghi',
            '123456789',
            'abcdefghijk',
            '12345678901',
            ' abcdefghijk ',
            ' 12345678901 ',
            'abcdefghijklm',
            '1234567890123',
            'abc def ghi jkl m',
            '1 234 567 890 123',
        ];

        $dataSet = [];
        foreach ($values as $value) {
            $dataSet["value: $value"] = [$value];
        }

        return $dataSet;
    }

    /**
     * @param string $value
     *
     * @return void
     * @throws ReflectionException
     */
    #[DataProvider('dataProvider_testSetGetNumber')]
    #[Depends('testSetGetNumber')]
    public function test__toString(string $value): void
    {
        $abstractNumber = new AbstractNumberTestObject();
        $reflectionClass = new ReflectionClass($abstractNumber);
        $setNumberMethod = $reflectionClass->getMethod('setNumber');
        $setNumberMethod->invoke($abstractNumber, $value);

        try {
            $result = (string) $abstractNumber;
        } catch (Throwable $exception) {
            // nothing here
        }

        self::assertNull($exception ?? null);
        self::assertIsString($result);
        self::assertSame($value, $result);
    }

    /**
     * @param string $value
     *
     * @return void
     * @throws JsonException
     * @throws ReflectionException
     */
    #[DataProvider('dataProvider_testSetGetNumber')]
    #[Depends('testSetGetNumber')]
    public function testJsonSerialize(string $value): void
    {
        $abstractNumber = new AbstractNumberTestObject();
        $result = $exception = null;

        $reflectionClass = new ReflectionClass($abstractNumber);
        $setNumberMethod = $reflectionClass->getMethod('setNumber');
        $setNumberMethod->invoke($abstractNumber, $value);

        $jsonValue = json_encode($value, JSON_THROW_ON_ERROR);

        try {
            $result = json_encode($abstractNumber, JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            // nothing here
        }

        self::assertNull($exception);
        self::assertIsString($result);
        self::assertJson($result);
        self::assertSame($jsonValue, $result);
    }
}
