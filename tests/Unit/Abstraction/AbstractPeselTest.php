<?php

declare(strict_types=1);

namespace PrInSt\ValidatorPolishPesel\Tests\Unit\Abstraction;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\TestCase;
use PrInSt\ValidatorPolishPesel\Abstraction\AbstractPesel;
use PrInSt\ValidatorPolishPesel\Enum\Gender;
use PrInSt\ValidatorPolishPesel\Tests\Datasets\GoodPeselDataSet;
use PrInSt\ValidatorPolishPesel\Tests\Feature\AbstractClassObjects\AbstractPeselTestObject;
use PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractPesel\ExtractDay;
use PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractPesel\ExtractGender;
use PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractPesel\ExtractMonth;
use PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractPesel\ExtractYear;
use PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractPesel\GetDates;
use PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractPesel\GetGender;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Throwable;
use function is_int;
use function is_string;

/**
 * Data sets are partially delivered directly in tests for faster execution and less memory usage.
 */
class AbstractPeselTest extends TestCase
{
    private static array $data = [];

    /**
     * @param string $name
     *
     * @return ReflectionMethod
     * @throws ReflectionException
     */
    private function reflectionMethod(string $name): ReflectionMethod
    {
        return (new ReflectionClass(AbstractPesel::class))->getMethod($name);
    }


    /**
     * @param ExtractDay $case
     *
     * @return void
     */
    #[DataProvider('dataProvider_testExtractDay')]
    #[DependsOnClass(AbstractNumberTest::class)]
    public function testExtractDay(ExtractDay $case): void
    {
        $object = new AbstractPeselTestObject($case->pesel);
        $result = $exception = null;

        try {
            $result = $this->reflectionMethod('extractDay')->invoke($object);
        } catch (Throwable $exception) {
            // nothing here
        }

        self::assertNull($exception);
        self::assertIsString($result);
        self::assertSame($case->expected, $result);
    }

    /**
     * @return string[]
     */
    public static function dataSet_testExtracts(): array
    {
        return [
            '00',
            '01',
            '10',
            'a0',
            'B0',
            '0c',
            '0D',
            '_*',
            '  ',
            ' 9',
            'z ',
        ];
    }

    /**
     * @return array<non-empty-string, ExtractDay[]>
     */
    public static function dataProvider_testExtractDay(): array
    {
        $data = [];

        foreach (self::dataSet_testExtracts() as $value) {
            $case = new ExtractDay($value);
            $data[$case->info] = [$case];
        }

        return $data;
    }

    /**
     * @param ExtractGender $case
     *
     * @return void
     */
    #[DataProvider('dataProvider_testExtractGender')]
    #[DependsOnClass(AbstractNumberTest::class)]
    public function testExtractGender(ExtractGender $case): void
    {
        $object = new AbstractPeselTestObject($case->pesel);
        $result = $exception = null;

        try {
            $result = $this->reflectionMethod('extractGender')->invoke($object);
        } catch (Throwable $exception) {
            // nothing here
        }

        self::assertNull($exception);
        self::assertIsString($result);
        self::assertSame($case->expected, $result);
    }

    /**
     * @return array<non-empty-string, ExtractGender[]>
     */
    public static function dataProvider_testExtractGender(): array
    {
        $data = [];

        $femaleNumbersConstant
            = (new ReflectionClass(AbstractPesel::class))
            ->getConstant('GENDER_FEMALE_CHARS')
        ;

        foreach ($femaleNumbersConstant as $value) {
            $case = new ExtractGender($value);
            $data[$case->info] = [$case];
        }

        $maleNumbersConstant
            = (new ReflectionClass(AbstractPesel::class))
            ->getConstant('GENDER_MALE_CHARS')
        ;

        foreach ($maleNumbersConstant as $value) {
            $case = new ExtractGender($value);
            $data[$case->info] = [$case];
        }

        foreach (
            [
                'a',
                'B',
                '_',
            ] as $value
        ) {
            $case = new ExtractGender($value);
            $data[$case->info] = [$case];
        }

        return $data;
    }

