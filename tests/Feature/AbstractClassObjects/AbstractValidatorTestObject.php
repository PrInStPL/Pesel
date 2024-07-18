<?php

declare(strict_types=1);

namespace PrInSt\ValidatorPolishPesel\Tests\Feature\AbstractClassObjects;

use PrInSt\ValidatorPolishPesel\Abstraction\AbstractValidator;

/**
 * AbstractValidator object for testing abstract class.
 */
final class AbstractValidatorTestObject extends AbstractValidator
{
    /**
     * @param string $pesel
     */
    public function __construct(string $pesel)
    {
        $this->setNumber($pesel);
    }
}
