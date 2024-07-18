<?php

declare(strict_types=1);

namespace PrInSt\ValidatorPolishPesel\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\DependsExternal;
use PrInSt\ValidatorPolishPesel\Enum\Gender;
use PrInSt\ValidatorPolishPesel\Exception\InvalidBirthdateException;
use PrInSt\ValidatorPolishPesel\Exception\InvalidBirthdatePatternException;
use PrInSt\ValidatorPolishPesel\Exception\InvalidFormatException;
use PrInSt\ValidatorPolishPesel\Exception\InvalidGenderException;
use PrInSt\ValidatorPolishPesel\Exception\InvalidGenderPatternException;
use PrInSt\ValidatorPolishPesel\Exception\InvalidPeselException;
use PrInSt\ValidatorPolishPesel\Exception\InvalidWeightsException;
use PrInSt\ValidatorPolishPesel\Pesel;
use PHPUnit\Framework\TestCase;
use PrInSt\ValidatorPolishPesel\Tests\Datasets\GoodPeselDataSet;
use PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\Pesel\IfCase;
use PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\Pesel\TryCase;
use PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\Pesel\TryGenderCase;
use PrInSt\ValidatorPolishPesel\Tests\Unit\Abstraction\AbstractNumberTest;
use PrInSt\ValidatorPolishPesel\Tests\Unit\Abstraction\AbstractValidatorTest;
use Random\RandomException;
use Throwable;
use function random_int;
use function substr;

/**
 * Pesel tests
 */
class PeselTest extends TestCase
{
    private static array $data = [];


    /**
     * @param string $value
     *
     * @return void
     */
    #[DataProvider('dataProvider_test__construct')]
    #[DependsExternal(AbstractNumberTest::class, 'testSetGetNumber')]
    public function test__construct(string $value): void
    {
        $result = $exception = null;

        try {
            $result = new Pesel($value);
        } catch (Throwable $exception) {
            // nothing here
        }

        self::assertNull($exception);
        self::assertInstanceOf(Pesel::class, $result);
        self::assertSame($value, $result->getNumber());
    }

    /**
     * @return array<non-empty-string, string[]>
     */
    public static function dataProvider_test__construct(): array
    {
        $data = [];

        foreach (
            [
                '',
                ' ',
                '  ',
                '   ',
                '    ',
                '     ',
                '      ',
                '       ',
                '        ',
                '         ',
                '          ',
                '           ',
                '            ',
                '1',
                '12',
                '123',
                '1234',
                '12345',
                '123456',
                '1234567',
                '12345678',
                '123456789',
                '1234567890',
                '12345678901',
                '123456789012',
                'a',
                'ab',
                'abc',
                'abcd',
                'abcde',
                'abcdef',
                'abcdefg',
                'abcdefgh',
                'abcdefghi',
                'abcdefghij',
                'abcdefghijk',
                'abcdefghijkl',
                ' 123',
                '123 ',
                '1 23',
                '12 3',
                '1 2 3',
                '\\',
                '*',
                '0x12',
            ] as $value
        ) {
            $data["pesel: '$value'"] = [$value];
        }

        return $data;
    }

    /**
     * @return void
     */
    #[Depends('test__construct')]
    #[DependsExternal(AbstractValidatorTest::class, 'testIsValidGender')]
    public function testIsFemale(): void
    {
        foreach (self::dataSet_testIsFemale() as $case) {
            $result = $exception = null;

            try {
                $result
                    = (new Pesel($case->pesel))
                    ->isFemale()
                ;
            } catch(Throwable $exception) {
                // nothing here
            }

            self::assertNull($exception, $case->info);
            self::assertIsBool($result, $case->info);
            self::assertSame($case->expected, $result, $case->info);
        }

        foreach (self::dataSet_testIsMale() as $case) {
            $result = $exception = null;

            try {
                $result
                    = (new Pesel($case->pesel))
                    ->isFemale()
                ;
            } catch(Throwable $exception) {
                // nothing here
            }

            self::assertNull($exception, $case->info);
            self::assertIsBool($result, $case->info);
            self::assertNotSame($case->expected, $result, $case->info);
        }
    }

