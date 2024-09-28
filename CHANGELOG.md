# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- This CHANGELOG file to hopefully serve as an evolving example of a
  standardized open source project CHANGELOG.
- Implemented Bitrix24 API integration
    - Added HTTP files for API requests in PhpStorm
    - Created PHP script with cURL implementation for API requests
    - Created PHP script using Bitrix24 PHP SDK
- Set up project dependencies
    - Added `composer.json` file
    - Generated `composer.lock` file
- Configured Monolog for SDK logging
    - Integrated Monolog library for improved logging capabilities
    - Set up basic logging configuration for Bitrix24 SDK
- `.gitignore` file
- `Dockerfile` file
