<?php

declare(strict_types=1);

namespace PrInSt\ValidatorPolishPesel\Tests\Unit\Abstraction;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\DependsExternal;
use PrInSt\ValidatorPolishPesel\Abstraction\AbstractValidator;
use PHPUnit\Framework\TestCase;
use PrInSt\ValidatorPolishPesel\Enum\Gender;
use PrInSt\ValidatorPolishPesel\Tests\Datasets\GoodPeselDataSet;
use PrInSt\ValidatorPolishPesel\Tests\Feature\AbstractClassObjects\AbstractValidatorTestObject;
use PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractValidator\IsValid;
use PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractValidator\IsValidBirthday;
use PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractValidator\IsValidGender;
use PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractValidator\IsValidWeights;
use Random\RandomException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Throwable;
use function array_key_last;
use function random_int;
use function sprintf;
use function substr;

/**
 * Data sets are partially delivered directly in tests for faster execution and less memory usage.
 */
class AbstractValidatorTest extends TestCase
{
    /**
     * @param string $name
     *
     * @return ReflectionMethod
     * @throws ReflectionException
     */
    private function reflectionMethod(string $name): ReflectionMethod
    {
        return (new ReflectionClass(AbstractValidator::class))->getMethod($name);
    }

    /**
     * @param string $value
     * @param bool   $valid
     *
     * @return void
     */
    #[DataProvider('dataProvider_testIsValidGenderPattern')]
    #[DependsExternal(AbstractNumberTest::class, 'testSetGetNumber')]
    public function testIsValidGenderPattern(string $value, bool $valid): void
    {
        $object = new AbstractValidatorTestObject($value);

        $result = $exception = null;
        try {
            $result = $this->reflectionMethod('isValidGenderPattern')->invoke($object);
        } catch (Throwable $exception) {
            // nothing here
        }

        self::assertNull($exception);
        self::assertIsBool($result);
        self::assertSame($valid, $result);
    }

    /**
     * @return array<non-empty-string, array<string|bool>>
     */
    public static function dataProvider_testIsValidGenderPattern(): array
    {
        $data = [];

        foreach (
            [
                '' => false,
                ' ' => false,
                '123456789' => false,
                '123456789 ' => false,
                '1234567890' => true,
                '12345678911' => true,
                '00000000020' => true,
                '00000000030' => true,
                '00000000040' => true,
                '00000000050' => true,
                '00000000060' => true,
                '00000000070' => true,
                '00000000080' => true,
                'abcdefghi9k' => true,
                'abcdefghijk' => false,
                '           ' => false,
                '123456789 1' => false,
            ] as $value => $valid
        ) {
            $data[($valid ? 'valid' : 'invalid') . ": '$value'"] = [(string) $value, $valid];
        }

        return $data;
    }

    /**
     * @return void
     */
    #[Depends('testIsValidGenderPattern')]
    #[DependsExternal(AbstractNumberTest::class, 'testSetGetNumber')]
    #[DependsExternal(AbstractPeselTest::class, 'testGetGender')]
    public function testIsValidGender(): void
    {
        foreach (self::dataSet_testIsValidGender() as $case) {
            $object = new AbstractValidatorTestObject($case->pesel);

            $result = $exception = null;
            try {
                $result = $object->isValidGender($case->forGender);
            } catch(Throwable $exception) {
                // nothing here
            }

            self::assertNull($exception, $case->info);
            self::assertIsBool($result, $case->info);
            self::assertSame($case->expected, $result, $case->info);
        }
    }

