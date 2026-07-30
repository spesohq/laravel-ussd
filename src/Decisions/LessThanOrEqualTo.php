<?php

namespace Speso\Ussd\Decisions;

use Speso\Ussd\Contracts\Decision;

class LessThanOrEqualTo implements Decision
{
    public function __construct(
        private float|int|string $expected
    ) {
    }

    public function decide(string $actual): bool
    {
        return $actual <= $this->expected;
    }
}