    /**
     * @param ExtractMonth $case
     *
     * @return void
     */
    #[DataProvider('dataProvider_testExtractMonth')]
    #[DependsOnClass(AbstractNumberTest::class)]
    public function testExtractMonth(ExtractMonth $case): void
    {

        $object = new AbstractPeselTestObject($case->pesel);
        $result = $exception = null;

        try {
            $result = $this->reflectionMethod('extractMonth')->invoke($object);
        } catch (Throwable $exception) {
            // nothing here
        }

        self::assertNull($exception);
        self::assertIsString($result);
        self::assertSame($case->expected, $result);
    }

    /**
     * @return array<non-empty-string, ExtractMonth[]>
     */
    public static function dataProvider_testExtractMonth(): array
    {
        $data = [];

        foreach (self::dataSet_testExtracts() as $value) {
            $case = new ExtractMonth($value);
            $data[$case->info] = [$case];
        }

        return $data;
    }

    /**
     * @param ExtractYear $case
     *
     * @return void
     */
    #[DataProvider('dataProvider_testExtractYear')]
    #[DependsOnClass(AbstractNumberTest::class)]
    public function testExtractYear(ExtractYear $case): void
    {
        $object = new AbstractPeselTestObject($case->pesel);
        $result = $exception = null;

        try {
            $result = $this->reflectionMethod('extractYear')->invoke($object);
        } catch (Throwable $exception) {
            // nothing here
        }

        self::assertNull($exception);
        self::assertIsString($result);
        self::assertSame($case->expected, $result);
    }

