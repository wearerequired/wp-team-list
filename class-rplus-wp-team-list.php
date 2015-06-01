<?php
/**
 * WP Team List Plugin
 *
 * @package   WP_Team_List
 * @author    Silvan Hagen <silvan@required.ch>
 * @license   GPL-2.0+
 * @link      https://github.com/wearerequired/wp-team-list/
 * @copyright 2015 required gmbh
 */

/**
 * WP_Team_List class.
 *
 * @package WP_Team_List
 * @author  Silvan Hagen <silvan@required.ch>
 */
class WP_Team_List {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   0.1.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.3';

	/**
	 * Unique identifier for your plugin.
	 *
	 * The variable name is used as the text domain when internationalizing strings of text.
	 * Its value should match the Text Domain file header in the main plugin file.
	 *
	 * @since    0.1.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'wp-team-list';

	/**
	 * Instance of this class.
	 *
	 * @since    0.1.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Meta Key name of the profile setting.
	 *
	 * @since    0.1.0
	 *
	 * @var    string
	 */
	protected static $plugin_user_meta_key = 'rplus_wp_team_list_visibility';

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     0.1.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		add_action( 'widgets_init', array( $this, 'register_widgets' ) );

		// Add an action link pointing to profile page.
		$plugin_basename = plugin_basename( plugin_dir_path( __FILE__ ) . 'rplus-wp-team-list.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ) );

		// Add a checkbox to the user profile and edit user screen
		add_action( 'show_user_profile', array( $this, 'admin_render_profile_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'admin_render_profile_fields' ) );

		// Save additional user profile and user edit infos
		add_action( 'personal_options_update', array( $this, 'admin_save_profile_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'admin_save_profile_fields' ) );

		add_filter( 'manage_users_columns', array( $this, 'admin_add_visibility_column' ) );
		add_action( 'manage_users_custom_column', array( $this, 'admin_add_visibility_column_content' ), 10, 3 );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     0.1.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * Makes dummy gettext calls to get user role strings in the catalog.
	 *
	 * @since    0.1.0
	 */
	public function load_plugin_textdomain() {
		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, basename( dirname( __FILE__ ) ) . '/languages' );

		/* translators: user role */
		_x( 'Administrator', 'User role', 'wp-team-list' );
		/* translators: user role */
		_x( 'Editor', 'User role', 'wp-team-list' );
		/* translators: user role */
		_x( 'Author', 'User role', 'wp-team-list' );
		/* translators: user role */
		_x( 'Contributor', 'User role', 'wp-team-list' );
		/* translators: user role */
		_x( 'Subscriber', 'User role', 'wp-team-list' );
	}

	/**
	 * Register the public-facing stylesheet.
	 *
	 * @since    0.1.0
	 */
	public function register_styles() {
		wp_register_style( 'rplus-wp-team-list-plugin-styles', plugins_url( 'css/rplus-wp-team-list.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    0.1.0
	 */
	public function add_action_links( $links ) {
		return array_merge(
			array(
				'profile' => '<a href="' . admin_url( 'profile.php' ) . '">' . __( 'Settings', 'wp-team-list' ) . '</a>'
			),
			$links
		);
	}

	/**
	 * Render additional profile fields.
	 *
	 * Add a checkbox to the user profile and user edit screen in the /wp-admin,
	 * if checked, the user will be excluded from all public facing output that
	 * WP Team List might creating using either the Shortcode, Widget or Template
	 * Tag available.
	 *
	 * @param  object $user WP_User
	 *
	 * @return string        Renders the checkbox
	 *
	 * @uses     get_user_meta( $user_id, $key = '', $single = false )
	 * @link     http://codex.wordpress.org/Function_Reference/get_user_meta
	 *
	 * @since    0.1.0
	 */
	public function admin_render_profile_fields( $user ) { ?>
		<!-- START: WP_Team_List::render_profile_fields -->
		<h3><?php _e( 'Team List Settings', 'wp-team-list' ); ?></h3>

		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="rplus_wp_team_list_visibility"><?php _e( 'Hide on Team List?', 'wp-team-list' ); ?></label>
				</th>
				<td>
					<label for="rplus_wp_team_list_visibility">
						<input type="checkbox" name="<?php echo WP_Team_List::$plugin_user_meta_key; ?>" id="rplus_wp_team_list_visibility" value="hidden" <?php checked( get_user_meta( $user->ID, WP_Team_List::$plugin_user_meta_key, true ), "hidden" ); ?>>
						<?php _e( 'Hide this user from WP Team List', 'wp-team-list' ); ?>
					</label>
				</td>
			</tr>
		</table>
		<!-- END: WP_Team_List::render_profile_fields -->
	<?php }

	/**
	 * Save additional profile fields.
	 *
	 * Updates the user meta information for the specific user or your profile
	 * when saved from the /wp-admin user profile or user edit screen.
	 *
	 * @param    integer $user_id ID of the currently edited WP_User
	 *
	 * @uses     update_user_meta( $user_id, $meta_key, $meta_value, $prev_value = '' )
	 * @link     http://codex.wordpress.org/Function_Reference/update_user_meta
	 *
	 * @since    0.1.0
	 */
	public function admin_save_profile_fields( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		$maybe_value = 'visible';

		if ( isset( $_POST[ WP_Team_List::$plugin_user_meta_key ] ) ) {
			$maybe_value = sanitize_text_field( $_POST[ WP_Team_List::$plugin_user_meta_key ] );
		}

		update_user_meta( $user_id, WP_Team_List::$plugin_user_meta_key, $maybe_value );
	}

	/**
	 * Show the team list visibility in the user list table.
	 *
	 * @param  string $val         The current column value.
	 * @param  string $column_name Name of the current column.
	 * @param  int    $user_id     ID of the current user.
	 *
	 * @return string The user's visibility status (visible or hidden).
	 */
	public function admin_add_visibility_column_content( $val, $column_name, $user_id ) {
		$visibility = get_user_meta( $user_id, WP_Team_List::$plugin_user_meta_key, $single = true );

		if ( WP_Team_List::$plugin_user_meta_key === $column_name ) {
			$val = __( 'Visible', 'wp-team-list' );

			if ( 'hidden' == $visibility ) {
				$val = __( 'Hidden', 'wp-team-list' );
			}
		}

		return $val;
	}

	/**
	 * Add additional column to the user table in wp-admin.
	 *
	 * @param  array $columns
	 *
	 * @return array
	 */
	public function admin_add_visibility_column( $columns ) {
		$columns[ WP_Team_List::$plugin_user_meta_key ] = __( 'Team List', 'wp-team-list' );

		return $columns;
	}

	/**
	 * Register WP Team List Widget
	 *
	 * @return void
	 */
	public function register_widgets() {
		if ( class_exists( 'WP_Team_List_Widget' ) ) {
			register_widget( 'WP_Team_List_Widget' );
		}
	}

	/**
	 * Load the frontend template.
	 *
	 * This function loads the specific template file from either your theme or child theme
	 * or falls back on the templates living in the /wp-team-list/templates folder.
	 *
	 * @param   string  $template_file Name of the template file to load.
	 * @param   WP_User $user          The user object that is needed by the template.
	 *
	 * @since    0.1.0
	 */
	public function load_template( $template_file, $user ) {
		/**
		 * Make template file filterable.
		 *
		 * @param   string  $template_file Name of the template file to load.
		 * @param   WP_User $user          The user object that is needed by the template.
		 */
		$template_file = apply_filters( 'rplus_wp_team_list_template', $template_file, $user );

		// Check if the template file exists in the theme folder
		if ( $overridden_template = locate_template( $template_file ) ) {
			// Load the requested template file from the theme or child theme folder
			$template_path = $overridden_template;
		} else {
			// Load the requested template file from the plugin folder
			$template_path = dirname( __FILE__ ) . '/templates/' . $template_file;
		}

		include( $template_path );
	}

	/**
	 * Get Users
	 *
	 * Returns an array of WP_User objects if we found some from the $args delivered or (bool)
	 * false if we can't find any users.
	 *
	 * @param    array $args Array of User Query args
	 *
	 * @return    mixed    false|array     Returns false or an array of WP_User objects
	 *
	 * @since    0.1.0
	 */
	public function get_users( $args ) {

		$defaults = apply_filters(
			'rplus_wp_team_list_default_args',
			array(
				'role'    => 'Administrator',
				'orderby' => 'post_count',
				'order'   => 'DESC',
			)
		);

		$args = apply_filters(
			'rplus_wp_team_list_args',
			wp_parse_args( $args, $defaults )
		);

		if ( 'all' === $args['role'] ) {
			unset( $args['role'] );
		}

		if ( 'last_name' === $args['orderby'] || 'first_name' === $args['orderby'] ) {
			$args['meta_key'] = $args['orderby'];
			$args['orderby']  = 'meta_value';
		}

		// Make sure we always get an array of WP_User objects
		$args['fields'] = 'ID';

		// Make sure the meta key for hiding isn't set.
		$args['meta_query'] = array(
			'relation' => 'OR',
			array(
				'key'     => WP_Team_List::$plugin_user_meta_key,
				'value'   => 'visible',
				'compare' => '=',
			),
			array(
				'key'     => WP_Team_List::$plugin_user_meta_key,
				'compare' => 'NOT EXISTS',
			),
		);

		$query = new WP_User_Query( apply_filters( 'rplus_wp_team_list_args', $args ) );
		$users = $query->get_results();

		if ( empty( $users ) ) {
			return false;
		}

		return $users;
	}

	/**
	 * WP Team List item classes.
	 *
	 * Allows for space separated string and array as
	 * data input.
	 *
	 * @param  string|array $classes List of class names.
	 *
	 * @return string The modified class list.
	 */
	public function item_classes( $classes ) {
		$defaults = apply_filters(
			'rplus_wp_team_list_default_classes',
			array(
				'wp-team-member',
				'wp-team-list-item'
			)
		);

		if ( ! is_array( $classes ) ) {
			$classes = explode( ' ', $classes );
		}

		$classes = apply_filters(
			'rplus_wp_team_list_classes',
			array_merge( $defaults, $classes )
		);

		$classes = array_map( 'esc_attr', $classes );

		return join( ' ', $classes );
	}

	/**
	 * Renders the template to the page
	 *
	 * @param  array   $args     WP_User_Query arguments
	 * @param  boolean $echo     Whether to return the result or echo it.
	 * @param  string  $template The template file name to include.
	 *
	 * @return void|string  renders markup
	 */
	public function render_team_list( $args, $echo = true, $template = 'rplus-wp-team-list.php' ) {
		$users = $this->get_users( $args );

		$output = '';

		if ( $users ) {
			wp_enqueue_style( 'rplus-wp-team-list-plugin-styles' );

			ob_start();

			foreach ( $users as $user_id ) {
				$user = get_userdata( $user_id );
				$this->load_template( $template, $user );
			}

			$output = ob_get_clean();
		}

		if ( $echo ) {
			echo $output;
		} else {
			return $output;
		}
	}
}