    /**
     * @return IsValidGender[]
     */
    public static function dataSet_testIsValidGender(): array
    {
        $data = [];

        foreach (['0', '2', '4', '6', '8'] as $femaleChar) {
            $data[] = new IsValidGender(
                pesel    : "000000000{$femaleChar}0",
                forGender: null,
                expected : true
            );
            $data[] = new IsValidGender(
                pesel    : "000000000{$femaleChar}0",
                forGender: Gender::Female,
                expected : true
            );
            $data[] = new IsValidGender(
                pesel    : "000000000{$femaleChar}0",
                forGender: Gender::Male,
                expected : false
            );
        }

        foreach (['1', '3', '5', '7', '9'] as $maleChar) {
            $data[] = new IsValidGender(
                pesel    : "000000000{$maleChar}0",
                forGender: null,
                expected : true
            );
            $data[] = new IsValidGender(
                pesel    : "000000000{$maleChar}0",
                forGender: Gender::Female,
                expected : false
            );
            $data[] = new IsValidGender(
                pesel    : "000000000{$maleChar}0",
                forGender: Gender::Male,
                expected : true
            );
        }

        foreach (GoodPeselDataSet::FEMALE_1800 as $pesel) {
            $data[] = new IsValidGender(
                pesel     : $pesel,
                forGender : null,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_1800'
            );
            $data[] = new IsValidGender(
                pesel     : $pesel,
                forGender : Gender::Female,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_1800'
            );
        }

        foreach (GoodPeselDataSet::FEMALE_1900 as $pesel) {
            $data[] = new IsValidGender(
                pesel     : $pesel,
                forGender : null,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_1900'
            );
            $data[] = new IsValidGender(
                pesel     : $pesel,
                forGender : Gender::Female,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_1900'
            );
        }

        foreach (GoodPeselDataSet::FEMALE_2000 as $pesel) {
            $data[] = new IsValidGender(
                pesel     : $pesel,
                forGender : null,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_2000'
            );
            $data[] = new IsValidGender(
                pesel     : $pesel,
                forGender : Gender::Female,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_2000'
            );
        }

        foreach (GoodPeselDataSet::FEMALE_2100 as $pesel) {
            $data[] = new IsValidGender(
                pesel     : $pesel,
                forGender : null,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_2100'
            );
            $data[] = new IsValidGender(
                pesel     : $pesel,
                forGender : Gender::Female,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_2100'
            );
        }

        foreach (GoodPeselDataSet::FEMALE_2200 as $pesel) {
            $data[] = new IsValidGender(
                pesel     : $pesel,
                forGender : null,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_2200'
            );
            $data[] = new IsValidGender(
                pesel     : $pesel,
                forGender : Gender::Female,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_2200'
            );
        }

        foreach (GoodPeselDataSet::MALE_1800 as $pesel) {
            $data[] = new IsValidGender(
                pesel     : $pesel,
                forGender : null,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_1800'
            );
            $data[] = new IsValidGender(
                pesel     : $pesel,
                forGender : Gender::Male,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_1800'
            );
        }

        foreach (GoodPeselDataSet::MALE_1900 as $pesel) {
            $data[] = new IsValidGender(
                pesel     : $pesel,
                forGender : null,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_1900'
            );
            $data[] = new IsValidGender(
                pesel     : $pesel,
                forGender : Gender::Male,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_1900'
            );
        }

        foreach (GoodPeselDataSet::MALE_2000 as $pesel) {
            $data[] = new IsValidGender(
                pesel     : $pesel,
                forGender : null,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_2000'
            );
            $data[] = new IsValidGender(
                pesel     : $pesel,
                forGender : Gender::Male,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_2000'
            );
        }

        foreach (GoodPeselDataSet::MALE_2100 as $pesel) {
            $data[] = new IsValidGender(
                pesel     : $pesel,
                forGender : null,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_2100'
            );
            $data[] = new IsValidGender(
                pesel     : $pesel,
                forGender : Gender::Male,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_2100'
            );
        }

        foreach (GoodPeselDataSet::MALE_2200 as $pesel) {
            $data[] = new IsValidGender(
                pesel     : $pesel,
                forGender : null,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_2200'
            );
            $data[] = new IsValidGender(
                pesel     : $pesel,
                forGender : Gender::Male,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_2200'
            );
        }

        return $data;
    }

