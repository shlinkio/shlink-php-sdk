# Shlink PHP SDK

[![Build Status](https://img.shields.io/github/actions/workflow/status/shlinkio/shlink-php-sdk/ci.yml?branch=main&logo=github&style=flat-square)](https://github.com/shlinkio/shlink-php-sdk/actions/workflows/ci.yml)
[![Code Coverage](https://img.shields.io/codecov/c/gh/shlinkio/shlink-php-sdk/main?style=flat-square)](https://app.codecov.io/gh/shlinkio/shlink-php-sdk)
[![Latest Stable Version](https://img.shields.io/github/release/shlinkio/shlink-php-sdk.svg?style=flat-square)](https://packagist.org/packages/shlinkio/shlink-php-sdk)
[![License](https://img.shields.io/github/license/shlinkio/shlink-php-sdk.svg?style=flat-square)](https://github.com/shlinkio/shlink-php-sdk/blob/main/LICENSE)
[![Paypal donate](https://img.shields.io/badge/Donate-paypal-blue.svg?style=flat-square&logo=paypal&colorA=aaaaaa)](https://slnk.to/donate)

A PHP SDK to consume [Shlink](https://shlink.io)'s REST API in a very convenient and robust way.

* Very expressive API.
* Decoupled from implementations: Depending only on [PSR-17](https://www.php-fig.org/psr/psr-17/) and [PSR-18](https://www.php-fig.org/psr/psr-18/) interfaces.
* Dependency injection: Every service can be composed out of a set of pieces.
* Statically typed and immutable DTOs, with meaningful named constructors.
* Generator-based iterable collections, to abstract pagination and reduce resource consumption.
* Error handling via contextual exceptions.
* Extensively tested with unit tests and integration tests.

## Installation

Install the SDK with composer.

    composer install shlinkio/shlink-php-sdk
