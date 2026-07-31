<?php

namespace Speso\Ussd\Tests\Integration;

use Speso\Ussd\Tests\Dummy\CogConfigurator;
use Speso\Ussd\Tests\Dummy\ContinuingState;
use Speso\Ussd\Tests\Dummy\StartState;
use Speso\Ussd\Tests\TestCase;

final class SimulateCommandTest extends TestCase
{
    public function test_simulate_command_walks_through_a_flow_and_ends_the_call()
    {
        $this->artisan('ussd:simulate', ['state' => StartState::class])
            ->expectsOutputToContain('Simulating')
            ->expectsOutputToContain('Start')
            ->expectsQuestion('Reply', '1')
            ->expectsOutputToContain('Middle')
            ->expectsQuestion('Reply', '1')
            ->expectsOutputToContain('End')
            ->expectsOutputToContain('[ session ended ]')
            ->expectsQuestion('Press enter to dial again, or type :exit to quit', ':exit')
            ->assertExitCode(0);
    }

    public function test_simulate_command_can_be_exited_mid_call()
    {
        $this->artisan('ussd:simulate', ['state' => StartState::class])
            ->expectsQuestion('Reply', ':exit')
            ->assertExitCode(0);
    }

    public function test_simulate_command_can_restart_the_call_without_ending_the_session()
    {
        $this->artisan('ussd:simulate', ['state' => StartState::class])
            ->expectsQuestion('Reply', ':restart')
            ->expectsOutputToContain('Call dropped.')
            ->expectsOutputToContain('Start')
            ->expectsQuestion('Reply', ':exit')
            ->assertExitCode(0);
    }

    public function test_simulate_command_forces_the_raw_response_even_when_a_configurator_overrides_it()
    {
        $this->artisan('ussd:simulate', [
            'state' => StartState::class,
            '--configurator' => [CogConfigurator::class],
        ])
            ->expectsOutputToContain('Start')
            ->expectsQuestion('Reply', ':exit')
            ->assertExitCode(0);
    }

    public function test_simulate_command_previews_the_gateway_payload_when_a_response_is_given()
    {
        $this->artisan('ussd:simulate', [
            'state' => StartState::class,
            '--response' => \Speso\Ussd\Responses\AfricasTalkingResponse::class,
        ])
            ->expectsOutputToContain('Gateway payload:')
            ->expectsQuestion('Reply', ':exit')
            ->assertExitCode(0);
    }

    public function test_simulate_command_errors_when_the_state_class_does_not_exist()
    {
        $this->artisan('ussd:simulate', ['state' => 'App\\Ussd\\States\\DoesNotExist'])
            ->expectsOutputToContain('does not exist')
            ->assertExitCode(1);
    }

    public function test_simulate_command_errors_on_an_invalid_continuing_mode()
    {
        $this->artisan('ussd:simulate', ['state' => StartState::class, '--continuing-mode' => 'bogus'])
            ->expectsOutputToContain('Invalid --continuing-mode')
            ->assertExitCode(1);
    }

    public function test_simulate_command_errors_when_confirm_mode_is_missing_a_continuing_state()
    {
        $this->artisan('ussd:simulate', ['state' => StartState::class, '--continuing-mode' => 'confirm'])
            ->expectsOutputToContain('--continuing-state is required')
            ->assertExitCode(1);
    }

    public function test_simulate_command_accepts_a_continuing_state_in_confirm_mode()
    {
        $this->artisan('ussd:simulate', [
            'state' => StartState::class,
            '--continuing-mode' => 'confirm',
            '--continuing-state' => ContinuingState::class,
        ])
            ->expectsOutputToContain('Start')
            ->expectsQuestion('Reply', ':exit')
            ->assertExitCode(0);
    }
}