    /**
     * @return IfCase[]
     */
    public static function dataSet_testIsFemale(): array
    {
        if (!empty(self::$data[__FUNCTION__])) {
            return self::$data[__FUNCTION__];
        }

        foreach (GoodPeselDataSet::FEMALE_1800 as $pesel) {
            self::$data[__FUNCTION__][] = new IfCase(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_1800'
            );
        }

        foreach (GoodPeselDataSet::FEMALE_1900 as $pesel) {
            self::$data[__FUNCTION__][] = new IfCase(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_1900'
            );
        }

        foreach (GoodPeselDataSet::FEMALE_2000 as $pesel) {
            self::$data[__FUNCTION__][] = new IfCase(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_2000'
            );
        }

        foreach (GoodPeselDataSet::FEMALE_2100 as $pesel) {
            self::$data[__FUNCTION__][] = new IfCase(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_2100'
            );
        }

        foreach (GoodPeselDataSet::FEMALE_2200 as $pesel) {
            self::$data[__FUNCTION__][] = new IfCase(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_2200'
            );
        }

        return self::$data[__FUNCTION__];
    }

    /**
     * @return void
     */
    #[Depends('test__construct')]
    #[DependsExternal(AbstractValidatorTest::class, 'testIsValidGender')]
    public function testIsMale(): void
    {
        foreach (self::dataSet_testIsFemale() as $case) {
            $result = $exception = null;

            try {
                $result
                    = (new Pesel($case->pesel))
                    ->isMale()
                ;
            } catch(Throwable $exception) {
                // nothing here
            }

            self::assertNull($exception, $case->info);
            self::assertIsBool($result, $case->info);
            self::assertNotSame($case->expected, $result, $case->info);
        }

        foreach (self::dataSet_testIsMale() as $case) {
            $result = $exception = null;

            try {
                $result
                    = (new Pesel($case->pesel))
                    ->isMale()
                ;
            } catch(Throwable $exception) {
                // nothing here
            }

            self::assertNull($exception, $case->info);
            self::assertIsBool($result, $case->info);
            self::assertSame($case->expected, $result, $case->info);
        }
    }

    /**
     * @return IfCase[]
     */
    public static function dataSet_testIsMale(): array
    {
        if (!empty(self::$data[__FUNCTION__])) {
            return self::$data[__FUNCTION__];
        }

        foreach (GoodPeselDataSet::MALE_1800 as $pesel) {
            self::$data[__FUNCTION__][] = new IfCase(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_1800'
            );
        }

        foreach (GoodPeselDataSet::MALE_1900 as $pesel) {
            self::$data[__FUNCTION__][] = new IfCase(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_1900'
            );
        }

        foreach (GoodPeselDataSet::MALE_2000 as $pesel) {
            self::$data[__FUNCTION__][] = new IfCase(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_2000'
            );
        }

        foreach (GoodPeselDataSet::MALE_2100 as $pesel) {
            self::$data[__FUNCTION__][] = new IfCase(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_2100'
            );
        }

        foreach (GoodPeselDataSet::MALE_2200 as $pesel) {
            self::$data[__FUNCTION__][] = new IfCase(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_2200'
            );
        }

        return self::$data[__FUNCTION__];
    }

    /**
     * @return void
     */
    #[Depends('test__construct')]
    #[DependsExternal(AbstractValidatorTest::class, 'testIsValidBirthdate')]
    public function testTryIsValidBirthdate(): void
    {
        foreach (self::dataSet_testTryIsValidBirthdate() as $case) {
            $result = $exception = null;

            try {
                $pesel = new Pesel($case->pesel);
                $result = $pesel->tryIsValidBirthdate();
            } catch (Throwable $exception) {
                // nothing here
            }

            if ($case->expected) {
                self::assertNotNull($exception, $case->info);
                self::assertIsObject($exception, $case->info);
                self::assertInstanceOf(InvalidPeselException::class, $exception, $case->info);
                self::assertInstanceOf(InvalidBirthdateException::class, $exception, $case->info);
                self::assertInstanceOf($case->expected, $exception, $case->info);

                self::assertNotNull($exception->getPesel(), $case->info);
                self::assertSame($pesel, $exception->getPesel(), $case->info);

                self::assertNull($result, $case->info);
            } else {
                self::assertNull($exception, $case->info);
                self::assertInstanceOf(Pesel::class, $result, $case->info);
            }
        }
    }

