<?php

declare(strict_types=1);

namespace PrInSt\ValidatorPolishPesel\Abstraction;

use JsonSerializable;
use Stringable;

/**
 * Basic number container
 */
abstract class AbstractNumber implements JsonSerializable, Stringable
{
    private string $number = '';



    /**
     * Get number
     */
    final public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * Set new number
     *
     * @param string $number
     *
     * @return AbstractNumber
     */
    final protected function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @inheritDoc
     */
    final public function __toString(): string
    {
        return $this->number;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): string
    {
        return $this->number;
    }
}
