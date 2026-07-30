<?php

namespace Speso\Ussd\Attributes;

use Attribute;
use Speso\Ussd\Contracts\Decision;

#[Attribute(Attribute::TARGET_CLASS)]
final class Truncate
{
    public function __construct(
        public int $limit,
        public string $end,
        public array|Decision|string $more
    ) {
    }
}
