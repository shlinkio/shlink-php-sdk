# Shlink PHP SDK

[![Build Status](https://img.shields.io/github/workflow/status/shlinkio/shlink-php-sdk/Continuous%20integration/main?logo=github&style=flat-square)](https://github.com/shlinkio/shlink-php-sdk/actions?query=workflow%3A%22Continuous+integration%22)
[![Code Coverage](https://img.shields.io/codecov/c/gh/shlinkio/shlink-php-sdk/main?style=flat-square)](https://app.codecov.io/gh/shlinkio/shlink-php-sdk)
[![Infection MSI](https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fshlinkio%2Fshlink-php-sdk%2Fmain)](https://dashboard.stryker-mutator.io/reports/github.com/shlinkio/shlink-php-sdk/main)
[![Latest Stable Version](https://img.shields.io/github/release/shlinkio/shlink-php-sdk.svg?style=flat-square)](https://packagist.org/packages/shlinkio/shlink-php-sdk)
[![License](https://img.shields.io/github/license/shlinkio/shlink-php-sdk.svg?style=flat-square)](https://github.com/shlinkio/shlink-php-sdk/blob/main/LICENSE)
[![Paypal donate](https://img.shields.io/badge/Donate-paypal-blue.svg?style=flat-square&logo=paypal&colorA=aaaaaa)](https://slnk.to/donate)

A PHP SDK to consume Shlink's REST API in a very convenient and robust way.

* Very expressive API.
* Decoupled from implementations. Depending only on PSR-17 and PSR-18 interfaces.
* Dependency injection. Every service can be composed out of a set of pieces.
* Statically typed and immutable DTOs, with meaningful named constructors.
* Generator-based iterable collections, to abstract pagination and reduce resource consumption.
* Error handling via contextual exceptions.
* Extensively tested. Unit tests, integration tests and mutation testing.

## Installation

Install the SDK with composer.

    composer install shlinkio/shlink-php-sdk

## Docs

Read the documentation ad [https://php-sdk.shlink.io](https://php-sdk.shlink.io)
