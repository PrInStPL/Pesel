<?php

declare(strict_types=1);

namespace PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractValidator;

use PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractTestCase;

readonly class IsValidBirthday extends AbstractTestCase
{
    public function __construct(
        string         $pesel,
        public bool    $expected,
        ?string        $infoPrefix = null
    ) {
        $info
            = (empty($infoPrefix) ? '' : "[$infoPrefix] ")
            . $pesel
            . ' [expected: ' . ($this->expected ? 'TRUE' : 'FALSE') . ']'
        ;

        parent::__construct(
            $info,
            $pesel
        );
    }
}
