<?php
/**
 * Test our plugin.
 *
 * @package WP_Team_List
 */

/**
 * Class WP_Team_List_Test.
 */
class WP_Team_List_Test extends WP_Team_List_TestCase {
	/**
	 * The plugin should be installed and activated.
	 */
	function test_plugin_activated() {
		$this->assertTrue( class_exists( 'WP_Team_List' ) );
	}
}
