<?php

namespace Speso\Ussd\Decisions;

use Speso\Ussd\Contracts\Decision;

class Fallback implements Decision
{
    public function decide(string $actual): bool
    {
        return true;
    }
}
