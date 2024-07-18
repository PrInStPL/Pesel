<?php

declare(strict_types=1);

namespace PrInSt\ValidatorPolishPesel\Exception;

use PrInSt\ValidatorPolishPesel\Pesel;
use Throwable;

/**
 * Invalid pesel weights calculation exception
 */
class InvalidWeightsException extends InvalidPeselException
{
    public const string DEFAULT_MESSAGE = 'Pesel weights calculation is invalid.';

    /**
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     * @param Pesel|null     $peselObject
     */
    public function __construct(
        string     $message = self::DEFAULT_MESSAGE,
        int        $code = 0,
        ?Throwable $previous = null,
        ?Pesel     $peselObject = null
    ) {
        parent::__construct($message, $code, $previous, $peselObject);
    }
}
