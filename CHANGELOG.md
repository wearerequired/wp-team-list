# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## Added
* Block for the block editor to insert a team list in any post type.

## Fixed
* PHP warning when saving a widget.

## Changed
* Refactoring by using PHP namespaces.
* Bumped WordPress minimum requirement to 5.0.
* Bumped PHP minimum requirement to 5.6.

## Deprecated
* `rplus_wp_team_list_default_classes` filter, use `wp_team_list_default_classes`.

## Removed
* `rplus_wp_team_list()` and `rplus_wp_team_list_classes()`.

## [2.0.1]
### Fixed
* Removed HTML output on profile page.
* Removed type hint for shortcode attributes to avoid a PHP fatal error.
* Correct `author` CSS classes.

### Changed
* Improved plugin description.
* Translations moved to https://translate.wordpress.org/projects/wp-plugins/wp-team-list.
* Added deprecation notice and removed shortcode UI for `[rplus_team_list]` shortcode in favour of the new `[wp_team_list]` shortcode.

## [2.0.0]
### Fixed
* Smaller corrections in the widget.

### Changed
* Improved documentation.
* Simplified template loading.
* Filterable user roles, making it easier to disable output.

### Deprecated
* `rplus_team_list` shortcode, use `wp_team_list`.
* `rplus_wp_team_list()`, use ` wp_team_list()->render_team_list()`.
* `rplus_wp_team_list_classes()`, use `wp_team_list()->item_classes()`.

## [1.1.2]
### Fixed
* A small error in the previous release.

## [1.1.1]
### Changed
* Allows role 'All' in the widget to display users with any role.

## [1.1.0]
### Fixed
* Corrects tylesheet URL.

### Changed
* Support multiple roles in the shortcode (comma-separated).
* Enable only displaying users with specific IDs (`include` shortcode attribute).
* Allow querying by users which have published posts (`has_published_posts` shortcode attribute).

## [1.0.5]
### Fixed
* Make 'Order By' string translatable.

### Changed
* Lots of improvements under the hood.
* 100% compatible with WordPress 4.3.

## [1.0.4]
### Fixed
* Support ordering by `last_name` and `first_name` columns.

## [1.0.3]
### Fixed
* Properly translate the link title attributes

## [1.0.2]
### Fixed
* Correctly translate the user roles

### Added
* German (Switzerland) translation (de_CH)

## [1.0.1]
### Fixed
* Updated textdomain to match the plugin slug

## 1.0.0
### Added
* Initial Release

[Unreleased]: https://github.com/wearerequired/wp-team-list/compare/2.0.1...HEAD
[2.0.1]: https://github.com/wearerequired/wp-team-list/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/wearerequired/wp-team-list/compare/1.1.3...2.0.0
[1.1.2]: https://github.com/wearerequired/wp-team-list/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/wearerequired/wp-team-list/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/wearerequired/wp-team-list/compare/1.0.5...1.1.0
[1.0.5]: https://github.com/wearerequired/wp-team-list/compare/1.0.4...1.0.5
[1.0.4]: https://github.com/wearerequired/wp-team-list/compare/1.0.3...1.0.4
[1.0.3]: https://github.com/wearerequired/wp-team-list/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/wearerequired/wp-team-list/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/wearerequired/wp-team-list/compare/1.0.0...1.0.1
