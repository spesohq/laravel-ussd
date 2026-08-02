# Laravel Ussd

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Build Status][ico-github]][link-github]

Build Ussd (Unstructured Supplementary Service Data) applications with laravel without breaking a sweat.

## Features

- **Menus as classes** — define screens as `State` classes with a fluent `Menu` builder, and route between them declaratively with `#[Transition]` attributes.
- **Built-in decisions** — match input with `Equal`, `Between`, `In`, `Regex`, `IsNumeric` and more out of the box; scaffold custom ones with `ussd:decision`.
- **Conditional branching** — `Action` classes decide the next state at runtime (e.g. after an HTTP call), for flows that can't be expressed as static attributes.
- **Back navigation** — a `#[Back]` attribute with an automatic per-session history stack, no manual bookkeeping.
- **Automatic pagination** — the `WithPagination` trait plus a `#[Paginate]` attribute page long listings without manual bookkeeping.
- **Response truncation** — a `#[Truncate]` attribute caps how many characters a screen returns, so dynamic content can't blow past your gateway's character limit.
- **Resumable sessions** — `useContinuingState()` lets a redial pick back up where a timed-out session left off, silently or after confirming with the user.
- **Configurators** — group and share repeated setup (response format, exception handling, etc.) across controllers.
- **Localized menus** — build menu content from translation files, and persist a session's chosen language across requests.
- **Session records** — a `Record` API (`get`/`set`/`increment`/`decrement`/...) for persisting data during a session, with optional cross-session persistence via `public: true`.
- **Encrypted session data** — store sensitive values (PINs, account numbers) encrypted at rest in the session `Record`.
- **Built-in gateway responses** — ships `Response` classes for AfricasTalking, Nsano, Nalo, Moolre, Arkesel and Speso; scaffold your own for anything else.
- **Exception handling** — implement `ExceptionHandler` to turn an unhandled exception into a message the caller sees, instead of a dead session.
- **Flow visualization** — `ussd:graph` renders a Mermaid state diagram of a flow straight from its attributes.
- **Interactive simulation** — `ussd:simulate` lets you walk a flow in the terminal like a real handset, no gateway or phone required.
- **Flow linting** — `ussd:lint` catches dead ends, broken transitions, duplicate matches and unreachable states before they ship.
- **Testing utilities** — a fluent `Ussd::test()` API for asserting screens, context, and session state across multi-step conversations.
- **Session events** — `StateEntered` and `SessionTerminated` events for logging or analytics, without touching core classes.
- **Artisan generators** — scaffold states, actions, responses, decisions, configurators and exception handlers with commands like `ussd:state` and `ussd:action`.

## Installation

You can install the package via composer:

``` bash
composer require speso/laravel-ussd:^3.0
```

For older version use

``` bash
composer require speso/laravel-ussd:^2.0
```

### Upgrading from `sparors/laravel-ussd`

As of v3.0.0, this package is published as `speso/laravel-ussd` under the `Speso\Ussd` namespace. If you're on an older `sparors/laravel-ussd` install, upgrading requires two steps:

1. Swap the composer dependency:
   ``` bash
   composer remove sparors/laravel-ussd
   composer require speso/laravel-ussd:^3.0
   ```
2. Find and replace `Sparors\Ussd` with `Speso\Ussd` across your application (`use` statements, service provider references, config `vendor:publish` calls, etc.). There are no other API or behavior changes in this release, so no further code changes are required.

## Documentation

See the [documentation][link-business-docs].

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email services@speso.co instead of using the issue tracker.

## Credits

Special thanks to [Isaac Sai][link-isaac-sai] for writing this package.

- [Speso][link-author]
- [All Contributors][link-contributors]

## License

MIT. Please see the [license file](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/speso/laravel-ussd.svg?style=flat-square
[ico-github]: https://img.shields.io/github/actions/workflow/status/spesohq/laravel-ussd/php.yml?style=flat-square

[link-packagist]: https://packagist.org/packages/speso/laravel-ussd
[link-github]: https://github.com/spesohq/laravel-ussd/actions/workflows/php.yml
[link-author]: https://github.com/spesohq
[link-contributors]: ../../contributors
[link-isaac-sai]: https://github.com/CyberSai
[link-business-docs]: https://docs.speso.co/laravel-ussd
