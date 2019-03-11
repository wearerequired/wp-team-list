<?php
/**
 * Delete all user meta data when the plugin is uninstalled.
 */

// If uninstall, not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$query = new WP_User_Query( [ 'meta_key' => 'rplus_wp_team_list_visibility' ] );

/* @var WP_User $user */
foreach ( $query->get_results() as $user ) {
	delete_user_meta( $user->ID, 'rplus_wp_team_list_visibility' );
}
