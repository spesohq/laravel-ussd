<?php

namespace Speso\Ussd\Attributes;

use Attribute;
use Speso\Ussd\Contracts\Decision;

#[Attribute(Attribute::TARGET_CLASS)]
final class Back
{
    public function __construct(
        public array|Decision|string $match,
        public null|array|string $callback = null
    ) {
    }
}