    /**
     * @return TryCase[]
     */
    public static function dataSet_testTryIsValidBirthdate(): array
    {
        $data = [];

        foreach (
            [
                '00000000000',
                '12345678901',
            ] as $value
        ) {
            $data[] = new TryCase(
                pesel   : $value,
                expected: InvalidBirthdatePatternException::class
            );
        }

        foreach (
            [
                1800 => 80,
                1900 => 0,
                2000 => 20,
                2100 => 40,
                2200 => 60,
            ] as $century => $monthAddition
        ) {
            for ($year = 0; $year < 100; $year++) {
                $yearFull = $century + $year;
                $yearPesel = sprintf('%02d', $year);

                // bad month (zero)
                $data[] = new TryCase(
                    pesel   : $yearPesel . sprintf('%02d01', $monthAddition),
                    expected: InvalidBirthdatePatternException::class
                );
                // END: bad month (zero)

                for ($month = 1; $month <= 12; $month++) {
                    $monthDays = cal_days_in_month(CAL_GREGORIAN, $month, $yearFull);
                    $monthPesel = sprintf('%02d', $month + $monthAddition);

                    // bad day (zero)
                    $data[] = new TryCase(
                        pesel   : $yearPesel . $monthPesel . '00',
                        expected: InvalidBirthdatePatternException::class
                    );
                    // END: bad day (zero)

                    for ($day = 1; $day <= $monthDays; $day++) {
                        $data[] = new TryCase(
                            pesel   : $yearPesel . $monthPesel . sprintf('%02d', $day),
                            expected: null
                        );
                    }

                    // bad day (greater than days in the month)
                    // may be InvalidBirthdateException or InvalidBirthdatePatternException
                    $data[] = new TryCase(
                        pesel   : $yearPesel . $monthPesel . sprintf('%02d', $monthDays + 1),
                        expected: InvalidBirthdateException::class
                    );
                    // END: bad day (greater than days in the month)

                    // bad day (day 33)
                    $data[] = new TryCase(
                        pesel   : $yearPesel . $monthPesel . '33',
                        expected: InvalidBirthdatePatternException::class
                    );
                    // END: bad day (greater than days in the month)
                }

                // bad month (13th)
                $data[] = new TryCase(
                    pesel   : $yearPesel . sprintf('%02d01', 13 + $monthAddition),
                    expected: InvalidBirthdatePatternException::class
                );
                // END: bad month (13th)
            }
        }

        foreach (self::dataSet_testIsFemale() as $case) {
            $data[] = new TryCase(
                pesel   : $case->pesel,
                expected: null
            );
        }

        foreach (self::dataSet_testIsMale() as $case) {
            $data[] = new TryCase(
                pesel   : $case->pesel,
                expected: null
            );
        }

        return $data;
    }

    /**
     * @return void
     */
    #[Depends('test__construct')]
    #[DependsExternal(AbstractValidatorTest::class, 'testIsValidGender')]
    public function testTryIsValidGender(): void
    {
        foreach (self::dataSet_testTryIsValidGender() as $case) {
            $result = $exception = null;

            try {
                $pesel = new Pesel($case->pesel);
                $result = $pesel->tryIsValidGender($case->forGender);
            } catch (Throwable $exception) {
                // nothing here
            }

            if ($case->expected) {
                self::assertNotNull($exception, $case->info);
                self::assertIsObject($exception, $case->info);
                self::assertInstanceOf(InvalidPeselException::class, $exception, $case->info);
                self::assertInstanceOf(InvalidGenderException::class, $exception, $case->info);
                self::assertInstanceOf($case->expected, $exception, $case->info);

                self::assertNotNull($exception->getPesel(), $case->info);
                self::assertSame($pesel, $exception->getPesel(), $case->info);

                self::assertNull($result, $case->info);
            } else {
                self::assertNull($exception, $case->info);
                self::assertInstanceOf(Pesel::class, $result, $case->info);
            }
        }
    }

