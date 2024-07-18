<?php

declare(strict_types=1);

namespace PrInSt\ValidatorPolishPesel\Tests\Feature\AbstractClassObjects;

use PrInSt\ValidatorPolishPesel\Abstraction\AbstractPesel;

/**
 * AbstractPesel object for testing abstract class.
 */
final class AbstractPeselTestObject extends AbstractPesel
{
    /**
     * @param string $pesel
     */
    public function __construct(string $pesel)
    {
        $this->setNumber($pesel);
    }
}
