<?php

declare(strict_types=1);

namespace PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\Pesel;

use PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractTestCase;
use function is_null;

readonly class IfCase extends AbstractTestCase
{
    public function __construct(
        string      $pesel,
        public bool $expected,
        ?string     $infoPrefix = null
    ) {
        parent::__construct(
            info : (is_null($infoPrefix) ? '' : "[$infoPrefix] ")
            . "pesel: '$pesel'"
            . ' [expected: ' . ($this->expected ? 'true' : 'false') . ']',
            pesel: $pesel
        );
    }
}