    /**
     * @param string $value
     * @param bool   $valid
     *
     * @return void
     */
    #[DataProvider('dataProvider_testIsValidFormat')]
    #[DependsExternal(AbstractNumberTest::class, 'testSetGetNumber')]
    public function testIsValidFormat(string $value, bool $valid): void
    {
        $object = new AbstractValidatorTestObject($value);

        $result = $exception = null;
        try {
            $result = $object->isValidFormat();
        } catch (Throwable $exception) {
            // nothing here
        }

        self::assertNull($exception);
        self::assertIsBool($result);
        self::assertSame($valid, $result);
    }

    /**
     * @return array<non-empty-string, array<string|bool>>
     */
    public static function dataProvider_testIsValidFormat(): array
    {
        $data = [];

        foreach (
            [
                ''              => false,
                ' '             => false,
                '1'             => false,
                '12'            => false,
                '123'           => false,
                '1234'          => false,
                '12345'         => false,
                '123456'        => false,
                '1234567'       => false,
                '12345678'      => false,
                '123456789'     => false,
                '1234567890'    => false,
                '12345678901'   => true,
                '00000000000'   => true,
                'abcdefghijk'   => false,
                '           '   => false,
                '123456789012'  => false,
                '1234567890123' => false,
                ' 2345678901'   => false,
                '1234567890 '   => false,
                '12345 78901'   => false,
                '12345a78901'   => false,
                'b2345678901'   => false,
                '1234567890c'   => false,
            ] as $value => $valid
        ) {
            $data[($valid ? 'valid' : 'invalid') . ": '$value'"] = [(string) $value, $valid];
        }

        return $data;
    }

    /**
     * @return void
     */
    #[Depends('testIsValidFormat')]
    #[DependsExternal(AbstractNumberTest::class, 'testSetGetNumber')]
    public function testIsValidBirthdatePattern(): void
    {
        foreach (self::dataSet_testIsValidBirthdatePattern() as $case) {
            $object = new AbstractValidatorTestObject($case->pesel);

            $result = $exception = null;
            try {
                $result = $this->reflectionMethod('isValidBirthdatePattern')->invoke($object);
            } catch(Throwable $exception) {
                // nothing here
            }

            self::assertNull($exception, $case->info);
            self::assertIsBool($result, $case->info);
            self::assertSame($case->expected, $result, $case->info);
        }
    }

