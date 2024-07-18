<?php

declare(strict_types=1);

namespace PrInSt\ValidatorPolishPesel\Abstraction;

use DateTimeImmutable;
use DateTimeZone;
use PrInSt\ValidatorPolishPesel\Enum\Gender;
use Throwable;
use function date_create_immutable_from_format;
use function in_array;
use function intval;
use function is_null;

/**
 * Abstract PESEL
 */
abstract class AbstractPesel extends AbstractNumber
{
    private const array GENDER_FEMALE_CHARS = ['0', '2', '4', '6', '8'];
    private const array GENDER_MALE_CHARS = ['1', '3', '5', '7', '9'];
    final protected const array MONTH_IN_CENTURY_ADDITION = [
        1800 => 80,
        1900 => 0,
        2000 => 20,
        2100 => 40,
        2200 => 60,
    ];
    private const array MONTH_IN_CENTURY_MAP = [
        1800 => ['81', '82', '83', '84', '85', '86', '87', '88', '89', '90', '91', '92'],
        1900 => ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'],
        2000 => ['21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32'],
        2100 => ['41', '42', '43', '44', '45', '46', '47', '48', '49', '50', '51', '52'],
        2200 => ['61', '62', '63', '64', '65', '66', '67', '68', '69', '70', '71', '72'],
    ];



    private ?DateTimeImmutable $birthdate = null;
    private ?int $dateCentury = null;
    private ?int $dateRealMonth = null;
    private ?int $dateRealYear = null;
    private ?Gender $gender = null;



    /**
     * @return string
     */
    protected function extractDay(): string
    {
        return substr($this->getNumber(), 4, 2);
    }

    /**
     * @return string
     */
    protected function extractGender(): string
    {
        return substr($this->getNumber(), 9, 1);
    }

    /**
     * @return string
     */
    protected function extractMonth(): string
    {
        return substr($this->getNumber(), 2, 2);
    }

    /**
     * @return string
     */
    protected function extractYear(): string
    {
        return substr($this->getNumber(), 0, 2);
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getBirthdate(): ?DateTimeImmutable
    {
        if ($this->birthdate) {
            return $this->birthdate;
        }

        if (
            ($year = $this->getDateRealYear())
            && ($month = $this->getDateRealMonth())
            && !empty($day = $this->extractDay())
        ) {
            try {
                /**
                 * @var int    $year
                 * @var int    $month
                 * @var string $day
                 */
                $date = date_create_immutable_from_format(
                    'Y-n-d H:i:s',
                    "$year-$month-$day 00:00:00",
                    new DateTimeZone('Europe/Warsaw')
                );

                if ($date) {
                    $this->birthdate = $date;
                }
            } catch (Throwable) {
                // nothing here
            }
        }

        return $this->birthdate;
    }

    /**
     * Returns the mapped century if the PESEL month is valid.
     *
     * @return int|null
     */
    protected function getDateCentury(): ?int
    {
        if ($this->dateCentury) {
            return $this->dateCentury;
        }

        if (!empty($month = $this->extractMonth())) {
            foreach (self::MONTH_IN_CENTURY_MAP as $century => $months) {
                if (in_array($month, $months, true)) {
                    $this->dateCentury = $century;
                    break;
                }
            }
        }

        return $this->dateCentury;
    }

    /**
     * Return calculated real month if the PESEL month is valid and if calculated real month is positive.
     *
     * @return int|null
     */
    protected function getDateRealMonth(): ?int
    {
        if ($this->dateRealMonth) {
            return $this->dateRealMonth;
        }

        if ($century = $this->getDateCentury()) {
            try {
                $realMonth = intval($this->extractMonth()) - self::MONTH_IN_CENTURY_ADDITION[$century];

                if (0 < $realMonth) {
                    $this->dateRealMonth = $realMonth;
                }
            } catch (Throwable) {
                // nothing here
            }
        }

        return $this->dateRealMonth;
    }

    /**
     * Return calculated real year if century was mapped with success.
     *
     * @return int|null
     */
    protected function getDateRealYear(): ?int
    {
        if ($this->dateRealYear) {
            return $this->dateRealYear;
        }

        if ($century = $this->getDateCentury()) {
            try {
                $year = intval($this->extractYear());

                if (0 <= $year) {
                    $this->dateRealYear = $century + $year;
                }
            } catch (Throwable) {
                // nothing here
            }
        }

        return $this->dateRealYear;
    }

    /**
     * @return Gender|null
     */
    public function getGender(): ?Gender
    {
        if (!is_null($this->gender)) {
            return $this->gender;
        }

        $gender = $this->extractGender();

        if (in_array($gender, self::GENDER_FEMALE_CHARS, true)) {
            $this->gender = Gender::Female;
        } elseif (in_array($gender, self::GENDER_MALE_CHARS, true)) {
            $this->gender = Gender::Male;
        }

        return $this->gender;
    }
}
