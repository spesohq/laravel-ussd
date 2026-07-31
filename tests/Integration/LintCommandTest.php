<?php

namespace Speso\Ussd\Tests\Integration;

use Speso\Ussd\Tests\Dummy\Lint\BackOnly;
use Speso\Ussd\Tests\Dummy\Lint\GoodStart;
use Speso\Ussd\Tests\Dummy\StartState;
use Speso\Ussd\Tests\TestCase;

final class LintCommandTest extends TestCase
{
    public function test_lint_command_reports_no_issues_for_a_clean_flow()
    {
        $this->artisan('ussd:lint', ['states' => [StartState::class]])
            ->expectsOutputToContain('No issues found across 3 reachable state(s).')
            ->assertExitCode(0);
    }

    public function test_lint_command_errors_when_a_given_root_class_does_not_exist()
    {
        $this->artisan('ussd:lint', ['states' => ['App\\Ussd\\States\\DoesNotExist']])
            ->expectsOutputToContain('does not exist')
            ->assertExitCode(1);
    }

    public function test_lint_command_flags_a_state_with_no_way_out_as_an_error()
    {
        $this->artisan('ussd:lint', ['states' => [GoodStart::class]])
            ->expectsOutputToContain('Errors:')
            ->expectsOutputToContain('Has no #[Transition], #[Back] or #[Terminate] attribute')
            ->assertExitCode(1);
    }

    public function test_lint_command_flags_a_back_only_state_as_a_warning()
    {
        $this->artisan('ussd:lint', ['states' => [GoodStart::class]])
            ->expectsOutputToContain('Warnings:')
            ->expectsOutputToContain('Only has a #[Back] attribute')
            ->assertExitCode(1);
    }

    public function test_lint_command_flags_a_broken_transition_target_as_an_error()
    {
        $this->artisan('ussd:lint', ['states' => [GoodStart::class]])
            ->expectsOutputToContain('Speso\\Ussd\\Tests\\Dummy\\Lint\\NoSuchClass] does not exist')
            ->assertExitCode(1);
    }

    public function test_lint_command_flags_duplicate_transition_matches_as_a_warning()
    {
        $this->artisan('ussd:lint', ['states' => [GoodStart::class]])
            ->expectsOutputToContain("Duplicate #[Transition] match [Equal(1)]")
            ->assertExitCode(1);
    }

    public function test_lint_command_discovers_roots_and_unreachable_states_from_the_configured_namespace()
    {
        config(['ussd.namespace' => 'Speso\\Ussd\\Tests\\Dummy\\Lint']);

        $this->artisan('ussd:lint')
            ->expectsOutputToContain('Orphan: Not reached from the given root state(s)')
            ->assertExitCode(1);
    }

    public function test_lint_command_does_not_flag_a_continue_state_as_unreachable()
    {
        config(['ussd.namespace' => 'Speso\\Ussd\\Tests\\Dummy\\Lint']);

        $this->artisan('ussd:lint')
            ->doesntExpectOutputToContain('ContinueOnly')
            ->assertExitCode(1);
    }

    public function test_lint_command_errors_when_no_roots_are_given_or_discovered()
    {
        config(['ussd.namespace' => 'App\\Ussd\\Nowhere']);

        $this->artisan('ussd:lint')
            ->expectsOutputToContain('No root states were given')
            ->assertExitCode(1);
    }

    public function test_a_warning_only_flow_exits_cleanly_without_strict()
    {
        $this->artisan('ussd:lint', ['states' => [BackOnly::class]])
            ->expectsOutputToContain('Only has a #[Back] attribute')
            ->assertExitCode(0);
    }

    public function test_strict_option_fails_on_warnings_alone()
    {
        $this->artisan('ussd:lint', ['states' => [BackOnly::class], '--strict' => true])
            ->assertExitCode(1);
    }
}
