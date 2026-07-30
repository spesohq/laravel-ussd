<?php

namespace Speso\Ussd\Tests\Integration;

use Speso\Ussd\Tests\Dummy\BeginningState;
use Speso\Ussd\Tests\Dummy\EndState;
use Speso\Ussd\Tests\Dummy\MiddleState;
use Speso\Ussd\Tests\Dummy\PetitAction;
use Speso\Ussd\Tests\Dummy\StartState;
use Speso\Ussd\Tests\TestCase;

final class GraphCommandTest extends TestCase
{
    public function test_graph_command_renders_a_diagram_for_a_linear_flow()
    {
        $this->artisan('ussd:graph', ['state' => StartState::class])
            ->expectsOutputToContain('stateDiagram-v2')
            ->expectsOutputToContain(sprintf('%s : %s', $this->nodeId(StartState::class), 'StartState'))
            ->expectsOutputToContain(sprintf(
                '%s --> %s : Equal(1)',
                $this->nodeId(StartState::class),
                $this->nodeId(MiddleState::class)
            ))
            ->expectsOutputToContain(sprintf('%s --> [*]', $this->nodeId(EndState::class)))
            ->assertExitCode(0);
    }

    public function test_graph_command_renders_a_back_edge_to_the_history_node()
    {
        $this->artisan('ussd:graph', ['state' => StartState::class])
            ->expectsOutputToContain(sprintf('%s --> __history__ : Equal(0)', $this->nodeId(MiddleState::class)))
            ->expectsOutputToContain('__history__ : Previous state')
            ->assertExitCode(0);
    }

    public function test_graph_command_labels_a_non_state_transition_target_as_dynamic()
    {
        $this->artisan('ussd:graph', ['state' => BeginningState::class])
            ->expectsOutputToContain(sprintf('%s : PetitAction (dynamic)', $this->nodeId(PetitAction::class)))
            ->assertExitCode(0);
    }

    public function test_graph_command_writes_the_diagram_to_a_file_when_output_option_given()
    {
        $path = tempnam(sys_get_temp_dir(), 'ussd-graph');

        $this->artisan('ussd:graph', ['state' => StartState::class, '--output' => $path])
            ->expectsOutputToContain("Diagram written to {$path}")
            ->assertExitCode(0);

        $this->assertStringContainsString('stateDiagram-v2', file_get_contents($path));

        unlink($path);
    }

    public function test_graph_command_errors_when_the_class_does_not_exist()
    {
        $this->artisan('ussd:graph', ['state' => 'App\\Ussd\\States\\DoesNotExist'])
            ->expectsOutputToContain('does not exist')
            ->assertExitCode(1);
    }

    private function nodeId(string $class): string
    {
        return str_replace('\\', '_', $class);
    }
}
