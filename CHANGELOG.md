# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## [2.0.1] - 2018-10-08

### Changed

- PSR-15 `RequestHandlerInterface` can now be piped in to the `Basic` and `Containerized` pipelines
- `Containerized` pipeline now checks that the retrieved value is a PSR-15 `MiddlewareInterface`

## [2.0.0] - 2018-09-18

### Changed

- Replaced `http-interop/http-server-middleware` with `psr/http-server-middleware`
- Upgraded `syberisle/coding-standards`

### Added

- Added [CHANGELOG](CHANGELOG.md), [CONTRIBUTING](CONTRIBUTING.md), and coding standards

## [1.0.0] - 2018-06-20

- Initial release