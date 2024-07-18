<?php

declare(strict_types=1);

namespace PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractPesel;

use PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractTestCase;

readonly class ExtractGender extends AbstractTestCase
{
    public function __construct(public string $expected)
    {
        $pesel = '000000000' . $this->expected . '0';

        parent::__construct(
            "$pesel [expected: '$this->expected']",
            $pesel
        );
    }
}
