# Changelog

All notable changes to `laravel Ussd` will be documented in this file.

## [Unreleased]
### Added
- Add `Menu::trans()`, `Menu::transLine()` and `Menu::transChoice()` for building menu content from translation files.
- Add `Record::locale()` and `Record::setLocale()` to persist a session's chosen locale; `Ussd` applies it automatically, including within the same request when a `Transition` or `Action` callback sets it (so the very next screen renders in the new locale), and resets to the application's configured default locale for sessions that haven't set one, so a locale never leaks from one session into another in long-running processes (e.g. Octane, queue workers).
- Add a `#[Back]` attribute for back navigation. `Ussd` maintains a per-session history stack, pushing the outgoing state on every `Transition` and popping it on a matching `Back`.
- Add a `ussd:graph` artisan command that renders a Mermaid state diagram of a flow's `Transition`, `Back` and `Terminate` attributes.
- Add built-in `Response` implementations for common gateways: `Speso\Ussd\Responses\AfricasTalkingResponse` (plain-text `CON`/`END`), `Speso\Ussd\Responses\NsanoResponse` (`USSDResp` JSON), `Speso\Ussd\Responses\NaloResponse` (`USERID`/`MSISDN`/`USERDATA`/`MSG`/`MSGTYPE` JSON), `Speso\Ussd\Responses\MoolreResponse` (`message`/`reply` JSON) and `Speso\Ussd\Responses\ArkeselResponse` (`sessionID`/`userID`/`msisdn`/`message`/`continueSession` JSON).
- Add `Speso\Ussd\Events\StateEntered` and `Speso\Ussd\Events\SessionTerminated`, dispatched on every rendered state and once when a session ends (including via an unhandled exception), for logging or analytics listeners.
- Add `Record::setEncrypted()` and `Record::getEncrypted()` to encrypt sensitive values (PINs, account numbers) at rest using the application's encryption key, instead of storing them as plain values in the cache store.
- Add a `ussd:simulate` artisan command that runs a flow interactively in the terminal like a real handset, so it can be walked through without a gateway, controller, or phone. Supports `--phone`, `--with`, `--configurator`, `--response` (to preview the gateway-formatted payload), `--continuing-mode`/`--continuing-state`/`--continuing-ttl` and `--store`, plus a `:restart` command at the reply prompt to simulate a dropped call for exercising `ContinuingMode::CONTINUE`/`CONFIRM`.
- Add a `ussd:lint` artisan command that walks a flow's `Transition`, `Back` and `Terminate` attributes and reports dead ends (a state with no way out), broken `Transition` targets (a nonexistent class, or one that's neither a `State` nor an `Action`), duplicate `Transition` matches on the same state (only the first can ever trigger), and states under `config('ussd.namespace')` that aren't reached from any given root. Accepts one or more root state classes, or auto-discovers every `InitialState`/`InitialAction` in the configured namespace when none are given; `--strict` exits non-zero on warnings too, for CI.

### Fixed
- Fix the readme's Nsano configurator example, which referenced an undefined `$termination` variable and a nonexistent `setResponse()` method (the real method is `useResponse()`).

## [v3.0.0] - 2026-07-30
### Changed
- Renamed package from `sparors/laravel-ussd` to `speso/laravel-ussd`.
- Renamed namespace from `Sparors\Ussd` to `Speso\Ussd`. See the readme for migration steps.

### Added
- Add Laravel 13 support.

## [v3.0.0-beta.3] - 2025-03-20
### Added
- Add Laravel 12 support.

## [v3.0.0-beta.2] - 2025-02-02
### Added
- Add Laravel 11 support.


## [v3.0.0-beta.1] - 2024-01-21
### Removed
- Removed machine in favor of USSD facade.

### Changed
- Changed state interface.
- Changed record implementation and public apis.
- Changed config variables

### Added
- Added `Transition`, `Paginate`, `Truncate` and `Terminate` Attributes.
- Added Custom Exception Handling.
- Added command to create responses, exception handlers and decisions.
- Added decision classes for navigating USSD menus.
- Added testing utility to Ussd Facade.
- Added pagination utility.
- Added resumability of timed-out sessions.
- Added interfaces for decision, exception handler, response, initial state and initial action.
- Added support for dependency injection.
- Added USSD context.

## [v2.5.0] - 2022-06-19
### Added
- Add configuring USSDs using decorator pattern.

## [v2.4.2] - 2022-06-12
### Changed
- Change Action class `setRecord` method to return `$this`

## [v2.4.1] - 2022-03-31
### Fixed
- Clean up

## [v2.4.0] - 2022-02-22
### Added
- Add Laravel 9 support
- Add PHP 8.1 support

## [v2.3.1] - 2021-10-15
### Fixed
- Coding style

## [v2.3.0] - 2021-06-27
### Added
- Add missen test to improve coverage
### Change
- Minor bug fixes
- Upgrade test dependencies


## [v2.2.0] - 2020-09-11
### Added
- Add Support for laravel 8

## [v2.1.0] - 2020-05-26
### Change
- Initial State method of machine can accept action
- All internal private methods to protected

## [v2.0.0] - 2020-05-24
### Added
- Action class to run application logics
- Artisan command to create action class
- increment method to records
- decrement method to records

### Changed
- config file class namespace split to action and state namespace
- Updated changelog
- Readme
- machine class now runs ussd actions
- Updated contributing

## [v1.0.0] - 2020-05-12
### Added
- More test
- Machine SetInitialState can take callable
### Changed
- State type changed to action
- License File changed to github format
- Updated Readme
- Updated changelog

## [v0.1.0] - 2020-05-02
### Added
- Ussd Package Project with README, contributing, changelog, license, etc.
- State class to define what should occur at various stages when user navigates
- Artisan command to create State classes
- Machine class to run all linked States
- HasManipulators traits to help Machine Class with common functions
- Menu class to be used create user menus in the various states
- Decision class to decide on how to link the various states after accepting user's input
- Record class to save data
- Ussd Class to provide access other classes
- Ussd facade to proxy to the Ussd class
- Ussd config to allow developers customize behaviour
- Ussd service Provider class to allow laravel know how to integrate the package

[Unreleased]: ../../compare/v3.0.0...HEAD
[v3.0.0]: ../../compare/v3.0.0-beta.3...v3.0.0
[v3.0.0-beta.1]: ../../compare/v2.5.0...v3.0.0-beta.1
[v2.5.0]: ../../compare/v2.4.2...v2.5.0
[v2.4.2]: ../../compare/v2.4.1...v2.4.2
[v2.4.1]: ../../compare/v2.4.0...v2.4.1
[v2.4.0]: ../../compare/v2.3.1...v2.4.0
[v2.3.1]: ../../compare/v2.3.0...v2.3.1
[v2.3.0]: ../../compare/v2.2.0...v2.3.0
[v2.2.0]: ../../compare/v2.1.0...v2.2.0
[v2.1.0]: ../../compare/v2.0.0...v2.1.0
[v2.0.0]: ../../compare/v1.0.0...v2.0.0
[v1.0.0]: ../../compare/v0.1.0...v1.0.0
[v0.1.0]: ../../releases/tag/v0.1.0
