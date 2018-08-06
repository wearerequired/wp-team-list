<?php
/**
 * Initializes the plugin.
 *
 * Contrary to rplus-wp-team-list.php, this file doesn't need to work on PHP 5.2.
 *
 * @package WP_Team_List
 */

define( 'Required\WPTeamList\PLUGIN_DIR', __DIR__ );
define( 'Required\WPTeamList\TEMPLATES_DIR', __DIR__ . '/templates' );

/**
 * Get the team list instance.
 *
 * @return Required\WPTeamList\Plugin Plugin instance.
 */
function wp_team_list() {
	static $wp_team_list = null;

	if ( null === $wp_team_list ) {
		$wp_team_list = new \Required\WPTeamList\Plugin();
	}

	return $wp_team_list;
}

add_action( 'plugins_loaded', [ wp_team_list(), 'add_hooks' ] );
