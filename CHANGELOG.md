# CHANGELOG

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com), and this project adheres to [Semantic Versioning](https://semver.org).

## [Unreleased]
### Added
* [#23](https://github.com/shlinkio/shlink-php-client/issues/23) Added support for API v3.

### Changed
* Migrated from prophecy to PHPUnit mocks.

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [1.0.0] - 2022-10-01
### Added
* [#15](https://github.com/shlinkio/shlink-php-client/issues/15) Added support for pagination in tags, including filters and ordering.
* [#16](https://github.com/shlinkio/shlink-php-client/issues/16) Added support for new `/tags/stats` endpoint introduced in Shlink 3.0.0.
* [#19](https://github.com/shlinkio/shlink-php-client/issues/19) Added support for `/domain/{domain}/visits` endpoint introduced in Shlink 3.1.0.
* [#21](https://github.com/shlinkio/shlink-php-client/issues/21) Added support for `/visits/non-orphan` endpoint introduced in Shlink 3.0.0.
* [#5](https://github.com/shlinkio/shlink-php-client/issues/5) Added library documentation using [docsify](https://docsify.js.org/).

### Changed
* [#7](https://github.com/shlinkio/shlink-php-client/issues/7) Added new integration tests suite.

### Deprecated
* *Nothing*

### Removed
* [#18](https://github.com/shlinkio/shlink-php-client/issues/18) Dropped support for PHP 8.0.
* Dropped support for Shlink previous to v3.0.0. Mostly everything will continue working, but issues won't be addressed if only affecting older versions.

### Fixed
* *Nothing*


## [0.2.0] - 2022-01-10
### Added
* [#12](https://github.com/shlinkio/shlink-php-client/issues/12) Created `ShlinkClientBuilder` and `SingletonShlinkClientBuilder`, which can be used to create client instances at runtime.

### Changed
* Updated to infection 0.26, enabling HTML reports.
* Added explicitly enabled composer plugins to composer.json.

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [0.1.0] - 2021-12-04
### Added
* First release

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*
