<?php

declare(strict_types=1);

namespace PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractPesel;

use PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractTestCase;

readonly class ExtractYear extends AbstractTestCase
{
    public function __construct(public string $expected)
    {
        $pesel = $this->expected . '000000000';

        parent::__construct(
            "$pesel [expected: '$this->expected']",
            $pesel
        );
    }
}
