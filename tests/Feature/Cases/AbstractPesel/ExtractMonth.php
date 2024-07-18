<?php

declare(strict_types=1);

namespace PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractPesel;

use PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractTestCase;

readonly class ExtractMonth extends AbstractTestCase
{
    public function __construct(public string $expected)
    {
        $pesel = '00' . $this->expected . '0000000';

        parent::__construct(
            "$pesel [expected: '$this->expected']",
            $pesel
        );
    }
}
