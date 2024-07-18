<?php

declare(strict_types=1);

namespace PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractValidator;

use PrInSt\ValidatorPolishPesel\Enum\Gender;
use PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractTestCase;

readonly class IsValid extends AbstractTestCase
{
    public function __construct(
        string         $pesel,
        public ?Gender $forGender,
        public bool    $expected,
        ?string        $infoPrefix = null
    ) {
        $info
            = (empty($infoPrefix) ? '' : "[$infoPrefix] ")
            . "pesel: '$pesel'"
            . ' [forGender: ' . ($this->forGender ? 'Gender::' . $this->forGender->name : 'NULL') . '; '
            . 'expected: ' . ($this->expected ? 'TRUE' : 'FALSE') . ']'
        ;

        parent::__construct(
            $info,
            $pesel
        );
    }
}
