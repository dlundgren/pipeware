# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) 
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.0.0] - 2019-06-17
### Changed
- Added PSR ContainerInterface type hint to the Containerized pipeline

## [2.0.3] - 2019-05-13
### Changed
- Added the ability for the `Processor` to be cloneable

## [2.0.2] - 2018-10-08
### Changed
- Made `Containerized` more amenable to extension
- Fixed a bug with the `IsPipeline::handleStage` not handling strings
- Fixed a bug where `Containerized` wasn't converting PSR-15 `RequestHandlerInterface` to `MiddlewareInterface` properly

## [2.0.1] - 2018-10-08
### Changed
- PSR-15 `RequestHandlerInterface` can now be piped in to the `Basic` and `Containerized` pipelines
- `Containerized` pipeline now checks that the retrieved value is a PSR-15 `MiddlewareInterface`

## [2.0.0] - 2018-09-18
### Added
- Added [CHANGELOG](CHANGELOG.md), [CONTRIBUTING](CONTRIBUTING.md), and coding standards

### Changed
- Replaced `http-interop/http-server-middleware` with `psr/http-server-middleware`
- Upgraded `syberisle/coding-standards`

## [1.0.0] - 2018-06-20

- Initial release