    /**
     * @return TryGenderCase[]
     */
    public static function dataSet_testTryIsValidGender(): array
    {
        $data = [];

        foreach (
            [
                '123456789a',
                '123456789 ',
                '         b',
            ] as $value
        ) {
            $data[] = new TryGenderCase(
                pesel    : $value,
                forGender: null,
                expected : InvalidGenderPatternException::class
            );
            $data[] = new TryGenderCase(
                pesel    : $value,
                forGender: Gender::Female,
                expected : InvalidGenderPatternException::class
            );
            $data[] = new TryGenderCase(
                pesel    : $value,
                forGender: Gender::Male,
                expected : InvalidGenderPatternException::class
            );
        }

        foreach (['0', '2', '4', '6', '8'] as $value) {
            $data[] = new TryGenderCase(
                pesel    : '         ' . $value,
                forGender: null,
                expected : null
            );
            $data[] = new TryGenderCase(
                pesel    : '         ' . $value,
                forGender: Gender::Female,
                expected : null
            );
            $data[] = new TryGenderCase(
                pesel    : '         ' . $value,
                forGender: Gender::Male,
                expected : InvalidGenderException::class
            );
        }

        foreach (['1', '3', '5', '7', '9'] as $value) {
            $data[] = new TryGenderCase(
                pesel    : '         ' . $value,
                forGender: null,
                expected : null
            );
            $data[] = new TryGenderCase(
                pesel    : '         ' . $value,
                forGender: Gender::Male,
                expected : null
            );
            $data[] = new TryGenderCase(
                pesel    : '         ' . $value,
                forGender: Gender::Female,
                expected : InvalidGenderException::class
            );
        }

        foreach (self::dataSet_testIsFemale() as $case) {
            $data[] = new TryGenderCase(
                pesel     : $case->pesel,
                forGender : null,
                expected  : null
            );
            $data[] = new TryGenderCase(
                pesel     : $case->pesel,
                forGender : Gender::Female,
                expected  : null
            );
            $data[] = new TryGenderCase(
                pesel     : $case->pesel,
                forGender : Gender::Male,
                expected  : InvalidGenderException::class
            );
        }

        foreach (self::dataSet_testIsMale() as $case) {
            $data[] = new TryGenderCase(
                pesel     : $case->pesel,
                forGender : null,
                expected  : null
            );
            $data[] = new TryGenderCase(
                pesel     : $case->pesel,
                forGender : Gender::Male,
                expected  : null
            );
            $data[] = new TryGenderCase(
                pesel     : $case->pesel,
                forGender : Gender::Female,
                expected  : InvalidGenderException::class
            );
        }

        return $data;
    }

    /**
     * @return void
     */
    #[Depends('test__construct')]
    #[DependsExternal(AbstractValidatorTest::class, 'testIsValidFormat')]
    public function testTryIsValidFormat(): void
    {
        foreach (self::dataSet_testTryIsValidFormat() as $case) {
            $result = $exception = null;

            try {
                $pesel = new Pesel($case->pesel);
                $result = $pesel->tryIsValidFormat();
            } catch (Throwable $exception) {
                // nothing here
            }

            if ($case->expected) {
                self::assertNotNull($exception, $case->info);
                self::assertIsObject($exception, $case->info);
                self::assertInstanceOf(InvalidPeselException::class, $exception, $case->info);
                self::assertInstanceOf($case->expected, $exception, $case->info);

                self::assertNotNull($exception->getPesel(), $case->info);
                self::assertSame($pesel, $exception->getPesel(), $case->info);

                self::assertNull($result, $case->info);
            } else {
                self::assertNull($exception, $case->info);
                self::assertInstanceOf(Pesel::class, $result, $case->info);
            }
        }
    }

