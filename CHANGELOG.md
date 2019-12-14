# Snitch Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 3.0.1 - 2019.12.13
### Fixed
- Issue with calling `$user->getIsGuest()` from plugin `init` (before app is fully bootstrapped)

## 3.0.0 - 2019.06.02
### Added
- Config `messageTemplate` for the warning message. This is parsed as twig. Default value `'May also be edited by: <a href="mailto:{{user.email}}">{{user.username}}</a>.'`
- `collidingUsers()` service and `collisionMessages()` service

### Removed
- Config `message`. Use the new, more flexible `messageTemplate`
- `userData()` service

## 2.1.2 - 2019.05.15
### Fixed
- Fixed case issue with Snitch.js vs snitch.js (and css)

## 2.1.1 - 2019.05.14
### Fixed
- Rewrote the javascript polling to better serialize things. Now using jQuery queues.

## 2.1.0 - 2019.05.06
### Added
- Ability to find conflicts with Fields as well as Elements

## 2.0.3 - 2019.03.29
### Fixed
- Fix Javascript errors that prevented plugin from working with [Two-Factor Authentication](https://plugins.craftcms.com/two-factor-authentication)

## 2.0.2 - 2018.07.16
### Fixed
- Fix PHP Warning: session_start(): Unable to clear session lock record.

## 2.0.1 - 2018.03.08
### Fixed
- Case issue causing `Class marionnewlevant\snitch\assetbundles\Snitch\SnitchAsset does not exist` error

## 2.0.0 - 2017.03.14
### Added
- Initial release for Craft 3
