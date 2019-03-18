<?php
/**
 * Plugin Name: Team List
 * Plugin URI:  https://github.com/wearerequired/rplus-wp-team-list
 * Description: Display your teammates anywhere on your WordPress site using this easy-to-use plugin. Provides you with a block, a widget, a shortcode, and a template function to list the users of your site.
 * Version:     3.0.0-RC2
 * Author:      required
 * Author URI:  https://required.com
 * Text Domain: wp-team-list
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * Copyright (c) 2014-2019 required (email: info@required.ch)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// phpcs:disable Generic.Arrays.DisallowLongArraySyntax -- File needs to be parsable by PHP 5.2.4.

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}

// phpcs:ignore WordPress.NamingConventions -- Variable gets unset.
$requirements_check = new WP_Requirements_Check(
	array(
		'title' => 'WP Team List',
		'php'   => '5.6',
		'wp'    => '5.0',
		'file'  => __FILE__,
	)
);
if ( $requirements_check->passes() ) {
	require_once __DIR__ . '/init.php';
}

unset( $requirements_check );
