<?php

declare(strict_types=1);

namespace PrInSt\ValidatorPolishPesel\Exception;

use Exception;
use PrInSt\ValidatorPolishPesel\Exception\Interface\InvalidPeselExceptionInterface;
use PrInSt\ValidatorPolishPesel\Pesel;
use Throwable;

/**
 * Invalid pesel exception
 */
class InvalidPeselException extends Exception implements InvalidPeselExceptionInterface
{
    public const string DEFAULT_MESSAGE = 'Pesel is invalid.';

    /**
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     * @param Pesel|null     $peselObject
     */
    public function __construct(
        string        $message = self::DEFAULT_MESSAGE,
        int           $code = 0,
        ?Throwable    $previous = null,
        public ?Pesel $peselObject = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @inheritDoc
     */
    public function getPesel(): ?Pesel
    {
        return $this->peselObject;
    }
}
