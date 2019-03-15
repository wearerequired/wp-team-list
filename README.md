# Team List #
* Contributors: wearerequired, swissspidy, neverything, hubersen, ocean90
* Tags: authors, widget, users, list, team, shortcode
* Requires at least: 5.0
* Tested up to: 5.1
* Requires PHP: 5.6
* Stable tag: 2.0.1
* License: GPLv2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display your teammates anywhere on your WordPress site using this easy-to-use plugin.

## Description ##

Team List is a plugin that helps you to create a simple team site using your WordPress users with various display options. Either use a block, a shortcode, a template tag or the built-in widget to display blog authors.

After creating similar functionality for a couple of clients, we decided to roll our knowledge into this simple plugin. It's really straightforward to use:

First of all, the plugin adds a small checkbox to the user profile in WordPress to toggle its visibility in the team list. This means you can decide for every user whether he should show up in the team list or not.

You can then use one of these ways to display the list anywhere on your site.

**Action**

Use the `wp_team_list` hook to directly display the users in your theme or plugin.

For example, you can show users of any role ordered by their name:


	<?php
	echo do_action( 'wp_team_list', array( 'role' => 'all', 'orderby' => 'name' ) );
	?>


**Note:** Team List supports many of the arguments [`WP_User_Query`](https://codex.wordpress.org/Class_Reference/WP_User_Query "WordPress Codex Codex WP_User_Query") supports.

**Block**

Use the "Team List" block in the block-based editor to display one or more team lists in any post types. You can select one or more roles and change the order. If you want you can also provide a link to a full team page.

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

**Pro tip:** If you use the [Shortcake WordPress plugin](https://wordpress.org/plugins/shortcode-ui/), you'll get an inline preview of the shortcode right in the visual editor. You can also add the shortcode with the click of a button.

**Widget**

Want do display the team members in your sidebar? Use the built-in WordPress widget. You can set the role you want, the number of users to show and even link to a separate team page.

## Installation ##

1. Upload `wp-team-list` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Use one of the supported methods (hook, shortcode, or widget) to display a team list.
4. **Optional:** Set the visibility status of your users in their profiles.

## Frequently Asked Questions ##

### How do I contribute to Team List? ###

Easy! You can report bugs, with reproduction steps, or post patches on [GitHub](https://github.com/wearerequired/rplus-wp-team-list).

### What kind of filters / hooks are available? ###
* `wp_team_list_user_role` - Filter the user role displayed in the team list.
* `wp_team_list_query_args` - Filter the `WP_User_Query` arguments.
* `wp_team_list_template` - Change the team list template.
 Note: You can also put a `rplus-wp-team-list.php` file in your theme folder.
* `wp_team_list_avatar_size` - Filter the default avatar size.
* `wp_team_list_default_classes` - Filter default CSS classes.

## Screenshots ##

1. This is how your team list can look like with some additional CSS.
2. The plugin only provides limited styling. It's up to you to adjust it.
3. The block in the block editor with its settings.
4. The configuration options of the built-in widget.

## Changelog ##

### 2.0.1 ###
* Fix: Removed HTML output on profile page.
* Fix: Removed type hint for shortcode attributes to avoid a PHP fatal error.
* Fix: Correct `author` CSS classes.
* Enhancement: Improved plugin description.
* Enhancement: Translations moved to https://translate.wordpress.org/projects/wp-plugins/wp-team-list.
* Enhancement: Added deprecation notice and removed shortcode UI for `[rplus_team_list]` shortcode in favour of the new `[wp_team_list]` shortcode.

For older versions see Changelog.md.
