<?php

declare(strict_types=1);

namespace PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractPesel;

use PrInSt\ValidatorPolishPesel\Enum\Gender;
use PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractTestCase;

readonly class GetGender extends AbstractTestCase
{
    public function __construct(string $pesel, public ?Gender $expected, ?string $infoPrefix = null)
    {
        $expectedString = $this->expected ? 'Gender::' . $this->expected->name : 'NULL';
        $info = (empty($infoPrefix) ? '' : "[$infoPrefix] ") . "$pesel [expected: $expectedString]";

        parent::__construct(
            $info,
            $pesel
        );
    }
}
