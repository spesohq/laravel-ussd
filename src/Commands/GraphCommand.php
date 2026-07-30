<?php

namespace Speso\Ussd\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use ReflectionClass;
use Speso\Ussd\Attributes\Back;
use Speso\Ussd\Attributes\Terminate;
use Speso\Ussd\Attributes\Transition;
use Speso\Ussd\Contracts\Decision;
use Speso\Ussd\Contracts\State;

class GraphCommand extends Command
{
    private const HISTORY = '__history__';

    protected $signature = 'ussd:graph
                            {state : The fully qualified class name of the initial USSD state}
                            {--output= : Write the diagram to a file instead of the console}';

    protected $description = 'Render a Mermaid state diagram of a USSD flow';

    public function handle(): int
    {
        $initial = $this->argument('state');

        if (!class_exists($initial)) {
            $this->error("Class [{$initial}] does not exist.");

            return 1;
        }

        $lines = [];
        $visited = [];
        $usesHistory = false;

        $this->walk($initial, $visited, $lines, $usesHistory);

        if ($usesHistory) {
            $lines[] = sprintf('%s : Previous state', $this->nodeId(static::HISTORY));
        }

        array_unshift($lines, 'stateDiagram-v2');

        if ($output = $this->option('output')) {
            file_put_contents($output, implode(PHP_EOL, $lines).PHP_EOL);
            $this->info("Diagram written to {$output}");

            return 0;
        }

        foreach ($lines as $line) {
            $this->line($line);
        }

        return 0;
    }

    /**
     * @param array<string, bool> $visited
     * @param string[] $lines
     */
    private function walk(string $class, array &$visited, array &$lines, bool &$usesHistory): void
    {
        if (isset($visited[$class])) {
            return;
        }

        $visited[$class] = true;

        $id = $this->nodeId($class);

        if (!class_exists($class) || !in_array(State::class, class_implements($class) ?: [], true)) {
            $lines[] = sprintf('%s : %s (dynamic)', $id, class_basename($class));

            return;
        }

        $lines[] = sprintf('%s : %s', $id, class_basename($class));

        $reflected = new ReflectionClass($class);

        foreach ($reflected->getAttributes(Transition::class) as $attribute) {
            $transition = $attribute->newInstance();

            $lines[] = sprintf(
                '%s --> %s : %s',
                $id,
                $this->nodeId($transition->to),
                $this->describeDecision($transition->match)
            );

            $this->walk($transition->to, $visited, $lines, $usesHistory);
        }

        foreach ($reflected->getAttributes(Back::class) as $attribute) {
            $back = $attribute->newInstance();
            $usesHistory = true;

            $lines[] = sprintf(
                '%s --> %s : %s',
                $id,
                $this->nodeId(static::HISTORY),
                $this->describeDecision($back->match)
            );
        }

        if ([] !== $reflected->getAttributes(Terminate::class)) {
            $lines[] = "{$id} --> [*]";
        }
    }

    private function nodeId(string $class): string
    {
        return Str::of($class)->replace('\\', '_')->value();
    }

    private function describeDecision(array|Decision|string $match): string
    {
        if (is_string($match)) {
            return class_basename($match);
        }

        if (is_array($match)) {
            return $this->formatDecision($match[0], array_slice($match, 1));
        }

        $reflected = new ReflectionClass($match);
        $args = [];

        foreach ($reflected->getConstructor()?->getParameters() ?? [] as $parameter) {
            if (!$reflected->hasProperty($parameter->getName())) {
                continue;
            }

            $property = $reflected->getProperty($parameter->getName());
            $property->setAccessible(true);
            $value = $property->getValue($match);

            if ($parameter->isVariadic() && is_array($value)) {
                array_push($args, ...$value);
            } else {
                $args[] = $value;
            }
        }

        return $this->formatDecision($match::class, $args);
    }

    private function formatDecision(string $class, array $args): string
    {
        $label = class_basename($class);

        if ([] === $args) {
            return $label;
        }

        $formatted = array_map(
            fn ($value) => is_string($value) ? "'{$value}'" : (string) $value,
            $args
        );

        return sprintf('%s(%s)', $label, implode(', ', $formatted));
    }
}
