# Laravel Ussd

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Build Status][ico-github]][link-github]

Build Ussd (Unstructured Supplementary Service Data) applications with laravel without breaking a sweat.

## Features

- **Menus as classes** — define screens as `State` classes with a fluent `Menu` builder, and route between them declaratively with `#[Transition]` attributes.
- **Conditional branching** — `Action` classes decide the next state at runtime (e.g. after an HTTP call), for flows that can't be expressed as static attributes.
- **Back navigation** — a `#[Back]` attribute with an automatic per-session history stack, no manual bookkeeping.
- **Configurators** — group and share repeated setup (response format, exception handling, etc.) across controllers.
- **Localized menus** — build menu content from translation files, and persist a session's chosen language across requests.
- **Encrypted session data** — store sensitive values (PINs, account numbers) encrypted at rest in the session `Record`.
- **Built-in gateway responses** — ships `Response` classes for AfricasTalking, Nsano, Nalo, Moolre and Arkesel; scaffold your own for anything else.
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

## Usage

- Using v3 (current)? See the [V3 README](./v3.readme.md).
- Using v2? See the [V2 README](./v2.readme.md).

## Documentation

You'll find the V3 documentation in the [V3 README](./v3.readme.md) or the [GitHub wiki][link-wiki]. Archived V2 documentation is available in the [V2 README](./v2.readme.md).

A browsable version of these docs is also available on the [Speso Business API docs site][link-business-docs].

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email services@speso.co instead of using the issue tracker.

## Credits

- [Speso][link-author]
- [All Contributors][link-contributors]

Special thanks to [Isaac Sai][link-isaac-sai] for writing this package.

## License

MIT. Please see the [license file](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/speso/laravel-ussd.svg?style=flat-square
[ico-github]: https://img.shields.io/github/actions/workflow/status/spesohq/laravel-ussd/php.yml?style=flat-square

[link-packagist]: https://packagist.org/packages/speso/laravel-ussd
[link-github]: https://github.com/spesohq/laravel-ussd/actions/workflows/php.yml
[link-wiki]: https://github.com/spesohq/laravel-ussd/wiki
[link-author]: https://github.com/spesohq
[link-contributors]: ../../contributors
[link-isaac-sai]: https://github.com/CyberSai
[link-business-docs]: https://business.speso.co/docs/laravel-ussd
