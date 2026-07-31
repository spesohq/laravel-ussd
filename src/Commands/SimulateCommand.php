<?php

namespace Speso\Ussd\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Speso\Ussd\Context;
use Speso\Ussd\ContinuingMode;
use Speso\Ussd\Contracts\Response;
use Speso\Ussd\Ussd;

class SimulateCommand extends Command
{
    protected $signature = 'ussd:simulate
                            {state : The fully qualified class name of the initial USSD state or action}
                            {--phone= : The subscriber identifier to simulate, random by default}
                            {--with=* : Extra context values as key=value pairs}
                            {--configurator=* : Configurator classes to apply}
                            {--response= : A Response class to preview the gateway-formatted payload alongside the message}
                            {--continuing-mode=start : One of "start", "continue" or "confirm"}
                            {--continuing-state= : The ContinueState class, required when --continuing-mode=confirm}
                            {--continuing-ttl= : TTL in seconds for the continuing session}
                            {--store= : The cache store used for the Record}';

    protected $description = 'Interactively simulate a USSD session in the terminal';

    public function handle(): int
    {
        $state = $this->argument('state');

        if (!class_exists($state)) {
            $this->error("Class [{$state}] does not exist.");

            return 1;
        }

        $continuingMode = match ($this->option('continuing-mode')) {
            'start' => ContinuingMode::START,
            'continue' => ContinuingMode::CONTINUE,
            'confirm' => ContinuingMode::CONFIRM,
            default => null,
        };

        if (null === $continuingMode) {
            $this->error('Invalid --continuing-mode, expected "start", "continue" or "confirm".');

            return 1;
        }

        $continuingState = $this->option('continuing-state');

        if (ContinuingMode::CONFIRM === $continuingMode && !$continuingState) {
            $this->error('--continuing-state is required when --continuing-mode=confirm.');

            return 1;
        }

        $with = [];

        foreach ($this->option('with') as $pair) {
            [$key, $value] = array_pad(explode('=', $pair, 2), 2, null);
            $with[$key] = $value;
        }

        $gid = $this->option('phone') ?: Str::random(10);
        $continuingTtl = $this->option('continuing-ttl') ? (int) $this->option('continuing-ttl') : null;

        $this->info("Simulating [{$state}] as {$gid}.");
        $this->line('Type :restart to drop the call without ending the session, :exit to hang up.');

        $uid = Str::random(10);
        $input = '';
        $dialed = '';

        while (true) {
            $context = Context::create($uid, $gid, $input)->with($with);

            $ussd = Ussd::build($context)
                ->useInitialState($state)
                ->useContinuingState($continuingMode, $continuingTtl, $continuingState);

            foreach ($this->option('configurator') as $configurator) {
                $ussd->useConfigurator($configurator);
            }

            if ($store = $this->option('store')) {
                $ussd->useStore($store);
            }

            $ussd->useResponse(fn (string $message, bool $terminating) => [
                'message' => $message,
                'terminating' => $terminating,
            ]);

            $result = $ussd->run();

            $message = $result['message'];
            $terminating = $result['terminating'];

            $this->renderScreen($message, $dialed, $terminating);

            if ($responseClass = $this->option('response')) {
                $this->renderPayload($responseClass, $message, $terminating);
            }

            if ($terminating) {
                $again = $this->ask('Press enter to dial again, or type :exit to quit', '');

                if (in_array(strtolower(trim($again)), [':exit', 'exit'], true)) {
                    return 0;
                }

                $uid = Str::random(10);
                $input = '';
                $dialed = '';

                continue;
            }

            $reply = $this->ask('Reply', '');

            if (in_array(strtolower(trim($reply)), [':exit', 'exit'], true)) {
                return 0;
            }

            if (':restart' === strtolower(trim($reply))) {
                $this->warn('Call dropped.');

                $uid = Str::random(10);
                $input = '';
                $dialed = '';

                continue;
            }

            $input = $reply;
            $dialed = trim("{$dialed}*{$reply}", '*');
        }
    }

    private function renderScreen(string $message, string $dialed, bool $terminating): void
    {
        $lines = [];

        foreach (explode("\n", $message) as $line) {
            foreach (explode("\n", wordwrap($line, 40, "\n", true)) as $wrapped) {
                $lines[] = $wrapped;
            }
        }

        if ([] === $lines) {
            $lines = [''];
        }

        $width = max(array_map('mb_strlen', $lines));
        $width = max($width, 20);

        $this->newLine();

        if ('' !== $dialed) {
            $this->line("<fg=gray>*{$dialed}#</>");
        }

        $this->line('┌'.str_repeat('─', $width + 2).'┐');

        foreach ($lines as $line) {
            $this->line(sprintf('│ %s │', $this->pad($line, $width)));
        }

        $this->line('└'.str_repeat('─', $width + 2).'┘');

        if ($terminating) {
            $this->line('<fg=red>[ session ended ]</>');
        }
    }

    private function renderPayload(string $responseClass, string $message, bool $terminating): void
    {
        if (!class_exists($responseClass)) {
            $this->warn("Response class [{$responseClass}] does not exist.");

            return;
        }

        $response = App::make($responseClass);

        if (!$response instanceof Response) {
            $this->warn("[{$responseClass}] does not implement ".Response::class.'.');

            return;
        }

        $payload = $response->respond($message, $terminating);

        if (is_array($payload)) {
            $payload = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        $this->line("<fg=gray>Gateway payload: {$payload}</>");
    }

    private function pad(string $line, int $width): string
    {
        return $line.str_repeat(' ', max(0, $width - mb_strlen($line)));
    }
}