    /**
     * @return IsValidBirthday[]
     */
    public static function dataSet_testIsValidBirthdatePattern(): array
    {
        $data = [];

        foreach (
            [
                ''            => false,
                ' '           => false,
                '1'           => false,
                '12'          => false,
                '123'         => false,
                '1234'        => false,
                '12345'       => false,
                'abcdefghijk' => false,
                '           ' => false,
                ' 2345678901' => false,
                '12345 78901' => false,
                '12345a78901' => false,
                'b2345678901' => false,
                '123c5678901' => false,
            ] as $value => $valid
        ) {
            $data[] = new IsValidBirthday((string) $value, $valid);
        }

        foreach (
            [
                1800 => ['81', '82', '83', '84', '85', '86', '87', '88', '89', '90', '91', '92'],
                1900 => ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'],
                2000 => ['21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32'],
                2100 => ['41', '42', '43', '44', '45', '46', '47', '48', '49', '50', '51', '52'],
                2200 => ['61', '62', '63', '64', '65', '66', '67', '68', '69', '70', '71', '72'],
            ] as $months
        ) {
            for ($year = 00; $year < 100; $year++) {
                $yearPesel = sprintf('%02d', $year);

                // bad month (0)
                $data[] = new IsValidBirthday(
                    pesel   : sprintf(
                        '%s%s01',
                        $yearPesel,
                        substr($months[0], 0, 1) . '0'
                    ),
                    expected: false
                );
                // END: bad month(0)

                // bad month (13-19)
                $monthFirstChar = substr($months[array_key_last($months)], 0, 1);

                for ($monthSecondInt = 3; $monthSecondInt <= 9; $monthSecondInt++) {
                    $data[] = new IsValidBirthday(
                        pesel   : sprintf(
                            '%s%s01',
                            $yearPesel,
                            $monthFirstChar . $monthSecondInt
                        ),
                        expected: false
                    );
                }
                // END: bad month(13-19)

                foreach ($months as $month) {
                    // bad day (0)
                    $data[] = new IsValidBirthday(
                        pesel   : sprintf('%s%s00', $yearPesel, $month),
                        expected: false
                    );
                    // END: bad day(0)

                    for ($day = 1; $day <= 31; $day++) {
                        $data[] = new IsValidBirthday(
                            pesel   : sprintf('%s%s%02d', $yearPesel, $month, $day),
                            expected: true
                        );
                    }

                    // bad day (32)
                    $data[] = new IsValidBirthday(
                        pesel   : sprintf('%s%s32', $yearPesel, $month),
                        expected: false
                    );
                    // END: bad day(32)
                }
            }
        }

        foreach (GoodPeselDataSet::FEMALE_1800 as $pesel) {
            $data[] = new IsValidBirthday(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_1800'
            );
        }

        foreach (GoodPeselDataSet::FEMALE_1900 as $pesel) {
            $data[] = new IsValidBirthday(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_1900'
            );
        }

        foreach (GoodPeselDataSet::FEMALE_2000 as $pesel) {
            $data[] = new IsValidBirthday(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_2000'
            );
        }

        foreach (GoodPeselDataSet::FEMALE_2100 as $pesel) {
            $data[] = new IsValidBirthday(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_2100'
            );
        }

        foreach (GoodPeselDataSet::FEMALE_2200 as $pesel) {
            $data[] = new IsValidBirthday(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_2200'
            );
        }

        foreach (GoodPeselDataSet::MALE_1800 as $pesel) {
            $data[] = new IsValidBirthday(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_1800'
            );
        }

        foreach (GoodPeselDataSet::MALE_1900 as $pesel) {
            $data[] = new IsValidBirthday(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_1900'
            );
        }

        foreach (GoodPeselDataSet::MALE_2000 as $pesel) {
            $data[] = new IsValidBirthday(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_2000'
            );
        }

        foreach (GoodPeselDataSet::MALE_2100 as $pesel) {
            $data[] = new IsValidBirthday(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_2100'
            );
        }

        foreach (GoodPeselDataSet::MALE_2200 as $pesel) {
            $data[] = new IsValidBirthday(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_2200'
            );
        }

        return $data;
    }

    /**
     * @return void
     */
    #[Depends('testIsValidBirthdatePattern')]
    #[DependsExternal(AbstractPeselTest::class, 'testGetDateRealMonth')]
    #[DependsExternal(AbstractPeselTest::class, 'testGetDateRealYear')]
    #[DependsExternal(AbstractPeselTest::class, 'testGetBirthdate')]
    public function testIsValidBirthdate(): void
    {
        foreach (self::dataSet_testIsValidBirthdate() as $case) {
            $object = new AbstractValidatorTestObject($case->pesel);

            $result = $exception = null;
            try {
                $result = $object->isValidBirthdate();
            } catch(Throwable $exception) {
                // nothing here
            }

            self::assertNull($exception, $case->info);
            self::assertIsBool($result, $case->info);
            self::assertSame($case->expected, $result, $case->info);
        }
    }

