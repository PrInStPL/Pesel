<?php

declare(strict_types=1);

namespace PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractPesel;

use PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractTestCase;

readonly class GetDates extends AbstractTestCase
{
    public function __construct(
        string         $infoPrefix,
        string         $pesel,
        public ?int    $expectedCentury = null,
        public ?int    $expectedYear = null,
        public ?int    $expectedMonth = null,
        public ?string $expectedBirthdate = null,
    ) {
        parent::__construct(
            "[$infoPrefix] $pesel"
            . " [expected century: '$this->expectedCentury'; "
            . "expected year: '$this->expectedYear'; "
            . "expected month: '$this->expectedMonth'; "
            . "expected birthdate: '$this->expectedBirthdate']",
            $pesel
        );
    }
}
