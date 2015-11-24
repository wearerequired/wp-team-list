# WP Team List #
* Contributors: wearerequired, swissspidy, neverything, hubersen
* Donate link: http://required.ch/
* Tags: authors, widget, users, list, team, shortcode
* Requires at least: 4.2.0
* Tested up to: 4.4
* Stable tag: 2.0.0
* License: GPLv2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display your teammates anywhere on your WordPress site using this easy-to-use plugin.

## Description ##

WP Team List is a plugin that helps you to create a simple team site using your WordPress users with various display options. Either use a shortcode, a template tag or the built-in widget to display blog authors.

After creating similar functionality for a couple of clients, we decided to roll our knowledge into this simple plugin. It's really straightforward to use:

First of all, the plugin adds a small checkbox to the user profile in WordPress to toggle its visibility in the team list. This means you can decide for every user whether he should show up in the team list or not.

You can then use one of these ways to display the list anywhere on your site.

**Action**

Use the `wp_team_list` hook to directly display the users in your theme or plugin.

For example, you can show users of any role ordered by their name:

```
<?php
echo do_action( 'wp_team_list', array( 'role' => 'all', 'orderby' => 'name' ) );
?>
```

**Note:** WP Team List supports many of the arguments [`WP_User_Query`](http://codex.wordpress.org/Class_Reference/WP_User_Query "WordPress Codex Codex WP_User_Query") supports.  

**Shortcode**

Use the `[wp_team_list]` shortcode to display a team list in your posts. Supported arguments:

* `role` - Filter users by roles (comma-separated).  
 Use `all` to show users with any role.  
 **Default:** `administrator`
* `orderby`  
 **Default:** `post_count`
* `order` - Either `asc` or `desc`.  
 **Default:** `desc`.
* `include` - Filter users with specific IDs (comma-separated).
* `has_published_posts` - Filter users with published posts.  
 Either a comma-separated list of post types or `true` to filter by all post types.

Example:

`[wp_team_list role="author" orderby="last_name" order="desc"]`

**Pro tip:** If you use the [Shortcake WordPress plugin](https://github.com/fusioneng/Shortcake "GitHub - Shortcake"), you'll get an inline preview of the shortcode right in the visual editor. You can also add the shortcode with the click of a button.

**Widget**

Want do display the team members in your sidebar? Use the built-in WordPress widget. You can set the role you want, the number of users to show and even link to a separate team page.

## Installation ##

1. Upload `wp-team-list` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Use one of the supported methods (hook, shortcode, or widget) to display a team list.
4. **Optional:** Set the visibility status of your users in their profiles.  

## Frequently Asked Questions ##

### How do I contribute to WP Team List? ###

Easy! You can report bugs, with reproduction steps, or post patches on [GitHub](https://github.com/wearerequired/rplus-wp-team-list).

### What kind of filters / hooks are available? ###
* `wp_team_list_user_role` - Filter the user role displayed in the team list.
* `wp_team_list_query_args` - Filter the `WP_User_Query` arguments.
* `wp_team_list_template` - Change the team list template.
 Note: You can also put a `rplus-wp-team-list.php` file in your theme folder.
* `wp_team_list_avatar_size` - Filter the default avatar size.

## Screenshots ##

1. This is how your team list can look like with some additional CSS.
2. The plugin only provides limited styling. It's up to you to adjust it.
3. The configuration options of the built-in widget.

## Changelog ##

### 2.0.0 ###
* Fix: Smaller corrections in the widget.
* Enhancement: Improved documentation.
* Enhancement: Simplified template loading.
* Enhancement: Filterable user roles, making it easier to disable output.

### 1.1.2 ###
* Fix: A small error in the previous release.

### 1.1.1 ###
* Enhancement: Allows role 'All' in the widget to display users with any role.

### 1.1.0 ###
* Fix: Correct stylesheet URL.
* Enhancement: Support multiple roles in the shortcode (comma-separated).
* Enhancement: Enable only displaying users with specific IDs (? include` shortcode attribute).
* Enhancement: Allow querying by users which have published posts (`has_published_posts` shortcode attribute).

### 1.0.5 ###
* Fix: Make 'Order By' string translatable.
* Enhancement: Lots of improvements under the hood.
* 100% compatible with WordPress 4.3.

### 1.0.4 ###
* Fix: Support ordering by `last_name` and `first_name` columns.

### 1.0.3 ###
* Successfully tested with WordPress 4.2
* Fix: Properly translate the link title attributes

### 1.0.2 ###
* Fix: Correctly translate the user roles
* New: Added German (Switzerland) translation (de_CH)

### 1.0.1 ###
* Fix: Updated textdomain to match the plugin slug

### 1.0.0 ###
* Initial Release

## Upgrade Notice ##

### 2.0.0 ###
Major rewrite with some deprecated stuff. Make sure to test first!

### 1.1.2 ###
Fixes a small bug in the previous release.

### 1.1.1 ###
Allows role 'All' in the widget to display users with any role.

### 1.1.0 ###
Supports multiple user roles und listing only specific users.

### 1.0.5 ###
Some small improvements under the hood. Also, 100% compatible with WordPress 4.3.

### 1.0.4 ###
Supports ordering by last and first names.

### 1.0.3 ###

This update includes a small translation fix in the templates.

### 1.0.2 ###

Thanks for using our plugin! This update properly translates user roles. de_CH translation included.

### 1.0.1 ###
We changed the plugin's textdomain to improve translation handling.

### 1.0.0 ###
Initial Release