    /**
     * @return IsValidBirthday[]
     */
    public static function dataSet_testIsValidBirthdate(): array
    {
        $data = [];

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
                $data[] = new IsValidBirthday(
                    pesel   : $yearPesel . sprintf('%02d01', $monthAddition),
                    expected: false
                );
                // END: bad month (zero)

                for ($month = 1; $month <= 12; $month++) {
                    $monthDays = cal_days_in_month(CAL_GREGORIAN, $month, $yearFull);
                    $monthPesel = sprintf('%02d', $month + $monthAddition);

                    // bad day (zero)
                    $data[] = new IsValidBirthday(
                        pesel   : $yearPesel . $monthPesel . '00',
                        expected: false
                    );
                    // END: bad day (zero)

                    for ($day = 1; $day <= $monthDays; $day++) {
                        $data[] = new IsValidBirthday(
                            pesel   : $yearPesel . $monthPesel . sprintf('%02d', $day),
                            expected: true
                        );
                    }

                    // bad day (greater than days in the month)
                    $data[] = new IsValidBirthday(
                        pesel   : $yearPesel . $monthPesel . sprintf('%02d', $monthDays + 1),
                        expected: false
                    );
                    // END: bad day (greater than days in the month)

                    // bad day (day 33)
                    $data[] = new IsValidBirthday(
                        pesel   : $yearPesel . $monthPesel . '33',
                        expected: false
                    );
                    // END: bad day (greater than days in the month)
                }

                // bad month (13th)
                $data[] = new IsValidBirthday(
                    pesel   : $yearPesel . sprintf('%02d01', 13 + $monthAddition),
                    expected: false
                );
                // END: bad month (13th)
            }
        }

        foreach (GoodPeselDataSet::FEMALE_1800 as $pesel) {
            $data[] = new IsValidBirthday(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_1800'
            );
        }

        foreach (GoodPeselDataSet::FEMALE_1900 as $pesel) {
            $data[] = new IsValidBirthday(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_1900'
            );
        }

        foreach (GoodPeselDataSet::FEMALE_2000 as $pesel) {
            $data[] = new IsValidBirthday(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_2000'
            );
        }

        foreach (GoodPeselDataSet::FEMALE_2100 as $pesel) {
            $data[] = new IsValidBirthday(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_2100'
            );
        }

        foreach (GoodPeselDataSet::FEMALE_2200 as $pesel) {
            $data[] = new IsValidBirthday(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_2200'
            );
        }

        foreach (GoodPeselDataSet::MALE_1800 as $pesel) {
            $data[] = new IsValidBirthday(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_1800'
            );
        }

        foreach (GoodPeselDataSet::MALE_1900 as $pesel) {
            $data[] = new IsValidBirthday(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_1900'
            );
        }

        foreach (GoodPeselDataSet::MALE_2000 as $pesel) {
            $data[] = new IsValidBirthday(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_2000'
            );
        }

        foreach (GoodPeselDataSet::MALE_2100 as $pesel) {
            $data[] = new IsValidBirthday(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_2100'
            );
        }

        foreach (GoodPeselDataSet::MALE_2200 as $pesel) {
            $data[] = new IsValidBirthday(
                pesel     : $pesel,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_2200'
            );
        }

        return $data;
    }

    /**
     * @return void
     * @throws RandomException
     */
    #[Depends('testIsValidFormat')]
    #[DependsExternal(AbstractNumberTest::class, 'testSetGetNumber')]
    public function testIsValidWeights(): void
    {
        foreach (self::dataSet_testIsValidWeights() as $case) {
            $object = new AbstractValidatorTestObject($case->pesel);

            $result = $exception = null;
            try {
                $result = $object->isValidWeights();
            } catch(Throwable $exception) {
                // nothing here
            }

            self::assertNull($exception, $case->info);
            self::assertIsBool($result, $case->info);
            self::assertSame($case->expected, $result, $case->info);
        }
    }

    /**
     * @param string $pesel
     *
     * @return string
     * @throws RandomException
     */
    private static function modifyPesel(string $pesel): string
    {
        $pos = random_int(0, 10);

        $char = $pesel[$pos];
        while ($char === $pesel[$pos]) {
            $char = (string) random_int(0, 9);
        }

        return substr($pesel, 0, $pos) . $char . substr($pesel, $pos + 1);
    }

    /**
     * @return IsValidWeights[]
     * @throws RandomException
     */
    public static function dataSet_testIsValidWeights(): array
    {
        $data[] = new IsValidWeights('00000000000', true, 'zeros only');
        $data[] = new IsValidWeights('12345678901', false, 'sequence');

        foreach (GoodPeselDataSet::FEMALE_1800 as $pesel) {
            $data[] = new IsValidWeights($pesel, true, 'GoodPeselDataSet::FEMALE_1800');
            $data[] = new IsValidWeights(
                pesel: self::modifyPesel($pesel),
                expected: false,
                infoPrefix: 'modified GoodPeselDataSet::FEMALE_1800'
            );
        }

        foreach (GoodPeselDataSet::FEMALE_1900 as $pesel) {
            $data[] = new IsValidWeights($pesel, true, 'GoodPeselDataSet::FEMALE_1900');
            $data[] = new IsValidWeights(
                pesel: self::modifyPesel($pesel),
                expected: false,
                infoPrefix: 'modified GoodPeselDataSet::FEMALE_1900'
            );
        }

        foreach (GoodPeselDataSet::FEMALE_2000 as $pesel) {
            $data[] = new IsValidWeights($pesel, true, 'GoodPeselDataSet::FEMALE_2000');
            $data[] = new IsValidWeights(
                pesel: self::modifyPesel($pesel),
                expected: false,
                infoPrefix: 'modified GoodPeselDataSet::FEMALE_2000'
            );
        }

        foreach (GoodPeselDataSet::FEMALE_2100 as $pesel) {
            $data[] = new IsValidWeights($pesel, true, 'GoodPeselDataSet::FEMALE_2100');
            $data[] = new IsValidWeights(
                pesel: self::modifyPesel($pesel),
                expected: false,
                infoPrefix: 'modified GoodPeselDataSet::FEMALE_2100'
            );
        }

        foreach (GoodPeselDataSet::FEMALE_2200 as $pesel) {
            $data[] = new IsValidWeights($pesel, true, 'GoodPeselDataSet::FEMALE_2200');
            $data[] = new IsValidWeights(
                pesel: self::modifyPesel($pesel),
                expected: false,
                infoPrefix: 'modified GoodPeselDataSet::FEMALE_2200'
            );
        }

        foreach (GoodPeselDataSet::MALE_1800 as $pesel) {
            $data[] = new IsValidWeights($pesel, true, 'GoodPeselDataSet::MALE_1800');
            $data[] = new IsValidWeights(
                pesel: self::modifyPesel($pesel),
                expected: false,
                infoPrefix: 'modified GoodPeselDataSet::MALE_1800'
            );
        }

        foreach (GoodPeselDataSet::MALE_1900 as $pesel) {
            $data[] = new IsValidWeights($pesel, true, 'GoodPeselDataSet::MALE_1900');
            $data[] = new IsValidWeights(
                pesel: self::modifyPesel($pesel),
                expected: false,
                infoPrefix: 'modified GoodPeselDataSet::MALE_1900'
            );
        }

        foreach (GoodPeselDataSet::MALE_2000 as $pesel) {
            $data[] = new IsValidWeights($pesel, true, 'GoodPeselDataSet::MALE_2000');
            $data[] = new IsValidWeights(
                pesel: self::modifyPesel($pesel),
                expected: false,
                infoPrefix: 'modified GoodPeselDataSet::MALE_2000'
            );
        }

        foreach (GoodPeselDataSet::MALE_2100 as $pesel) {
            $data[] = new IsValidWeights($pesel, true, 'GoodPeselDataSet::MALE_2100');
            $data[] = new IsValidWeights(
                pesel: self::modifyPesel($pesel),
                expected: false,
                infoPrefix: 'modified GoodPeselDataSet::MALE_2100'
            );
        }

        foreach (GoodPeselDataSet::MALE_2200 as $pesel) {
            $data[] = new IsValidWeights($pesel, true, 'GoodPeselDataSet::MALE_2200');
            $data[] = new IsValidWeights(
                pesel: self::modifyPesel($pesel),
                expected: false,
                infoPrefix: 'modified GoodPeselDataSet::MALE_2200'
            );
        }

        return $data;
    }

    /**
     * @return void
     */
    #[Depends('testIsValidFormat')]
    #[Depends('testIsValidWeights')]
    #[Depends('testIsValidBirthdate')]
    #[Depends('testIsValidGender')]
    #[DependsExternal(AbstractNumberTest::class, 'testSetGetNumber')]
    public function testIsValid(): void
    {
        foreach (self::dataSet_testIsValid() as $case) {
            $object = new AbstractValidatorTestObject($case->pesel);

            $result = $exception = null;
            try {
                $result = $object->isValid($case->forGender);
            } catch(Throwable $exception) {
                // nothing here
            }

            self::assertNull($exception, $case->info);
            self::assertIsBool($result, $case->info);
            self::assertSame($case->expected, $result, $case->info);
        }
    }

    /**
     * @return IsValid[]
     */
    public static function dataSet_testIsValid(): array
    {
        $data = [];

        foreach (
            [
                '',
                ' ',
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
                '1234567890123',
                '00000000000',
                '           ',
                'abcdefghijk',
                'abcdefghij',
                'abcdefghi',
                'abcdefgh',
                'abcdefg',
                'abcdef',
                'abcde',
                'abcd',
                'abc',
                'ab',
                'a',
            ] as $pesel
        ) {
            $data[] = new IsValid(
                pesel    : (string) $pesel,
                forGender: null,
                expected : false
            );
        }

        foreach (GoodPeselDataSet::FEMALE_1800 as $pesel) {
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : null,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_1800'
            );
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : Gender::Female,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_1800'
            );
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : Gender::Male,
                expected  : false,
                infoPrefix: 'GoodPeselDataSet::FEMALE_1800'
            );
        }

        foreach (GoodPeselDataSet::FEMALE_1900 as $pesel) {
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : null,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_1900'
            );
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : Gender::Female,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_1900'
            );
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : Gender::Male,
                expected  : false,
                infoPrefix: 'GoodPeselDataSet::FEMALE_1900'
            );
        }

        foreach (GoodPeselDataSet::FEMALE_2000 as $pesel) {
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : null,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_2000'
            );
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : Gender::Female,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_2000'
            );
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : Gender::Male,
                expected  : false,
                infoPrefix: 'GoodPeselDataSet::FEMALE_2000'
            );
        }

        foreach (GoodPeselDataSet::FEMALE_2100 as $pesel) {
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : null,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_2100'
            );
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : Gender::Female,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_2100'
            );
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : Gender::Male,
                expected  : false,
                infoPrefix: 'GoodPeselDataSet::FEMALE_2100'
            );
        }

        foreach (GoodPeselDataSet::FEMALE_2200 as $pesel) {
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : null,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_2200'
            );
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : Gender::Female,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::FEMALE_2200'
            );
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : Gender::Male,
                expected  : false,
                infoPrefix: 'GoodPeselDataSet::FEMALE_2200'
            );
        }

        foreach (GoodPeselDataSet::MALE_1800 as $pesel) {
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : null,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_1800'
            );
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : Gender::Female,
                expected  : false,
                infoPrefix: 'GoodPeselDataSet::MALE_1800'
            );
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : Gender::Male,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_1800'
            );
        }

        foreach (GoodPeselDataSet::MALE_1900 as $pesel) {
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : null,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_1900'
            );
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : Gender::Female,
                expected  : false,
                infoPrefix: 'GoodPeselDataSet::MALE_1900'
            );
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : Gender::Male,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_1900'
            );
        }

        foreach (GoodPeselDataSet::MALE_2000 as $pesel) {
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : null,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_2000'
            );
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : Gender::Female,
                expected  : false,
                infoPrefix: 'GoodPeselDataSet::MALE_2000'
            );
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : Gender::Male,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_2000'
            );
        }

        foreach (GoodPeselDataSet::MALE_2100 as $pesel) {
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : null,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_2100'
            );
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : Gender::Female,
                expected  : false,
                infoPrefix: 'GoodPeselDataSet::MALE_2100'
            );
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : Gender::Male,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_2100'
            );
        }

        foreach (GoodPeselDataSet::MALE_2200 as $pesel) {
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : null,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_2200'
            );
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : Gender::Female,
                expected  : false,
                infoPrefix: 'GoodPeselDataSet::MALE_2200'
            );
            $data[] = new IsValid(
                pesel     : $pesel,
                forGender : Gender::Male,
                expected  : true,
                infoPrefix: 'GoodPeselDataSet::MALE_2200'
            );
        }

        return $data;
    }
}
