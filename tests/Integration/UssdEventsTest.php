<?php

namespace Speso\Ussd\Tests\Integration;

use Illuminate\Support\Facades\Event;
use Speso\Ussd\Context;
use Speso\Ussd\Events\SessionTerminated;
use Speso\Ussd\Events\StateEntered;
use Speso\Ussd\Tests\Dummy\MiddleState;
use Speso\Ussd\Tests\Dummy\StartState;
use Speso\Ussd\Tests\TestCase;
use Speso\Ussd\Ussd;

final class UssdEventsTest extends TestCase
{
    public function test_state_entered_event_dispatches_for_each_rendered_state()
    {
        Event::fake();

        Ussd::build(Context::create('9876', '7890', '1'))->useInitialState(StartState::class)->run();

        Event::assertDispatched(
            StateEntered::class,
            fn ($event) => StartState::class === $event->state && '9876' === $event->context->uid()
        );

        Ussd::build(Context::create('9876', '7890', '1'))->useInitialState(StartState::class)->run();

        Event::assertDispatched(StateEntered::class, fn ($event) => MiddleState::class === $event->state);
        Event::assertDispatchedTimes(StateEntered::class, 2);
    }

    public function test_session_terminated_event_dispatches_once_when_session_ends()
    {
        Ussd::build(Context::create('9875', '7890', '1'))->useInitialState(StartState::class)->run();
        Ussd::build(Context::create('9875', '7890', '1'))->useInitialState(StartState::class)->run();

        Event::fake();

        Ussd::build(Context::create('9875', '7890', '1'))->useInitialState(StartState::class)->run();

        Event::assertDispatched(
            SessionTerminated::class,
            fn ($event) => '9875' === $event->context->uid()
        );
        Event::assertDispatchedTimes(SessionTerminated::class, 1);
    }

    public function test_session_terminated_event_does_not_dispatch_while_still_running()
    {
        Event::fake();

        Ussd::build(Context::create('9874', '7890', '1'))->useInitialState(StartState::class)->run();

        Event::assertNotDispatched(SessionTerminated::class);
    }

    public function test_session_terminated_event_dispatches_when_an_exception_is_thrown()
    {
        Ussd::build(Context::create('9873', '7890', '1'))->useInitialState(StartState::class)->run();

        Event::fake();

        Ussd::build(Context::create('9873', '7890', 'unmatched'))->useInitialState(StartState::class)->run();

        Event::assertDispatched(SessionTerminated::class);
    }
}
