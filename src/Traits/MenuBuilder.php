<?php

namespace Speso\Ussd\Traits;

use Illuminate\Support\Facades\Lang;

trait MenuBuilder
{
    public function text(string $text): static
    {
        $this->content .= $text;

        return $this;
    }

    public function line(string $text): static
    {
        $this->content .= $text . PHP_EOL;

        return $this;
    }

    public function trans(string $key, array $replace = [], ?string $locale = null): static
    {
        $this->content .= Lang::get($key, $replace, $locale);

        return $this;
    }

    public function transLine(string $key, array $replace = [], ?string $locale = null): static
    {
        $this->content .= Lang::get($key, $replace, $locale) . PHP_EOL;

        return $this;
    }

    public function transChoice(string $key, int $number, array $replace = [], ?string $locale = null): static
    {
        $this->content .= Lang::choice($key, $number, $replace, $locale);

        return $this;
    }

    public function lineBreak(int $times = 1): static
    {
        $this->content .= str_repeat(PHP_EOL, $times);

        return $this;
    }

    public function listing(
        array $items,
        ?callable $numbering = null,
        string $spacer = '.',
        string $divider = PHP_EOL,
        int $page = 1,
        ?int $perPage = null
    ): static {
        $numbering ??= fn (int $index) => $index + 1;
        $offset = max(0, ($page - 1) * ($perPage ?? 0));
        $items = array_slice($items, $offset, $perPage);

        foreach ($items as $index => $item) {
            $this->content .= "{$numbering($offset + $index)}{$spacer}{$item}{$divider}";
        }

        return $this;
    }

    public function format(string $format, array ...$values): static
    {
        $this->content .= sprintf($format, ...$values);

        return $this;
    }
}
