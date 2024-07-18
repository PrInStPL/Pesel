<?php

declare(strict_types=1);

namespace PrInSt\ValidatorPolishPesel;

use PrInSt\ValidatorPolishPesel\Abstraction\AbstractValidator;
use PrInSt\ValidatorPolishPesel\Enum\Gender;
use PrInSt\ValidatorPolishPesel\Exception\InvalidBirthdateException;
use PrInSt\ValidatorPolishPesel\Exception\InvalidBirthdatePatternException;
use PrInSt\ValidatorPolishPesel\Exception\InvalidFormatException;
use PrInSt\ValidatorPolishPesel\Exception\InvalidGenderException;
use PrInSt\ValidatorPolishPesel\Exception\InvalidGenderPatternException;
use PrInSt\ValidatorPolishPesel\Exception\InvalidPeselException;
use PrInSt\ValidatorPolishPesel\Exception\InvalidWeightsException;
use PrInSt\ValidatorPolishPesel\Interface\PeselInterface;

/**
 * Polish PESEL
 */
class Pesel extends AbstractValidator implements PeselInterface
{
    /**
     * @param string $number
     */
    public function __construct(string $number)
    {
        $this->setNumber($number);
    }

    /**
     * @inheritDoc
     */
    public function isFemale(): bool
    {
        return $this->isValidGender(Gender::Female);
    }

    /**
     * @inheritDoc
     */
    public function isMale(): bool
    {
        return $this->isValidGender(Gender::Male);
    }

    /**
     * Try to create the PESEL instance and validate it.
     *
     * @throws InvalidPeselException
     */
    public static function tryCreate(string $number, ?Gender $forGender = null): self
    {
        return (new self($number))->tryIsValid($forGender);
    }

    /**
     * @return $this
     * @throws InvalidPeselException
     */
    public function tryIsValid(?Gender $forGender = null): self
    {
        try {
            $this
                ->tryIsValidFormat()
                ->tryIsValidWeights()
                ->tryIsValidBirthdate()
            ;

            if ($forGender) {
                $this->tryIsValidGender($forGender);
            }
        } catch (InvalidPeselException $e) {
            throw new InvalidPeselException($e->getMessage(), $e->getCode(), $e, $this);
        }

        return $this;
    }

    /**
     * @return Pesel
     * @throws InvalidBirthdateException
     * @throws InvalidBirthdatePatternException
     */
    public function tryIsValidBirthdate(): self
    {
        if (!$this->isValidBirthdatePattern()) {
            throw new InvalidBirthdatePatternException(peselObject: $this);
        }

        if (!$this->isValidBirthdate()) {
            throw new InvalidBirthdateException(peselObject: $this);
        }

        return $this;
    }

    /**
     * @return Pesel
     * @throws InvalidFormatException
     */
    public function tryIsValidFormat(): self
    {
        if (!$this->isValidFormat()) {
            throw new InvalidFormatException(peselObject: $this);
        }

        return $this;
    }

    /**
     * @param Gender|null $forGender
     *
     * @return Pesel
     * @throws InvalidGenderException
     * @throws InvalidGenderPatternException
     */
    public function tryIsValidGender(?Gender $forGender = null): self
    {
        if (!$this->isValidGenderPattern()) {
            throw new InvalidGenderPatternException(peselObject: $this);
        }

        if (!$this->isValidGender($forGender)) {
            throw new InvalidGenderException(peselObject: $this);
        }

        return $this;
    }

    /**
     * @return Pesel
     * @throws InvalidFormatException
     * @throws InvalidWeightsException
     */
    public function tryIsValidWeights(): self
    {
        if (!$this->isValidFormat()) {
            throw new InvalidFormatException(peselObject: $this);
        }

        if (!$this->isValidWeights()) {
            throw new InvalidWeightsException(peselObject: $this);
        }

        return $this;
    }
}
