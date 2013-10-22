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

if ( ! function_exists( 'rplus_wp_team_list' ) ) :

    function rplus_wp_team_list( $args = array(), $echo = true, $template = 'rplus-wp-team-list.php' ) {

        if (  ! class_exists( 'WP_Team_List' ) )
            wp_die( __( 'Oops, it looks like WP_Team_List doesn\'t exist!', 'rplus-wp-team-list' ) );

        $wp_team_list = WP_Team_List::get_instance();

        return $wp_team_list->render_team_list( $args, $echo, $template );

    }

endif; // ( ! function_exists( 'rplus_wp_team_list' ) )

if ( ! function_exists( 'rplus_wp_team_list_classes' ) ) :

    function rplus_wp_team_list_classes( $classes ) {

        if (  ! class_exists( 'WP_Team_List' ) )
            wp_die( __( 'Oops, it looks like WP_Team_List doesn\'t exist!', 'rplus-wp-team-list' ) );

        $wp_team_list = WP_Team_List::get_instance();

        echo $wp_team_list->item_classes( $classes );
    }

endif; // ( ! function_exists( 'rplus_wp_team_list_classes' ) )