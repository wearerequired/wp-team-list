<?php
/**
 * The WP Team List Plugin
 *
 * @package   WP_Team_List
 * @author    Silvan Hagen <silvan@required.ch>
 * @license   GPL-2.0+
 * @link      https://github.com/wearerequired/wp-team-list/
 * @copyright 2013 required gmbh
 *
 * @wordpress-plugin
 * Plugin Name: WP Team List
 * Plugin URI:  http://wp.required.ch/plugin/wp-team-list/
 * Description: Provides you with a Widget <strong>WP Team List Widget</strong> a shortcode <code>[rplus_team_list]</code> and a template function <code>rplus_wp_team_list( $args, $echo = true );</code> to list your authors as a publisher team.
 * Version:     0.4.0
 * Author:      required+ Silvan Hagen
 * Author URI:  http://required.ch
 * Text Domain: rplus-wp-team-list
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Load the main plugin class
require_once( plugin_dir_path( __FILE__ ) . 'class-rplus-wp-team-list.php' );
require_once( plugin_dir_path( __FILE__ ) . 'widgets/class-rplus-wp-team-list-widget.php' );

// Register hooks that are fired when the plugin is activated or deactivated.
// When the plugin is deleted, the uninstall.php file is loaded.
register_activation_hook( __FILE__, array( 'WP_Team_List', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WP_Team_List', 'deactivate' ) );

// As soon as WP is ready we load the instance of the plugin
add_action( 'plugins_loaded', array( 'WP_Team_List', 'get_instance' ) );

/**
 * @param array  $args
 * @param bool   $echo
 * @param string $template
 *
 * @return string
 */
function rplus_wp_team_list( $args = array(), $echo = true, $template = 'rplus-wp-team-list.php' ) {
	/** @var WP_Team_List $wp_team_list */
	$wp_team_list = WP_Team_List::get_instance();

	return $wp_team_list->render_team_list( $args, $echo, $template );
}

function rplus_wp_team_list_classes( $classes ) {
	/** @var WP_Team_List $wp_team_list */
	$wp_team_list = WP_Team_List::get_instance();
	echo $wp_team_list->item_classes( $classes );
}

/**
 * Render the shortcode.
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content The encapsuled text.
 *
 * @return string
 */
function rplus_wp_team_list_shortcode( $atts, $content = '' ) {
	global $post;

	$atts = shortcode_atts( array(
		'role'    => 'Administrator',
		'orderby' => 'post_count',
		'order'   => 'DESC',
	), $atts, 'note' );

	/** @var WP_Team_List $wp_team_list */
	$wp_team_list = WP_Team_List::get_instance();

	return $wp_team_list->render_team_list( $atts, false, 'rplus-wp-team-list.php' );

}

add_shortcode( 'rplus_team_list', 'rplus_wp_team_list_shortcode' );

/**
 * Register a UI for the Shortcode using Shortcake
 */
function wplus_wp_team_list_shortcode_ui() {
	if ( ! function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
		return;
	}

	// Include this in order to use get_editable_roles()
	require_once( ABSPATH . 'wp-admin/includes/user.php' );

	$user_roles = array( 'all' => __( 'All' ) );
	foreach ( get_editable_roles() as $role => $data ) {
		$user_roles[ $role ] = $data['name'];
	}

	/**
	 * Register a UI for the Shortcode.
	 * Pass the shortcode tag (string)
	 * and an array or args.
	 */
	shortcode_ui_register_for_shortcode(
		'rplus_team_list',
		array(

			'label'         => __( 'Team List' ),
			'listItemImage' => 'dashicons-groups',
			'attrs'         => array(
				array(
					'label'   => __( 'Role' ),
					'attr'    => 'role',
					'type'    => 'select',
					'value'   => 'administrator',
					'options' => $user_roles,
				),
				array(
					'label'   => __( 'Order By' ),
					'attr'    => 'orderby',
					'type'    => 'select',
					'value' => 'post_count',
					'options' => array(
						'post_count' => __( 'Post Count' )
					)
				),
				array(
					'label'   => __( 'Order' ),
					'attr'    => 'order',
					'type'    => 'radio',
					'value' => 'desc',
					'options' => array(
						'asc'  => __( 'Ascending' ),
						'desc' => __( 'Descending' ),
					)
				),
			),
		)
	);
}

add_action( 'init', 'wplus_wp_team_list_shortcode_ui' );