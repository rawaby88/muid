# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [5.0.0] - 2026-01-18

### Breaking Changes
- Complete package rewrite - no backward compatibility with v4.x
- New namespace structure
- New trait names: `HasMuid`, `HasIntegerMuid`
- New configuration format
- Removed `doctrine/dbal` dependency

### Added
- Multiple generation strategies: `ordered`, `incremental`, `padded`
- `HasMuid` trait for string-based MUIDs (time-sortable)
- `HasIntegerMuid` trait for integer-based MUIDs with virtual prefix
- `ValidMuid` validation rule
- Comprehensive parsing and validation API
- Base62/Base36 configurable encoding
- Signature validation (optional)

### Changed
- Blueprint macros now return proper types (ForeignIdColumnDefinition)
- Configuration structure completely redesigned
- MUID anatomy: prefix + timestamp(8) + random

### Removed
- `MuidService` class (replaced by `MuidFactory`)
- `MuidFacade` (replaced by `Facades\Muid`)
- `Muid` trait (replaced by `HasMuid`)
- `Model` base class
- `muid:make:model` Artisan command
