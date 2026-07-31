<?php

namespace Speso\Ussd\Commands;

use Composer\Autoload\ClassLoader;
use FilesystemIterator;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use Speso\Ussd\Attributes\Back;
use Speso\Ussd\Attributes\Terminate;
use Speso\Ussd\Attributes\Transition;
use Speso\Ussd\Contracts\Action;
use Speso\Ussd\Contracts\ContinueState;
use Speso\Ussd\Contracts\InitialAction;
use Speso\Ussd\Contracts\InitialState;
use Speso\Ussd\Contracts\State;

class LintCommand extends Command
{
    protected $signature = 'ussd:lint
                            {states?* : Root state classes to walk from; defaults to every InitialState/InitialAction discovered in the configured namespace}
                            {--strict : Exit with a non-zero status if any warnings are reported, not just errors}';

    protected $description = 'Check a USSD flow for dead ends, broken transitions and duplicate matches';

    /** @var array<int, array{0: string, 1: string}> */
    private array $errors = [];

    /** @var array<int, array{0: string, 1: string}> */
    private array $warnings = [];

    public function handle(): int
    {
        $roots = $this->argument('states');

        foreach ($roots as $root) {
            if (!class_exists($root)) {
                $this->error("Class [{$root}] does not exist.");

                return 1;
            }
        }

        $namespace = trim(config('ussd.namespace', 'App\Ussd'), '\\');
        $directory = $this->resolveNamespaceDirectory($namespace);
        $discovered = $directory ? $this->discoverStates($namespace, $directory) : [];

        if ([] === $roots) {
            $roots = array_values(array_filter(
                $discovered,
                fn (string $class) => is_a($class, InitialState::class, true) || is_a($class, InitialAction::class, true)
            ));
        }

        if ([] === $roots) {
            $this->error('No root states were given, and none could be discovered automatically under the "ussd.namespace" config. Pass one or more state classes explicitly.');

            return 1;
        }

        $visited = [];

        foreach ($roots as $root) {
            $this->walk($root, $visited);
        }

        foreach ($discovered as $class) {
            if (isset($visited[$class]) || is_a($class, ContinueState::class, true)) {
                continue;
            }

            $this->warnings[] = [$class, 'Not reached from the given root state(s). Remove it if unused, or pass its own entry point to ussd:lint if it belongs to a separate flow.'];
        }

        $this->report(count($visited));

        if ([] !== $this->errors) {
            return 1;
        }

        if ($this->option('strict') && [] !== $this->warnings) {
            return 1;
        }

        return 0;
    }

    /** @param array<string, bool> $visited */
    private function walk(string $class, array &$visited): void
    {
        if (isset($visited[$class])) {
            return;
        }

        $visited[$class] = true;

        if (!class_exists($class)) {
            $this->errors[] = [$class, 'Class does not exist.'];

            return;
        }

        $implements = class_implements($class) ?: [];

        if (!in_array(State::class, $implements, true)) {
            if (!in_array(Action::class, $implements, true)) {
                $this->errors[] = [$class, 'Does not implement State or Action.'];
            }

            return;
        }

        $reflected = new ReflectionClass($class);

        $transitions = $reflected->getAttributes(Transition::class);
        $backs = $reflected->getAttributes(Back::class);
        $terminates = $reflected->getAttributes(Terminate::class);

        if ([] === $transitions && [] === $terminates) {
            if ([] === $backs) {
                $this->errors[] = [$class, 'Has no #[Transition], #[Back] or #[Terminate] attribute. Reaching this state will always throw a NextStateNotFoundException.'];
            } else {
                $this->warnings[] = [$class, 'Only has a #[Back] attribute, with no #[Transition] or #[Terminate]. Reaching this state will throw a NextStateNotFoundException whenever the back-navigation stack is empty.'];
            }
        }

        $seen = [];

        foreach ($transitions as $attribute) {
            $transition = $attribute->newInstance();
            $key = $this->decisionKey($transition->match);

            if (isset($seen[$key])) {
                $this->warnings[] = [
                    $class,
                    sprintf(
                        'Duplicate #[Transition] match [%s]. Only the first-declared transition (to [%s]) can ever trigger; the one to [%s] is unreachable.',
                        $key,
                        class_basename($seen[$key]),
                        class_basename($transition->to)
                    ),
                ];
            } else {
                $seen[$key] = $transition->to;
            }

            if (!class_exists($transition->to)) {
                $this->errors[] = [$class, "#[Transition] target [{$transition->to}] does not exist."];

                continue;
            }

            $targetImplements = class_implements($transition->to) ?: [];

            if (!in_array(State::class, $targetImplements, true) && !in_array(Action::class, $targetImplements, true)) {
                $this->errors[] = [$class, "#[Transition] target [{$transition->to}] does not implement State or Action."];

                continue;
            }

            $this->walk($transition->to, $visited);
        }
    }

    private function decisionKey(array|object|string $match): string
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

    private function resolveNamespaceDirectory(string $namespace): ?string
    {
        $namespace = trim($namespace, '\\').'\\';
        $loader = $this->findClassLoader();

        if (!$loader) {
            return null;
        }

        $best = null;
        $bestLength = 0;

        foreach ($loader->getPrefixesPsr4() as $prefix => $dirs) {
            if (str_starts_with($namespace, $prefix) && strlen($prefix) > $bestLength && [] !== $dirs) {
                $best = [$prefix, $dirs[0]];
                $bestLength = strlen($prefix);
            }
        }

        if (null === $best) {
            return null;
        }

        [$prefix, $dir] = $best;
        $relative = str_replace('\\', '/', substr($namespace, strlen($prefix)));
        $path = rtrim($dir, '/').'/'.rtrim($relative, '/');

        return is_dir($path) ? $path : null;
    }

    private function findClassLoader(): ?ClassLoader
    {
        foreach (spl_autoload_functions() ?: [] as $autoloader) {
            if (is_array($autoloader) && ($autoloader[0] ?? null) instanceof ClassLoader) {
                return $autoloader[0];
            }
        }

        return null;
    }

    /** @return string[] */
    private function discoverStates(string $namespace, string $directory): array
    {
        $namespace = trim($namespace, '\\');
        $classes = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ('php' !== $file->getExtension()) {
                continue;
            }

            $relative = Str::of($file->getPathname())
                ->after(rtrim($directory, '/').'/')
                ->beforeLast('.php')
                ->replace('/', '\\')
                ->value();

            $class = "{$namespace}\\{$relative}";

            if (!class_exists($class)) {
                continue;
            }

            if (in_array(State::class, class_implements($class) ?: [], true)) {
                $classes[] = $class;
            }
        }

        return $classes;
    }

    private function report(int $visitedCount): void
    {
        if ([] !== $this->errors) {
            $this->newLine();
            $this->line('<fg=red;options=bold>Errors:</>');

            foreach ($this->errors as [$class, $message]) {
                $this->line("  <fg=red>✘</> {$class}: {$message}");
            }
        }

        if ([] !== $this->warnings) {
            $this->newLine();
            $this->line('<fg=yellow;options=bold>Warnings:</>');

            foreach ($this->warnings as [$class, $message]) {
                $this->line("  <fg=yellow>!</> {$class}: {$message}");
            }
        }

        $this->newLine();

        if ([] === $this->errors && [] === $this->warnings) {
            $this->info("No issues found across {$visitedCount} reachable state(s).");

            return;
        }

        $this->line(sprintf(
            '%d reachable state(s), %d error(s), %d warning(s).',
            $visitedCount,
            count($this->errors),
            count($this->warnings)
        ));
    }
}
