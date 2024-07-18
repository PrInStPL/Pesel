<?php

declare(strict_types=1);

namespace PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\Pesel;

use PrInSt\ValidatorPolishPesel\Tests\Feature\Cases\AbstractTestCase;
use function is_null;

readonly class TryCase extends AbstractTestCase
{
    /**
     * @param string      $pesel
     * @param class-string|null $expected Full class name
     * @param string|null $infoPrefix
     */
    public function __construct(
        string         $pesel,
        public ?string $expected = null,
        ?string        $infoPrefix = null
    ) {
        parent::__construct(
            info: (is_null($infoPrefix) ? '' : "[$infoPrefix] ")
            . "pesel: '$pesel'"
            . ' [expected: ' . (
                !empty($this->expected)
                    ? substr(
                        $this->expected,
                        ($pos = strrpos($this->expected, '\\'))
                            ? ++$pos
                            : 0
                    )
                    : 'null'
                )
                . ']',
            pesel: $pesel
        );
    }
}