    /**
     * @return TryCase[]
     */
    public static function dataSet_testTryIsValidFormat(): array
    {
        $data = [];

        foreach (
            [
                ''             => false,
                ' '            => false,
                '  '           => false,
                '   '          => false,
                '    '         => false,
                '     '        => false,
                '      '       => false,
                '       '      => false,
                '        '     => false,
                '         '    => false,
                '          '   => false,
                '           '  => false,
                '            ' => false,
                '1'            => false,
                '12'           => false,
                '123'          => false,
                '1234'         => false,
                '12345'        => false,
                '123456'       => false,
                '1234567'      => false,
                '12345678'     => false,
                '123456789'    => false,
                '1234567890'   => false,
                '12345678901'  => true,
                '123456789012' => false,
                '12345678901 ' => false,
                ' 12345678901' => false,
                '12345 678901' => false,
                'a'            => false,
                'ab'           => false,
                'abc'          => false,
                'abcd'         => false,
                'abcde'        => false,
                'abcdef'       => false,
                'abcdefg'      => false,
                'abcdefgh'     => false,
                'abcdefghi'    => false,
                'abcdefghij'   => false,
                'abcdefghijk'  => false,
                'abcdefghijkl' => false,
                ' 123'         => false,
                '123 '         => false,
                '1 23'         => false,
                '12 3'         => false,
                '1 2 3'        => false,
                '\\'           => false,
                '*'            => false,
                '0x12'         => false,
                '00000000000'  => true,
            ] as $value => $valid
        ) {
            $data[] = new TryCase(
                pesel   : (string) $value,
                expected: $valid ? null : InvalidFormatException::class
            );
        }

        foreach (self::dataSet_testIsFemale() as $case) {
            $data[] = new TryCase(
                pesel     : $case->pesel,
                expected  : null
            );
        }

        foreach (self::dataSet_testIsMale() as $case) {
            $data[] = new TryCase(
                pesel     : $case->pesel,
                expected  : null
            );
        }

        return $data;
    }

    /**
     * @return void
     * @throws RandomException
     */
    #[Depends('test__construct')]
    #[DependsExternal(AbstractValidatorTest::class, 'testIsValidWeights')]
    public function testTryIsValidWeights(): void
    {
        foreach (self::dataSet_testTryIsValidWeights() as $case) {
            $result = $exception = null;

            try {
                $pesel = new Pesel($case->pesel);
                $result = $pesel->tryIsValidWeights();
            } catch (Throwable $exception) {
                // nothing here
            }

            if ($case->expected) {
                self::assertNotNull($exception, $case->info);
                self::assertIsObject($exception, $case->info);
                self::assertInstanceOf(InvalidPeselException::class, $exception, $case->info);
                self::assertInstanceOf($case->expected, $exception, $case->info);

                self::assertNotNull($exception->getPesel(), $case->info);
                self::assertSame($pesel, $exception->getPesel(), $case->info);

                self::assertNull($result, $case->info);
            } else {
                self::assertNull($exception, $case->info);
                self::assertInstanceOf(Pesel::class, $result, $case->info);
            }
        }
    }

