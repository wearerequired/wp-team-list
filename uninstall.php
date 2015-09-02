<?php
/**
 * Delete all user meta data when the plugin is uninstalled.
 *
 * @package   WP_Team_List
 * @author    Silvan Hagen <silvan@required.ch>
 * @license   GPL-2.0+
 * @link      https://github.com/wearerequired/wp-team-list/
 * @copyright 2015 required gmbh
 */

// If uninstall, not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$query = new WP_User_Query( array( 'meta_key' => 'rplus_wp_team_list_visibility' ) );

/* @var WP_User $user */
foreach ( $query->get_results() as $user ) {
	delete_user_meta( $user->ID, 'rplus_wp_team_list_visibility' );
}
