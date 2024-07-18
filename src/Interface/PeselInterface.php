<?php

declare(strict_types=1);

namespace PrInSt\ValidatorPolishPesel\Interface;

use DateTimeImmutable;
use JsonSerializable;
use PrInSt\ValidatorPolishPesel\Enum\Gender;
use Stringable;

/**
 * PESEL interface
 */
interface PeselInterface extends JsonSerializable, Stringable
{
    /**
     * Get birthdate (in Polish time zone) from pesel.
     *
     * @return DateTimeImmutable|null Return null if PESEL date isn't valid.
     */
    public function getBirthdate(): ?DateTimeImmutable;

    /**
     * Get PESEL number.
     *
     * @return string
     */
    public function getNumber(): string;

    /**
     * Check if the PESEL is for a woman.
     *
     * @return bool Return false if PESEL isn't valid.
     */
    public function isFemale(): bool;

    /**
     * Check if the PESEL is for a man.
     *
     * @return bool Return false if PESEL isn't valid.
     */
    public function isMale(): bool;

    /**
     * Check if PESEL number is fully valid.
     *
     * @param Gender|null $forGender
     *
     * @return bool
     */
    public function isValid(?Gender $forGender = null): bool;
}