    /**
     * @return TryCase[]
     * @throws RandomException
     */
    public static function dataSet_testTryIsValidWeights(): array
    {
        $data = [];

        foreach (
            [
                ''             => false,
                ' '            => false,
                '  '           => false,
                '   '          => false,
                '    '         => false,
                '     '        => false,
                '      '       => false,
                '       '      => false,
                '        '     => false,
                '         '    => false,
                '          '   => false,
                '           '  => false,
                '            ' => false,
                '1'            => false,
                '12'           => false,
                '123'          => false,
                '1234'         => false,
                '12345'        => false,
                '123456'       => false,
                '1234567'      => false,
                '12345678'     => false,
                '123456789'    => false,
                '1234567890'   => false,
                '123456789012' => false,
                '12345678901 ' => false,
                ' 12345678901' => false,
                '12345 678901' => false,
                'a'            => false,
                'ab'           => false,
                'abc'          => false,
                'abcd'         => false,
                'abcde'        => false,
                'abcdef'       => false,
                'abcdefg'      => false,
                'abcdefgh'     => false,
                'abcdefghi'    => false,
                'abcdefghij'   => false,
                'abcdefghijk'  => false,
                'abcdefghijkl' => false,
                ' 123'         => false,
                '123 '         => false,
                '1 23'         => false,
                '12 3'         => false,
                '1 2 3'        => false,
                '\\'           => false,
                '*'            => false,
                '0x12'         => false,
                '00000000000'  => true,
            ] as $value => $valid
        ) {
            $data[] = new TryCase(
                pesel   : (string) $value,
                expected: $valid ? null : InvalidFormatException::class
            );
        }

        foreach (self::dataSet_testIsFemale() as $case) {
            $data[] = new TryCase(
                pesel     : $case->pesel,
                expected  : null
            );

            $newLast = $last = substr($case->pesel, -1);
            while ($newLast === $last) {
                $newLast = (string) random_int(0, 9);
            }

            $data[] = new TryCase(
                pesel     : substr($case->pesel, 0, -1) . $newLast,
                expected  : InvalidWeightsException::class
            );
        }

        foreach (self::dataSet_testIsMale() as $case) {
            $data[] = new TryCase(
                pesel     : $case->pesel,
                expected  : null
            );

            $newLast = $last = substr($case->pesel, -1);
            while ($newLast === $last) {
                $newLast = (string) random_int(0, 9);
            }

            $data[] = new TryCase(
                pesel     : substr($case->pesel, 0, -1) . $newLast,
                expected  : InvalidWeightsException::class
            );
        }

        return $data;
    }

    /**
     * @return void
     * @throws RandomException
     */
    #[Depends('testTryIsValidBirthdate')]
    #[Depends('testTryIsValidFormat')]
    #[Depends('testTryIsValidGender')]
    #[Depends('testTryIsValidWeights')]
    #[DependsExternal(AbstractValidatorTest::class, 'testIsValid')]
    public function testTryIsValid(): void
    {
        foreach (self::dataSet_testTryIsValid() as $case) {
            $result = $exception = null;

            try {
                $pesel = new Pesel($case->pesel);
                $result = $pesel->tryIsValid($case->forGender);
            } catch (Throwable $exception) {
                // nothing here
            }

            if ($case->expected) {
                self::assertNotNull($exception, $case->info);
                self::assertIsObject($exception, $case->info);
                self::assertInstanceOf(InvalidPeselException::class, $exception, $case->info);

                self::assertNotNull($exception->getPesel(), $case->info);
                self::assertSame($pesel, $exception->getPesel(), $case->info);

                self::assertNotNull($exception->getPrevious(), $case->info);
                self::assertIsObject($exception->getPrevious(), $case->info);
                self::assertInstanceOf(InvalidPeselException::class, $exception->getPrevious(), $case->info);
                self::assertSame($case->expected, $exception->getPrevious()::class, $case->info);

                self::assertNull($result, $case->info);
            } else {
                self::assertNull($exception, $case->info);
                self::assertInstanceOf(Pesel::class, $result, $case->info);
            }
        }
    }

