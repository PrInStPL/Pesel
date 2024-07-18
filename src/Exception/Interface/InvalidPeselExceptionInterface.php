<?php

declare(strict_types=1);

namespace PrInSt\ValidatorPolishPesel\Exception\Interface;

use PrInSt\ValidatorPolishPesel\Pesel;
use Throwable;

/**
 * InvalidPeselExceptions interface
 */
interface InvalidPeselExceptionInterface extends Throwable
{
    /**
     * Get invalid Pesel object
     *
     * @return Pesel|null
     */
    public function getPesel(): ?Pesel;
}
