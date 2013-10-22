# r+ WP Team List

## Description
WP Simple Team is a plugin to create a simple team site using your WordPress users. The plugin provides you with a shortcode, template function and a widget.

We decided to create this plugin after we created this functionality for a couple of clients.

### Profile: Add a checkbox
We add an additional checkbox to the user profile in WordPress so you can decide for every WP_User wether she should show up in the Widget and the list.

### Template function: rplus_team_list( $args, $echo = true )

### Shortcode: [rplus_team_list args='']

### Widget: r+ Team List Widget
$members = 5;
$orderby = 'lastname';
$order = 'DESC';

### Templates: rplus-team-list.php, rplus-team-list-widget.php
You can copy these template files over into your theme and modify them the way you wish the users markup to be generated. Please make sure the that the original templates remain in the plugin folder: /rplus-wp-team-list/templates/