    /**
     * @return TryGenderCase[]
     * @throws RandomException
     */
    public static function dataSet_testTryIsValid(): array
    {
        $data = [];

        foreach (
            [
                ''             => false,
                ' '            => false,
                '  '           => false,
                '   '          => false,
                '    '         => false,
                '     '        => false,
                '      '       => false,
                '       '      => false,
                '        '     => false,
                '         '    => false,
                '          '   => false,
                '           '  => false,
                '            ' => false,
                '1'            => false,
                '12'           => false,
                '123'          => false,
                '1234'         => false,
                '12345'        => false,
                '123456'       => false,
                '1234567'      => false,
                '12345678'     => false,
                '123456789'    => false,
                '1234567890'   => false,
                '123456789012' => false,
                ' 12345678901' => false,
                '12345 678901' => false,
                'a'            => false,
                'ab'           => false,
                'abc'          => false,
                'abcd'         => false,
                'abcde'        => false,
                'abcdef'       => false,
                'abcdefg'      => false,
                'abcdefgh'     => false,
                'abcdefghi'    => false,
                'abcdefghij'   => false,
                'abcdefghijk'  => false,
                'abcdefghijkl' => false,
                ' 123'         => false,
                '123 '         => false,
                '1 23'         => false,
                '12 3'         => false,
                '1 2 3'        => false,
                '\\'           => false,
                '*'            => false,
                '0x12'         => false,
            ] as $value => $valid
        ) {
            $data[] = new TryGenderCase(
                pesel   : (string) $value,
                expected: $valid ? null : InvalidFormatException::class
            );
        }

        foreach (
            [
                '00000000000',
                '12345678903',
            ] as $value
        ) {
            $data[] = new TryGenderCase(
                pesel   : $value,
                expected: InvalidBirthdatePatternException::class
            );
        }

        foreach (self::dataSet_testIsFemale() as $case) {
            $data[] = new TryGenderCase(
                pesel     : $case->pesel,
                expected  : null
            );
            $data[] = new TryGenderCase(
                pesel     : $case->pesel,
                forGender : Gender::Female,
                expected  : null
            );
            $data[] = new TryGenderCase(
                pesel     : $case->pesel,
                forGender : Gender::Male,
                expected  : InvalidGenderException::class
            );

            $newLast = $last = substr($case->pesel, -1);
            while ($newLast === $last) {
                $newLast = (string) random_int(0, 9);
            }

            $data[] = new TryGenderCase(
                pesel     : substr($case->pesel, 0, -1) . $newLast,
                expected  : InvalidWeightsException::class
            );
        }

        foreach (self::dataSet_testIsMale() as $case) {
            $data[] = new TryGenderCase(
                pesel     : $case->pesel,
                expected  : null
            );
            $data[] = new TryGenderCase(
                pesel     : $case->pesel,
                forGender : Gender::Male,
                expected  : null
            );
            $data[] = new TryGenderCase(
                pesel     : $case->pesel,
                forGender : Gender::Female,
                expected  : InvalidGenderException::class
            );

            $newLast = $last = substr($case->pesel, -1);
            while ($newLast === $last) {
                $newLast = (string) random_int(0, 9);
            }

            $data[] = new TryGenderCase(
                pesel     : substr($case->pesel, 0, -1) . $newLast,
                expected  : InvalidWeightsException::class
            );
        }

        return $data;
    }

    /**
     * @return void
     * @throws RandomException
     */
    #[Depends('testTryIsValid')]
    public function testTryCreate(): void
    {
        foreach (self::dataSet_testTryIsValid() as $case) {
            $pesel = $exception = null;

            try {
                $pesel = Pesel::tryCreate($case->pesel, $case->forGender);
            } catch (Throwable $exception) {
                // nothing here
            }

            if ($case->expected) {
                self::assertNotNull($exception, $case->info);
                self::assertIsObject($exception, $case->info);
                self::assertInstanceOf(InvalidPeselException::class, $exception, $case->info);

                self::assertNotNull($exception->getPesel(), $case->info);

                self::assertNotNull($exception->getPrevious(), $case->info);
                self::assertIsObject($exception->getPrevious(), $case->info);
                self::assertInstanceOf(InvalidPeselException::class, $exception->getPrevious(), $case->info);
                self::assertSame($case->expected, $exception->getPrevious()::class, $case->info);

                self::assertNull($pesel, $case->info);
            } else {
                self::assertNull($exception, $case->info);
                self::assertInstanceOf(Pesel::class, $pesel, $case->info);
            }
        }
    }
}
