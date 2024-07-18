<?php

declare(strict_types=1);

namespace PrInSt\ValidatorPolishPesel\Tests\Feature\Cases;

readonly abstract class AbstractTestCase
{
    public function __construct(
        public string $info,
        public string $pesel,
    ) {
        // nothing here
    }
}
