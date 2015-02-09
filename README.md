# WP Team List #
**Contributors:** neverything, hubresen, swissspidy  
Donate link:
**Tags:** authors, widget, users, list, team, shortcode  
**Requires at least:** 3.5.1  
**Tested up to:** 4.1  
**Stable tag:** 1.0.0  
**License:** GPLv2 or later  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html  

Display your teammates anywhere on your WordPress site using this easy-to-use plugin.

## Description ##

WP Team List is a plugin that helps you to create a simple team site using your WordPress users with various display options. Either use a shortcode, a template tag or the built-in widget to display blog authors.

After creating similar functionality for a couple of clients, we decided to roll our knowledge into this simple plugin. It's really straightforward to use:

First of all, the plugin adds a small checkbox to the user profile in WordPress to toggle its visibility in the team list. This means you can decide for every user whether he should show up in the team list or not.

You can then use one of these ways to display the list anywhere on your site.

# Template Tag #

Use the `rplus_wp_team_list($args = array(), $echo = true)` function to directly display the users in your teme. If you use `echo = false`, the output is only returned, not echoed.

For example, you can show users of any role ordered by their name:

`<?php rplus_wp_team_list( array( 'role' => 'all', 'orderby' => 'name' ) ); ?>`

**Note:** WP Team List supports many of the arguments [`WP_User_Query`](http://codex.wordpress.org/Class_Reference/WP_User_Query "WordPress Codex Codex WP_User_Query") supports.  

# Shortcode #

The `[rplus_team_list]` accepts the same arguments as the template tag. Example:

`[rplus_team_list role="Administrator" orderby="post_count" order="desc"]`

This returns all admins ordered by the number of posts they've written (descending).

# Widget #

Want do display the team members in your sidebar? Use the built-in WordPress widget. You can set the role you want, the number of users to show and even link to a separate team page.

## Installation ##

1. Upload `rplus-wp-team-list` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Use one of the supported methods (template tag, shortcode, or widget)
**1. Optional:** Set the visibility status of your users  

## Frequently Asked Questions ##

### A question that someone might have ###

An answer to that question.

## Screenshots ##

### 1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from ###
![This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from](http://s.wordpress.org/extend/plugins/wp-team-list/screenshot-1.png)

the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
### 2. This is the second screen shot ###
![This is the second screen shot](http://s.wordpress.org/extend/plugins/wp-team-list/screenshot-2.png)


## Changelog ##

### 1.0.0 ###
* Initial Release

## Upgrade Notice ##

### 1.0.0 ###
Initial Release