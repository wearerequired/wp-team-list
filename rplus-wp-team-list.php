<?php
/**
 * The WP Team List Plugin
 *
 * @package   WP_Team_List
 * @author    Silvan Hagen <silvan@required.ch>
 * @license   GPL-2.0+
 * @link      https://github.com/wearerequired/wp-team-list/
 * @copyright 2015 required gmbh
 *
 * @wordpress-plugin
 * Plugin Name: WP Team List
 * Plugin URI:  https://github.com/wearerequired/rplus-wp-team-list
 * Description: Display your teammates anywhere on your WordPress site using this easy-to-use plugin. Provides you with a widget, a shortcode <code>[rplus_team_list]</code> and a template function <code>rplus_wp_team_list( $args, $echo = true );</code> to list the blog authors.
 * Version:     1.0.1
 * Author:      required+
 * Author URI:  http://required.ch
 * Text Domain: wp-team-list
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

// As soon as WP is ready we load the instance of the plugin
add_action( 'plugins_loaded', array( 'WP_Team_List', 'get_instance' ) );

/**
 * @param array  $args     Additional arguments for the WP_User_Query.
 * @param bool   $echo     Whether to echo the output or just return it.
 * @param string $template The template file to load for the team list.
 *
 * @return void|string
 */
function rplus_wp_team_list( $args = array(), $echo = true, $template = 'rplus-wp-team-list.php' ) {
	/** @var WP_Team_List $wp_team_list */
	$wp_team_list = WP_Team_List::get_instance();

	return $wp_team_list->render_team_list( $args, $echo, $template );
}

/**
 * @param string|array $classes List of class names.
 */
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

	wp_enqueue_style( 'rplus-wp-team-list-plugin-styles' );

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

	$user_roles = array( 'all' => __( 'All', 'wp-team-list' ) );
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

			'label'         => __( 'Team List', 'wp-team-list' ),
			'listItemImage' => 'dashicons-groups',
			'attrs'         => array(
				array(
					'label'   => __( 'Role', 'wp-team-list' ),
					'attr'    => 'role',
					'type'    => 'select',
					'value'   => 'administrator',
					'options' => $user_roles,
				),
				array(
					'label'   => __( 'Order By' ),
					'attr'    => 'orderby',
					'type'    => 'select',
					'value'   => 'post_count',
					'options' => array(
						'post_count' => __( 'Post Count', 'wp-team-list' )
					)
				),
				array(
					'label'   => __( 'Order', 'wp-team-list' ),
					'attr'    => 'order',
					'type'    => 'radio',
					'value'   => 'desc',
					'options' => array(
						'asc'  => __( 'Ascending', 'wp-team-list' ),
						'desc' => __( 'Descending', 'wp-team-list' ),
					)
				),
			),
		)
	);
}

add_action( 'init', 'wplus_wp_team_list_shortcode_ui' );