    /**
     * @return array<non-empty-string, ExtractYear[]>
     */
    public static function dataProvider_testExtractYear(): array
    {
        $data = [];

        foreach (self::dataSet_testExtracts() as $value) {
            $case = new ExtractYear($value);
            $data[$case->info] = [$case];
        }

        return $data;
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    #[Depends('testExtractMonth')]
    public function testGetDateCentury(): void
    {
        foreach (self::dataSet_testGetDates() as $case) {
            $object = new AbstractPeselTestObject($case->pesel);
            $result = $exception = null;

            try {
                $result = $this->reflectionMethod('getDateCentury')->invoke($object);
            } catch (Throwable $exception) {
                // nothing here
            }

            self::assertNull($exception, $case->info);
            if (is_int($case->expectedCentury)) {
                self::assertIsInt($result, $case->info);
            } else {
                self::assertNull($result, $case->info);
            }
            self::assertSame($case->expectedCentury, $result, $case->info);
        }
    }

    /**
     * @return GetDates[]
     */
    public static function dataSet_testGetDates(): array
    {
        if (!empty(self::$data[__FUNCTION__])) {
            return self::$data[__FUNCTION__];
        }

        /** @var array<positive-int, non-negative-int> $centuryAddition */
        $centuryAddition
            = (new ReflectionClass(AbstractPesel::class))
            ->getConstant('MONTH_IN_CENTURY_ADDITION')
        ;

        foreach ($centuryAddition as $century => $monthAddition) {
            for ($year = 0; $year < 100; $year++) {
                $yearFull = $century + $year;
                $yearPesel = sprintf('%02d', $year);

                // bad month (zero)
                self::$data[__FUNCTION__][] = new GetDates(
                    infoPrefix: 'bad month (zero)',
                    pesel     : $yearPesel
                    . sprintf('%02d', $monthAddition)
                    . '0100000'
                );
                // END: bad month

                for ($month = 1; $month <= 12; $month++) {
                    $monthDays = cal_days_in_month(CAL_GREGORIAN, $month, $yearFull);
                    $monthPesel = sprintf('%02d', $month + $monthAddition);

                    // bad day (zero)
                    $badYear = $yearFull - (1 < $month ? 0 : 1);
                    $badMonth = 1 < $month ? $month - 1 : 12;
                    $badDay = cal_days_in_month(CAL_GREGORIAN, $badMonth, $badYear);
                    self::$data[__FUNCTION__][] = new GetDates(
                        infoPrefix       : 'bad day (zero)',
                        pesel            : $yearPesel . $monthPesel . '0000000',
                        expectedCentury  : $century,
                        expectedYear     : $yearFull,
                        expectedMonth    : $month,
                        expectedBirthdate: "$badYear-" . sprintf('%02d-%02d', $badMonth, $badDay),
                    );
                    // END: bad day

                    for ($day = 1; $day <= $monthDays; $day++) {
                        $dayPesel = sprintf('%02d', $day);
                        $pesel = $yearPesel . $monthPesel . $dayPesel . '00000';

                        self::$data[__FUNCTION__][] = new GetDates(
                            infoPrefix       : 'good date',
                            pesel            : $pesel,
                            expectedCentury  : $century,
                            expectedYear     : $yearFull,
                            expectedMonth    : $month,
                            expectedBirthdate: "$yearFull-" . sprintf('%02d', $month) . "-$dayPesel",
                        );
                    }

                    // bad day (greater than mount count)
                    $badDay = $monthDays + 1;
                    $badMonth = 12 > $month ? $month + 1 : 1;
                    $badYear = $yearFull + (12 > $month ? 0 : 1);
                    self::$data[__FUNCTION__][] = new GetDates(
                        infoPrefix       : 'bad day (greater than mount count)',
                        pesel            : $yearPesel . $monthPesel . sprintf('%02d', $badDay) . '00000',
                        expectedCentury  : $century,
                        expectedYear     : $yearFull,
                        expectedMonth    : $month,
                        expectedBirthdate: "$badYear-" . sprintf('%02d-%02d', $badMonth, 1),
                    );
                    // END: bad day
                }

                // bad month (13)
                self::$data[__FUNCTION__][] = new GetDates(
                    infoPrefix: 'bad month (13)',
                    pesel     : $yearPesel
                    . sprintf('%02d', 13 + $monthAddition)
                    . '0100000'
                );
                // END: bad month
            }
        }

        return self::$data[__FUNCTION__];
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    #[Depends('testGetDateCentury')]
    public function testGetDateRealMonth(): void
    {
        foreach (self::dataSet_testGetDates() as $case) {
            $object = new AbstractPeselTestObject($case->pesel);
            $result = $exception = null;

            try {
                $result = $this->reflectionMethod('getDateRealMonth')->invoke($object);
            } catch (Throwable $exception) {
                // nothing here
            }

            self::assertNull($exception, $case->info);
            if (is_int($case->expectedMonth)) {
                self::assertIsInt($result, $case->info);
            } else {
                self::assertNull($result, $case->info);
            }
            self::assertSame($case->expectedMonth, $result, $case->info);
        }
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    #[Depends('testExtractYear')]
    #[Depends('testGetDateCentury')]
    public function testGetDateRealYear(): void
    {
        foreach (self::dataSet_testGetDates() as $case) {
            $object = new AbstractPeselTestObject($case->pesel);
            $result = $exception = null;

            try {
                $result = $this->reflectionMethod('getDateRealYear')->invoke($object);
            } catch (Throwable $exception) {
                // nothing here
            }

            self::assertNull($exception, $case->info);
            if (is_int($case->expectedYear)) {
                self::assertIsInt($result, $case->info);
            } else {
                self::assertNull($result, $case->info);
            }
            self::assertSame($case->expectedYear, $result, $case->info);
        }
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    #[Depends('testGetDateCentury')]
    #[Depends('testGetDateRealYear')]
    #[Depends('testGetDateRealMonth')]
    #[Depends('testExtractDay')]
    public function testGetBirthdate(): void
    {
        foreach (self::dataSet_testGetDates() as $case) {
            $object = new AbstractPeselTestObject($case->pesel);
            $result = $exception = null;

            try {
                $result = $object->getBirthdate();
            } catch (Throwable $exception) {
                // nothing here
            }

            self::assertNull($exception, $case->info);
            if (is_string($case->expectedBirthdate)) {
                self::assertInstanceOf(DateTimeImmutable::class, $result, $case->info);
                self::assertSame('Europe/Warsaw', $result->getTimezone()->getName(), $case->info);
                self::assertSame(
                    $case->expectedBirthdate,
                    $result->format('Y-m-d'),
                    $case->info
                );
            } else {
                self::assertNull($result, $case->info);
            }
        }
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    #[Depends('testExtractGender')]
    public function testGetGender(): void
    {
        foreach (self::dataSet_testGetGender() as $case) {
            $object = new AbstractPeselTestObject($case->pesel);
            $result = $exception = null;

            try {
                $result = $object->getGender();
            } catch (Throwable $exception) {
                // nothing here
            }

            self::assertNull($exception, $case->info);
            self::assertSame($case->expected, $result, $case->info);
        }
    }

    /**
     * @return GetGender[]
     */
    public static function dataSet_testGetGender(): array
    {
        $data = [];

        /** @var string[] $femaleChars */
        $femaleChars
            = (new ReflectionClass(AbstractPesel::class))
            ->getConstant('GENDER_FEMALE_CHARS')
        ;

        foreach ($femaleChars as $char) {
            $data[] = new GetGender('000000000' . $char . '0', Gender::Female);
        }

        /** @var string[] $maleChars */
        $maleChars
            = (new ReflectionClass(AbstractPesel::class))
            ->getConstant('GENDER_MALE_CHARS')
        ;

        foreach ($maleChars as $char) {
            $data[] = new GetGender('000000000' . $char . '0', Gender::Male);
        }

        foreach (
            [
                'a',
                'B',
                '_',
                ' ',
            ] as $char
        ) {
            $data[] = new GetGender('000000000' . $char . '0', null);
        }

        foreach (GoodPeselDataSet::FEMALE_1800 as $value) {
            $data[] = new GetGender($value, Gender::Female, 'GoodPeselDataSet::FEMALE_1800');
        }

        foreach (GoodPeselDataSet::FEMALE_1900 as $value) {
            $data[] = new GetGender($value, Gender::Female, 'GoodPeselDataSet::FEMALE_1900');
        }

        foreach (GoodPeselDataSet::FEMALE_2000 as $value) {
            $data[] = new GetGender($value, Gender::Female, 'GoodPeselDataSet::FEMALE_2000');
        }

        foreach (GoodPeselDataSet::FEMALE_2100 as $value) {
            $data[] = new GetGender($value, Gender::Female, 'GoodPeselDataSet::FEMALE_2100');
        }

        foreach (GoodPeselDataSet::FEMALE_2200 as $value) {
            $data[] = new GetGender($value, Gender::Female, 'GoodPeselDataSet::FEMALE_2200');
        }

        foreach (GoodPeselDataSet::MALE_1800 as $value) {
            $data[] = new GetGender($value, Gender::Male, 'GoodPeselDataSet::MALE_1800');
        }

        foreach (GoodPeselDataSet::MALE_1900 as $value) {
            $data[] = new GetGender($value, Gender::Male, 'GoodPeselDataSet::MALE_1900');
        }

        foreach (GoodPeselDataSet::MALE_2000 as $value) {
            $data[] = new GetGender($value, Gender::Male, 'GoodPeselDataSet::MALE_2000');
        }

        foreach (GoodPeselDataSet::MALE_2100 as $value) {
            $data[] = new GetGender($value, Gender::Male, 'GoodPeselDataSet::MALE_2100');
        }

        foreach (GoodPeselDataSet::MALE_2200 as $value) {
            $data[] = new GetGender($value, Gender::Male, 'GoodPeselDataSet::MALE_2200');
        }

        return $data;
    }
}
