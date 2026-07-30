<?php

namespace Speso\Ussd\Traits;

use Closure;
use DateInterval;
use DateTimeInterface;
use Illuminate\Support\Facades\App;
use Speso\Ussd\Context;
use Speso\Ussd\ContinuingMode;
use Speso\Ussd\Contracts\Configurator;
use Speso\Ussd\Contracts\ContinueState;
use Speso\Ussd\Contracts\ExceptionHandler;
use Speso\Ussd\Contracts\InitialAction;
use Speso\Ussd\Contracts\InitialState;
use Speso\Ussd\Contracts\Response;
use Speso\Ussd\Exceptions\InvalidConfiguratorException;
use Speso\Ussd\Exceptions\InvalidContinueStateException;
use Speso\Ussd\Exceptions\InvalidContinuingModeException;
use Speso\Ussd\Exceptions\InvalidExceptionHandlerException;
use Speso\Ussd\Exceptions\InvalidInitialStateException;
use Speso\Ussd\Exceptions\InvalidResponseException;

trait UssdBuilder
{
    public function useContext(Context $context): static
    {
        $this->context = $context;

        return $this;
    }

    public function useConfigurator(Configurator|string $configurator): static
    {
        if (is_string($configurator) && class_exists($configurator)) {
            $configurator = App::make($configurator);
        }

        throw_unless(
            $configurator instanceof Configurator,
            InvalidConfiguratorException::class,
            $configurator::class
        );

        $configurator->configure($this);

        return $this;
    }

    public function useInitialState(InitialAction|InitialState|string $initialState): static
    {
        if (is_string($initialState) && class_exists($initialState)) {
            $initialState = App::make($initialState);
        }

        throw_unless(
            $initialState instanceof InitialState || $initialState instanceof InitialAction,
            InvalidInitialStateException::class,
            $initialState::class
        );

        $this->initialState = $initialState;

        return $this;
    }

    public function useContinuingState(
        int $continuingMode,
        null|DateInterval|DateTimeInterface|int $continuingTtl,
        null|ContinueState|string $continuingState = null
    ): static {
        if (is_string($continuingState) && class_exists($continuingState)) {
            $continuingState = App::make($continuingState);
        }

        throw_unless(
            in_array($continuingMode, [ContinuingMode::START, ContinuingMode::CONTINUE, ContinuingMode::CONFIRM], true),
            InvalidContinuingModeException::class,
            $continuingMode
        );

        $this->continuingMode = $continuingMode;
        $this->continuingTtl = $continuingTtl;

        if (ContinuingMode::CONFIRM === $continuingMode) {
            throw_unless(
                $continuingState instanceof ContinueState,
                InvalidContinueStateException::class,
                isset($continuingState) ? $continuingState::class : null
            );
        }

        $this->continuingState = $continuingState;

        return $this;
    }

    public function useResponse(Closure|Response|string $response): static
    {
        if (is_string($response) && class_exists($response)) {
            $response = App::make($response);
        }

        throw_unless(
            $response instanceof Response || $response instanceof Closure,
            InvalidResponseException::class,
            $response::class
        );

        $this->response = $response;

        return $this;
    }

    public function useExceptionHandler(Closure|ExceptionHandler|string $exceptionHandler): static
    {
        if (is_string($exceptionHandler) && class_exists($exceptionHandler)) {
            $exceptionHandler = App::make($exceptionHandler);
        }

        throw_unless(
            $exceptionHandler instanceof ExceptionHandler || $exceptionHandler instanceof Closure,
            InvalidExceptionHandlerException::class,
            $exceptionHandler::class
        );

        $this->exceptionHandler = $exceptionHandler;

        return $this;
    }

    public function useStore(string $storeName): static
    {
        $this->storeName = $storeName;

        return $this;
    }
}
