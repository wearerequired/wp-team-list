<?php
/**
 * Delete all user meta data when the plugin is uninstalled.
 */

// If uninstall, not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$wp_team_list_query = new WP_User_Query( [ 'meta_key' => 'rplus_wp_team_list_visibility' ] );

/* @var WP_User $wp_team_list_user */
foreach ( $wp_team_list_query->get_results() as $wp_team_list_user ) {
	delete_user_meta( $wp_team_list_user->ID, 'rplus_wp_team_list_visibility' );
}
