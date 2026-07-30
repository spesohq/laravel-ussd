<?php

namespace Speso\Ussd\Decisions;

use Speso\Ussd\Contracts\Decision;

class IsNumeric implements Decision
{
    public function decide(string $actual): bool
    {
        return is_numeric($actual);
    }
}
