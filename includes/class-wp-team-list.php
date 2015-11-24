<?php
/**
 * Holds the WP Team List class.
 *
 * @package WP_Team_List
 */

/**
 * WP_Team_List class.
 */
class WP_Team_List {
	/**
	 * Plugin version.
	 *
	 * Mainly used for cache-busting of style and script file references.
	 *
	 * @var string
	 */
	const VERSION = '2.0.0';

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 */
	public function add_hooks() {
		// Load plugin text domain.
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Register the team list widget.
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );

		// Register the stylesheet.
		add_action( 'wp_enqueue_scripts', array( $this, 'register_stylesheet' ) );

		// Add a checkbox to the user profile and edit user screen.
		add_action( 'show_user_profile', array( $this, 'admin_render_profile_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'admin_render_profile_fields' ) );

		// Save additional user profile and user edit information.
		add_action( 'personal_options_update', array( $this, 'admin_save_profile_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'admin_save_profile_fields' ) );

		// Add the team list visibility status to the user list table.
		add_filter( 'manage_users_columns', array( $this, 'admin_add_visibility_column' ) );
		add_action( 'manage_users_custom_column', array( $this, 'admin_add_visibility_column_content' ), 10, 3 );

		// Shortcodes.
		add_action( 'init', array( $this, 'add_shortcode' ) );
		add_action( 'init', array( $this, 'register_shortcode_ui' ) );

		// Support displaying the team list using an action.
		add_action( 'wp_team_list', array( $this, 'render' ) );

		// Load stylesheet in the editor.
		add_filter( 'mce_css', array( $this, 'filter_mce_css' ) );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * Makes dummy gettext calls to get user role strings in the catalog.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'wp-team-list', false, basename( dirname( __DIR__ ) ) . '/languages' );

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
	 */
	public function register_stylesheet() {
		// Use minified libraries if SCRIPT_DEBUG is turned off.
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_register_style(
			'wp-team-list',
			plugins_url( 'css/wp-team-list' . $suffix . '.css', plugin_dir_path( __FILE__ ) ),
			array(),
			self::VERSION
		);
	}

	/**
	 * Render team list profile fields.
	 *
	 * @param WP_User $user User object.
	 */
	public function admin_render_profile_fields( WP_User $user ) {
		?>
		<h3><?php _e( 'Team List Settings', 'wp-team-list' ); ?></h3>

		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="wp_team_list_visibility">
						<?php _e( 'Team list', 'wp-team-list' ); ?>
					</label>
				</th>
				<td>
					<?php
					printf(
						'<input type="checkbox" name="%1$s" id="%1$s" value="hidden" %2$s>',
						'wp_team_list_visibility',
						checked( get_user_meta( $user->ID, 'rplus_wp_team_list_visibility', true ), 'hidden' )
					)
					?>
					<label for="wp_team_list_visibility"><?php _e( 'Hide this user from the team list', 'wp-team-list' ); ?></label>
				</td>
			</tr>
		</table>
	<?php }

	/**
	 * Save team list profile fields.
	 *
	 * @param int $user_id The current user's ID.
	 */
	public function admin_save_profile_fields( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		$value = 'visible';

		if ( isset( $_POST['wp_team_list_visibility'] ) ) {
			$value = sanitize_text_field( $_POST['wp_team_list_visibility'] );
		}

		update_user_meta( $user_id, 'rplus_wp_team_list_visibility', $value );
	}

	/**
	 * Show the team list visibility in the user list table.
	 *
	 * @param  string $val         The current column value.
	 * @param  string $column_name Name of the current column.
	 * @param  int    $user_id     The current user's ID.
	 * @return string The column content. Either 'Visible' or 'Hidden'.
	 */
	public function admin_add_visibility_column_content( $val, $column_name, $user_id ) {
		$visibility = get_user_meta( $user_id, 'rplus_wp_team_list_visibility', true );

		if ( 'wp_team_list_visibility' === $column_name ) {
			return ( 'hidden' === $visibility ) ? __( 'Hidden', 'wp-team-list' ) : __( 'Visible', 'wp-team-list' );
		}

		return $val;
	}

	/**
	 * Add additional column to the user table in wp-admin.
	 *
	 * @param array $columns List table columns.
	 * @return array The modified columns list.
	 */
	public function admin_add_visibility_column( $columns ) {
		$columns['wp_team_list_visibility'] = __( 'Team List', 'wp-team-list' );

		return $columns;
	}

	/**
	 * Register WP Team List Widget
	 */
	public function register_widgets() {
		register_widget( 'WP_Team_List_Widget' );
	}

	/**
	 * Get Users
	 *
	 * Returns an array of WP_User objects if we found some from the $args delivered or (bool)
	 * false if we can't find any users.
	 *
	 * @param array $args User query arguments.
	 * @return array The queried users.
	 */
	protected function get_users( $args ) {
		$defaults = array(
			'role'                => 'administrator',
			'orderby'             => 'post_count',
			'order'               => 'DESC',
			'include'             => '',
			'has_published_posts' => null,
		);

		$args = wp_parse_args( $args, $defaults );

		// Show users with any role when requested.
		if ( 'all' === $args['role'] ) {
			unset( $args['role'] );
		}

		// Allow easy ordering by first and last name.
		if ( 'last_name' === $args['orderby'] || 'first_name' === $args['orderby'] ) {
			$args['meta_key'] = $args['orderby'];
			$args['orderby']  = 'meta_value';
		}

		// Make sure we always get an array of WP_User objects.
		$args['fields'] = 'ID';

		// Make sure the meta key for hiding isn't set.
		$args['meta_query'] = array(
			'relation' => 'OR',
			array(
				'key'     => 'rplus_wp_team_list_visibility',
				'value'   => 'visible',
				'compare' => '=',
			),
			array(
				'key'     => 'rplus_wp_team_list_visibility',
				'compare' => 'NOT EXISTS',
			),
		);

		if ( isset( $args['has_published_posts'] ) && 'true' !== (string) $args['has_published_posts'] ) {
			$args['has_published_posts'] = array_filter( array_map( 'trim', explode( ',', $args['has_published_posts'] ) ) );
		}

		// For compatibility with WordPress 4.3 and below.
		$roles = array();
		if ( isset( $args['role'] ) ) {
			$roles = array_filter( array_map( 'trim', explode( ',', $args['role'] ) ) );
		}

		if ( 1 < count( $roles ) ) {
			global $wpdb;
			foreach ( $roles as $role ) {
				$args['meta_query'][] = array(
					'key'     => $wpdb->get_blog_prefix( get_current_blog_id() ) . 'capabilities',
					'value'   => $role,
					'compare' => 'LIKE',
				);
			}

			unset( $args['role'] );
		}

		/**
		 * Filter the team list user query arguments.
		 *
		 * @param array $args WP_User_Query arguments.
		 */
		$query = new WP_User_Query( apply_filters( 'wp_team_list_query_args', $args ) );

		// Needed because WordPress does not add a DISTINCT to the query all the time.
		$users = array_unique( $query->get_results() );

		return $users;
	}

	/**
	 * Load the team list template.
	 *
	 * First looks in your theme and falls back to the bundled template file.
	 *
	 * @param WP_User $user     The user object that is needed by the template.
	 * @param string  $template Name of the template file to load.
	 */
	protected function load_template( WP_User $user, $template = 'rplus-wp-team-list.php' ) {
		if ( 'rplus-wp-team-list.php' !== $template ) {
			_deprecated_argument(
				__FUNCTION__,
				'2.0.0',
				printf( __( 'Use the %s filter instead.', 'wp-team-list' ), '<code>wp_team_list_template</code>' )
			);
		}

		// Check if the template file exists in the theme folder.
		$overridden_template = locate_template( $template );
		if ( $overridden_template ) {
			// Load the requested template file from the theme or child theme folder.
			$template_path = $overridden_template;
		} else {
			// Load the requested template file from the plugin folder.
			$template_path = trailingslashit( dirname( __FILE__ ) ) . $template;
		}

		/**
		 * Filter the team list template.
		 *
		 * @param   string  $template Full path to the template file.
		 * @param   WP_User $user     The user object that is needed by the template.
		 */
		$template_path = apply_filters( 'wp_team_list_template', $template_path, $user );

		include( $template_path );
	}

	/**
	 * WP Team List item classes.
	 *
	 * Allows for space separated string and array as
	 * data input.
	 *
	 * @param  string|array $classes List of class names.
	 * @return string The modified class list.
	 */
	public function item_classes( $classes ) {
		$defaults = apply_filters(
			'rplus_wp_team_list_default_classes',
			array(
				'wp-team-member',
				'wp-team-list-item',
			)
		);

		if ( ! is_array( $classes ) ) {
			$classes = explode( ' ', $classes );
		}

		$classes = array_merge( $defaults, $classes );

		return esc_attr( join( ' ', $classes ) );
	}

	/**
	 * Render the team list.
	 *
	 * @param array $args WP_User_Query arguments.
	 * @return string
	 */
	public function render( array $args ) {
		$users = $this->get_users( $args );

		if ( ! $users ) {
			return '';
		}

		wp_enqueue_style( 'wp-team-list' );

		ob_start();

		foreach ( $users as $user_id ) {
			$user = get_userdata( $user_id );
			$this->load_template( $user );
		}

		return ob_get_clean();
	}

	/**
	 * Renders the template to the page.
	 *
	 * @param  array   $args     WP_User_Query arguments.
	 * @param  boolean $echo     Whether to return the result or echo it.
	 * @param  string  $template The template file name to include.
	 * @return void|string
	 */
	public function render_team_list( $args, $echo = true, $template = 'rplus-wp-team-list.php' ) {
		$users = $this->get_users( $args );

		$output = '';

		if ( $users ) {
			wp_enqueue_style( 'wp-team-list' );

			ob_start();

			foreach ( $users as $user_id ) {
				$user = get_userdata( $user_id );
				$this->load_template( $user, $template );
			}

			$output = ob_get_clean();
		}

		if ( $echo ) {
			echo $output; // WPCS: XSS ok.
		}

		return $output;
	}

	/**
	 * Add the team list shortcode.
	 */
	public function add_shortcode() {
		add_shortcode( 'rplus_team_list', array( $this, 'render_shortcode' ) );
		add_shortcode( 'wp_team_list', array( $this, 'render_shortcode' ) );
	}

	/**
	 *  Shortcode callback to render the team list.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string The rendered team list.
	 */
	public function render_shortcode( array $atts ) {
		$args = shortcode_atts( array(
			'role'                => 'Administrator',
			'orderby'             => 'post_count',
			'order'               => 'DESC',
			'include'             => '',
			'has_published_posts' => null,
		), $atts, 'wp_team_list' );

		return $this->render( $args );
	}

	/**
	 * Register a UI for the Shortcode using Shortcake
	 */
	public function register_shortcode_ui() {
		if ( ! function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
			return;
		}

		// Include this in order to use get_editable_roles().
		require_once( ABSPATH . 'wp-admin/includes/user.php' );

		$user_roles = array( 'all' => __( 'All', 'wp-team-list' ) );
		foreach ( get_editable_roles() as $role => $data ) {
			$user_roles[ $role ] = $data['name'];
		}

		$shortcode_ui_args = array(
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
					'label'   => __( 'Order By', 'wp-team-list' ),
					'attr'    => 'orderby',
					'type'    => 'select',
					'value'   => 'post_count',
					'options' => array(
						'post_count' => __( 'Post Count', 'wp-team-list' ),
					),
				),
				array(
					'label'   => __( 'Order', 'wp-team-list' ),
					'attr'    => 'order',
					'type'    => 'radio',
					'value'   => 'desc',
					'options' => array(
						'asc'  => __( 'Ascending', 'wp-team-list' ),
						'desc' => __( 'Descending', 'wp-team-list' ),
					),
				),
			),
		);

		shortcode_ui_register_for_shortcode( 'rplus_team_list', $shortcode_ui_args );
		shortcode_ui_register_for_shortcode( 'wp_team_list', $shortcode_ui_args );
	}

	/**
	 * Get a specific users' role.
	 *
	 * @param \WP_User $user User object.
	 * @return string Translated role name.
	 */
	public function get_user_role( WP_User $user ) {
		$role = translate_with_gettext_context( $GLOBALS['wp_roles']->roles[ $user->roles[0] ]['name'], 'User role', 'wp-team-list' );

		/**
		 * Filter the user role displayed in the team list.
		 *
		 * @param string  $role Role name.
		 * @param WP_User $user User object.
		 */
		return apply_filters( 'wp_team_list_user_role', $role, $user );
	}

	/**
	 * Filter the list of stylesheets enqueued in the editor.
	 *
	 * @param string $stylesheets Comma-separated list of editor stylesheets.
	 * @return string Modified stylesheet list.
	 */
	public function filter_mce_css( $stylesheets ) {
		$stylesheets = explode( ',', $stylesheets );

		// Use minified libraries if SCRIPT_DEBUG is turned off.
		$suffix        = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		$stylesheets[] = plugins_url( 'css/wp-team-list' . $suffix . '.css', plugin_dir_path( __FILE__ ) );

		return implode( ',', $stylesheets );
	}

	/**
	 * Get a user's avatar.
	 *
	 * @param \WP_User $user User object.
	 * @return false|string User avatar or false on failure.
	 */
	public function get_avatar( WP_User $user ) {
		/**
		 * Filter the team list avatar size.
		 *
		 * @param int     $size Avatar size. Default 50.
		 * @param WP_User $user Current user object.
		 */
		$size = apply_filters( 'wp_team_list_avatar_size', 90, $user );

		return get_avatar( $user->ID, $size );
	}
}
