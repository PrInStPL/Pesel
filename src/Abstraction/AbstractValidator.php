<?php

declare(strict_types=1);

namespace PrInSt\ValidatorPolishPesel\Abstraction;

use PrInSt\ValidatorPolishPesel\Enum\Gender;
use function array_key_exists;
use function intval;
use function is_null;
use function sprintf;
use function str_starts_with;
use const CAL_GREGORIAN;

/**
 * PESEL validator
 */
abstract class AbstractValidator extends AbstractPesel
{
    final protected const string BIRTHDATE_PATTERN
        = '/^([0-9]{2})' // year
        . '([02468][1-9]|[13579][012])' // month
        . '(0[1-9]|[12][0-9]|3[0-1])/' // day
    ;
    final public const string FORMAT_PATTERN = '/^[0-9]{11}$/';
    final public const string GENDER_PATTERN = '/^.{9}[0-9]/';
    final protected const array WEIGHTS = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3];



    private ?bool $validBirthdate = null;
    private ?bool $validBirthdatePattern = null;
    private ?bool $validFormatPattern = null;
    private ?bool $validGenderPattern = null;
    private ?bool $validWeights = null;



    /**
     * Validating PESEL fully
     *
     * @param Gender|null $forGender Check gender valid if set only.
     *
     * @return bool
     */
    final public function isValid(?Gender $forGender = null): bool
    {
        return
            $this->isValidFormat()
            && $this->isValidWeights()
            && $this->isValidBirthdate()
            && ($forGender ? $this->isValidGender($forGender) : true)
        ;
    }

    /**
     * Validate PESEL birthday pattern only.
     *
     * @return bool
     */
    final protected function isValidBirthdatePattern(): bool
    {
        if (!is_null($this->validBirthdatePattern)) {
            return $this->validBirthdatePattern;
        }

        return $this->validBirthdatePattern = (bool) preg_match(self::BIRTHDATE_PATTERN, $this->getNumber());
    }

    /**
     * Validate PESEL birthday pattern and date only.
     *
     * @return bool
     */
    final public function isValidBirthdate(): bool
    {
        if (!is_null($this->validBirthdate)) {
            return $this->validBirthdate;
        }

        if (
            !$this->isValidBirthdatePattern()
            || is_null($monthReal = $this->getDateRealMonth())
            || is_null($yearReal = $this->getDateRealYear())
        ) {
            return $this->validBirthdate = false;
        }

        $day = intval($this->extractDay());
        $monthDays = cal_days_in_month(CAL_GREGORIAN, $monthReal, $yearReal);

        if (
            $monthDays < $day
            || is_null($birthdate = $this->getBirthdate())
        ) {
            return $this->validBirthdate = false;
        }

        $dateRealCandidate = sprintf('%04d-%02d-%02d', $yearReal, $monthReal, $day);

        if ($birthdate->format('Y-m-d') !== $dateRealCandidate) {
            return $this->validBirthdate = false;
        }

        $birthdateYearFull = $birthdate->format('Y');
        $birthdateCentury = intval(substr($birthdateYearFull, 0, 2) . '00');

        if (!array_key_exists($birthdateCentury, self::MONTH_IN_CENTURY_ADDITION)) {
            return $this->validBirthdate = false;
        }

        $birthdayMonthAdded
            = intval($birthdate->format('m'))
            + self::MONTH_IN_CENTURY_ADDITION[$birthdateCentury]
        ;

        $peselDateCandidate
            = substr($birthdateYearFull, 2, 2)
            . sprintf('%02d', $birthdayMonthAdded)
            . $birthdate->format('d')
        ;

        return $this->validBirthdate = str_starts_with($this->getNumber(), $peselDateCandidate);
    }

    /**
     * Validate PESEL format only. A value containing all zeros is invalid.
     *
     * @return bool
     */
    final public function isValidFormat(): bool
    {
        return $this->validFormatPattern
            ?? $this->validFormatPattern = (bool) preg_match(self::FORMAT_PATTERN, $this->getNumber())
        ;
    }

    /**
     * Validate PESEL for gender pattern only.
     *
     * @return bool
     */
    final protected function isValidGenderPattern(): bool
    {
        if (!is_null($this->validGenderPattern)) {
            return $this->validGenderPattern;
        }

        if (true === $this->validFormatPattern) {
            return $this->validGenderPattern = true;
        }

        return $this->validGenderPattern = (bool) preg_match(self::GENDER_PATTERN, $this->getNumber());
    }

    /**
     * Validate PESEL for gender pattern and for gender if set.
     *
     * @param Gender|null $forGender
     *
     * @return bool
     */
    final public function isValidGender(?Gender $forGender = null): bool
    {
        return $this->isValidGenderPattern()
            && ($forGender ? $forGender === $this->getGender() : true)
        ;
    }

    /**
     * Validate PESEL for format pattern and control weights.
     *
     * @return bool
     */
    final public function isValidWeights(): bool
    {
        if (!is_null($this->validWeights)) {
            return $this->validWeights;
        }

        if (!$this->isValidFormat()) {
            return $this->validWeights = false;
        }

        $pesel = $this->getNumber();
        $sum = 0;

        foreach (self::WEIGHTS as $pos => $weight) {
            $sum += $weight * (int) $pesel[$pos];
        }

        $mod = $sum % 10;

        $control = $mod ? 10 - $mod : 0;

        return $this->validWeights = str_ends_with($pesel, (string) $control);
    }
}
