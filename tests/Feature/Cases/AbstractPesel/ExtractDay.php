<?php

declare(strict_types=1);

namespace PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractPesel;

use PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractTestCase;

readonly class ExtractDay extends AbstractTestCase
{
    public function __construct(public string $expected)
    {
        $pesel = '0000' . $this->expected . '00000';

        parent::__construct(
            "$pesel [expected: '$this->expected']",
            $pesel
        );
    }
}
