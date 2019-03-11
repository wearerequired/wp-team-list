<?php
/**
 * Plugin Name: WP Team List
 * Plugin URI:  https://github.com/wearerequired/rplus-wp-team-list
 * Description: Display your teammates anywhere on your WordPress site using this easy-to-use plugin. Provides you with a widget, a shortcode and a template function to list the blog users.
 * Version:     3.0.0-beta
 * Author:      required
 * Author URI:  https://required.com
 * Text Domain: wp-team-list
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package WP_Team_List
 */

// phpcs:disable Generic.Arrays.DisallowLongArraySyntax -- File needs to be parsable by PHP 5.2.4.

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}

// phpcs:ignore WordPress.NamingConventions -- Variable gets unset.
$requirements_check = new WP_Requirements_Check(
	array(
		'title' => 'WP Team List',
		'php'   => '5.4',
		'wp'    => '4.0',
		'file'  => __FILE__,
	)
);
if ( $requirements_check->passes() ) {
	require_once __DIR__ . '/init.php';
}

unset( $requirements_check );
