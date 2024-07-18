<?php

declare(strict_types=1);

namespace PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\Pesel;

use PrInSt\ValidatorPolishPesel\Enum\Gender;

readonly class TryGenderCase extends TryCase
{
    /**
     * @param string      $pesel
     * @param Gender|null $forGender
     * @param string|null $expected Full class name
     * @param string|null $infoPrefix
     */
    public function __construct(
        string         $pesel,
        public ?Gender $forGender = null,
        ?string        $expected = null,
        ?string        $infoPrefix = null
    ) {
        parent::__construct($pesel, $expected, $infoPrefix);
    }
}
