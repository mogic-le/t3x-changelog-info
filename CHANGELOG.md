# Changelog

All notable changes for the TYPO3 CHANGELOG-Info extension will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [unreleased]

### Fixed

- Show error message when changelog file cannot be found, instead of crashing
- Support Jira ticket keys with digits before the dash: K23-42

### Changed

- Improve README
- Make module available via "Web > Info > Changelog information" instead
  of own main backend module.
- Change extension icon
- Use michelf/php-markdown instead of fluidtypo3/vhs for rendering

## [1.1.1] - 2024-12-16

### Fixed

- Rename readme

## [1.1.0] - 2024-12-16

### Added

- Github action to release

## [1.0.0] - 2024-12-16

### Added

- Base module

### Changed

- Render view to module template
