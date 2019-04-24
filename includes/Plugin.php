<?php
/**
 * Holds the WP Team List class.
 */

namespace Required\WPTeamList;

use WP_User;
use WP_User_Query;

/**
 * WP_Team_List class.
 */
class Plugin {
	/**
	 * Plugin version.
	 *
	 * Mainly used for cache-busting of style and script file references.
	 *
	 * @var string
	 */
	const VERSION = '3.0.0';

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 */
	public function add_hooks() {
		// Load plugin text domain.
		add_action( 'init', [ $this, 'load_plugin_textdomain' ] );

		// Register the team list widget.
		add_action( 'widgets_init', [ $this, 'register_widgets' ] );

		// Register the stylesheet.
		add_action( 'wp_enqueue_scripts', [ $this, 'register_stylesheet' ] );

		// Add a checkbox to the user profile and edit user screen.
		add_action( 'show_user_profile', [ $this, 'admin_render_profile_fields' ] );
		add_action( 'edit_user_profile', [ $this, 'admin_render_profile_fields' ] );

		// Save additional user profile and user edit information.
		add_action( 'personal_options_update', [ $this, 'admin_save_profile_fields' ] );
		add_action( 'edit_user_profile_update', [ $this, 'admin_save_profile_fields' ] );

		// Add the team list visibility status to the user list table.
		add_filter( 'manage_users_columns', [ $this, 'admin_add_visibility_column' ] );
		add_action( 'manage_users_custom_column', [ $this, 'admin_add_visibility_column_content' ], 10, 3 );

		// Shortcodes.
		add_action( 'init', [ $this, 'add_shortcode' ] );
		add_action( 'init', [ $this, 'register_shortcode_ui' ] );

		// Blocks.
		add_action( 'init', [ $this, 'register_block_type' ] );

		// Support displaying the team list using an action.
		add_action( 'wp_team_list', [ $this, 'render' ] );

		// Load stylesheet in the editor.
		add_filter( 'mce_css', [ $this, 'filter_mce_css' ] );

		// Register REST API route.
		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * Makes dummy gettext calls to get user role strings in the catalog.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'wp-team-list' );

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
		wp_register_style(
			'wp-team-list',
			plugins_url( 'assets/css/style.css', plugin_dir_path( __FILE__ ) ),
			[],
			self::VERSION
		);

		wp_styles()->add_data( 'wp-team-list', 'rtl', true );
	}

	/**
	 * Render team list profile fields.
	 *
	 * @param \WP_User $user User object.
	 */
	public function admin_render_profile_fields( WP_User $user ) {
		?>
		<h3><?php _e( 'Team List Settings', 'wp-team-list' ); ?></h3>

		<?php wp_nonce_field( 'team-list-visibility-' . $user->ID, '_wp_team_list_nonce' ); ?>

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
						checked( get_user_meta( $user->ID, 'rplus_wp_team_list_visibility', true ), 'hidden', false )
					)
					?>
					<label for="wp_team_list_visibility"><?php _e( 'Hide this user from the team list', 'wp-team-list' ); ?></label>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Save team list profile fields.
	 *
	 * @param int $user_id The current user's ID.
	 */
	public function admin_save_profile_fields( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		if ( ! isset( $_POST['_wp_team_list_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['_wp_team_list_nonce'], 'team-list-visibility-' . $user_id ) ) {
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
		register_widget( Widget::class );
	}

	/**
	 * Get Users
	 *
	 * Returns an array of WP_User objects if we found some from the $args delivered or (bool)
	 * false if we can't find any users.
	 *
	 * @param array $args User query arguments.
	 * @return int[] The queried users' IDs.
	 */
	public function get_users( $args ) {
		$defaults = [
			'role'                => 'administrator',
			'orderby'             => 'post_count',
			'order'               => 'DESC',
			'include'             => '',
			'has_published_posts' => null,
		];

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
		$args['meta_query'] = [
			'relation' => 'AND',
			[
				'relation' => 'OR',
				[
					'key'     => 'rplus_wp_team_list_visibility',
					'value'   => 'visible',
					'compare' => '=',
				],
				[
					'key'     => 'rplus_wp_team_list_visibility',
					'compare' => 'NOT EXISTS',
				],
			],
		];

		if ( isset( $args['has_published_posts'] ) && 'true' !== (string) $args['has_published_posts'] ) {
			$args['has_published_posts'] = array_filter( array_map( 'trim', explode( ',', $args['has_published_posts'] ) ) );
		}

		$roles = [];
		if ( isset( $args['role'] ) ) {
			$roles = is_array( $args['role'] ) ? $args['role'] : array_filter( array_map( 'trim', explode( ',', $args['role'] ) ) );
		}

		unset( $args['role'] );

		if ( $roles ) {
			$args['role__in'] = $roles;
		}

		/**
		 * Filter the team list user query arguments.
		 *
		 * @param array $args WP_User_Query arguments.
		 */
		$query = new WP_User_Query( apply_filters( 'wp_team_list_query_args', $args ) );

		// Needed because WordPress does not add a DISTINCT to the query all the time.
		return array_unique( $query->get_results() );
	}

	/**
	 * Load the team list template.
	 *
	 * First looks in your theme and falls back to the bundled template file.
	 *
	 * @param \Required\WPTeamList\WP_User $user The user object that is needed by the template.
	 * @param string                       $template Name of the template file to load.
	 */
	protected function load_template( WP_User $user, $template = 'rplus-wp-team-list.php' ) {
		if ( 'rplus-wp-team-list.php' !== $template ) {
			_deprecated_argument(
				__FUNCTION__,
				'2.0.0',
				/* translators: %s: wp_team_list_template */
				sprintf( __( 'Use the %s filter instead.', 'wp-team-list' ), '<code>wp_team_list_template</code>' )
			);
		}

		// Check if the template file exists in the theme folder.
		$overridden_template = locate_template( $template );
		if ( $overridden_template ) {
			// Load the requested template file from the theme or child theme folder.
			$template_path = $overridden_template;
		} else {
			// Load the requested template file from the plugin folder.
			$template_path = trailingslashit( \Required\WPTeamList\TEMPLATES_DIR ) . $template;
		}

		/**
		 * Filter the team list template.
		 *
		 * @param   string  $template Full path to the template file.
		 * @param \Required\WPTeamList\WP_User $user The user object that is needed by the template.
		 */
		$template_path = apply_filters( 'wp_team_list_template', $template_path, $user );

		include $template_path;
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
		$default_classes = [
			'wp-team-member',
			'wp-team-list-item',
		];

		$default_classes = apply_filters_deprecated(
			'rplus_wp_team_list_default_classes',
			[ $default_classes ],
			'3.0.0',
			'wp_team_list_default_classes'
		);

		$default_classes = apply_filters(
			'wp_team_list_default_classes',
			$default_classes
		);

		if ( ! is_array( $classes ) ) {
			$classes = explode( ' ', $classes );
		}

		$classes = array_merge( $default_classes, $classes );

		return esc_attr( implode( ' ', $classes ) );
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
	 * @return string
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
			// phpcs:ignore WordPress.Security.EscapeOutput
			echo $output;
		}

		return $output;
	}

	/**
	 * Add the team list shortcode.
	 */
	public function add_shortcode() {
		add_shortcode( 'rplus_team_list', [ $this, 'render_shortcode' ] );
		add_shortcode( 'wp_team_list', [ $this, 'render_shortcode' ] );
	}

	/**
	 * Shortcode callback to render the team list.
	 *
	 * @param array|string $atts    Shortcode attributes.
	 * @param string       $content Shortcode inner content.
	 * @param string       $tag     Shortcode name.
	 * @return string The rendered team list.
	 */
	public function render_shortcode( $atts, $content = null, $tag = '' ) {
		if ( 'rplus_team_list' === $tag ) {
			_deprecated_argument(
				'do_shortcode_tag()',
				'2.0.0',
				sprintf(
					/* translators: 1: [rplus_team_list], 2: [wp_team_list] */
					__( 'The %1$s shortcode has been replaced with %2$s.', 'wp-team-list' ),
					'<code>[rplus_team_list]</code>',
					'<code>[wp_team_list]</code>'
				)
			);
		}

		$args = shortcode_atts(
			[
				'role'                => 'Administrator',
				'orderby'             => 'post_count',
				'order'               => 'DESC',
				'include'             => '',
				'has_published_posts' => null,
			],
			$atts,
			'wp_team_list'
		);

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
		require_once ABSPATH . 'wp-admin/includes/user.php';

		$user_roles = [ 'all' => __( 'All', 'wp-team-list' ) ];
		foreach ( get_editable_roles() as $role => $data ) {
			$user_roles[ $role ] = $data['name'];
		}

		$shortcode_ui_args = [
			'label'         => __( 'Team List', 'wp-team-list' ),
			'listItemImage' => 'dashicons-groups',
			'attrs'         => [
				[
					'label'   => __( 'Role', 'wp-team-list' ),
					'attr'    => 'role',
					'type'    => 'select',
					'value'   => 'administrator',
					'options' => $user_roles,
				],
				[
					'label'   => __( 'Order By', 'wp-team-list' ),
					'attr'    => 'orderby',
					'type'    => 'select',
					'value'   => 'post_count',
					'options' => [
						'post_count' => __( 'Post Count', 'wp-team-list' ),
					],
				],
				[
					'label'   => __( 'Order', 'wp-team-list' ),
					'attr'    => 'order',
					'type'    => 'radio',
					'value'   => 'desc',
					'options' => [
						'asc'  => __( 'Ascending', 'wp-team-list' ),
						'desc' => __( 'Descending', 'wp-team-list' ),
					],
				],
			],
		];

		shortcode_ui_register_for_shortcode( 'wp_team_list', $shortcode_ui_args );
	}

	/**
	 * Retrieve data of user's first role.
	 *
	 * @param \Required\WPTeamList\WP_User $user User object.
	 * @param string                       $field Optional. Field to retrieve. Accepts 'name' and
	 *                                            'display_name'. Default: 'display_name'.
	 * @return string|false Field value on success, false otherwise.
	 */
	public function get_user_role( WP_User $user, $field = 'display_name' ) {
		$role = current( $user->roles );

		switch ( $field ) {
			case 'display_name':
				$role_names = $GLOBALS['wp_roles']->get_names();
				// phpcs:ignore WordPress.WP.I18n
				$role_name = translate_with_gettext_context( $role_names[ $role ], 'User role', 'wp-team-list' );

				/**
				 * Filter the display name of user's role displayed in the team list.
				 *
				 * @param string                       $role_name Role name.
				 * @param \Required\WPTeamList\WP_User $user      User object.
				 */
				return apply_filters( 'wp_team_list_user_role', $role_name, $user );

			case 'name':
				return $role;
		}

		return false;
	}

	/**
	 * Filter the list of stylesheets enqueued in the editor.
	 *
	 * @param string $stylesheets Comma-separated list of editor stylesheets.
	 * @return string Modified stylesheet list.
	 */
	public function filter_mce_css( $stylesheets ) {
		$styles = explode( ',', $stylesheets );

		$style = is_rtl() ? 'assets/css/style-rtl.css' : 'assets/css/style.css';
		$styles[] = plugins_url( $style, plugin_dir_path( __FILE__ ) );

		return implode( ',', $styles );
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
		 * @param int     $size Avatar size. Default 90.
		 * @param \Required\WPTeamList\WP_User $user Current user object.
		 */
		$size = apply_filters( 'wp_team_list_avatar_size', 90, $user );

		return get_avatar( $user->ID, $size );
	}

	/**
	 * Registers the team list block.
	 *
	 * Registers all block assets so that they can be enqueued through Gutenberg in
	 * the corresponding context.
	 *
	 * @see https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type/#enqueuing-block-scripts
	 */
	public function register_block_type() {
		wp_register_script(
			'wp-team-list-block-editor',
			plugins_url( 'assets/js/editor.js', plugin_dir_path( __FILE__ ) ),
			[
				'lodash',
				'wp-blocks',
				'wp-components',
				'wp-data',
				'wp-element',
				'wp-editor',
				'wp-i18n',
				'wp-url',
			],
			self::VERSION,
			true
		);

		wp_set_script_translations( 'wp-team-list-block-editor', 'wp-team-list' );

		wp_register_style(
			'wp-team-list-editor',
			plugins_url( 'assets/css/editor.css', plugin_dir_path( __FILE__ ) ),
			[],
			self::VERSION
		);

		wp_styles()->add_data( 'wp-team-list-editor', 'rtl', true );

		register_block_type(
			'required/wp-team-list',
			[
				'editor_script'   => 'wp-team-list-block-editor',
				'editor_style'    => 'wp-team-list-editor',
				'style'           => 'wp-team-list-block',
				'render_callback' => [ $this, 'render_team_list_block' ],
				'attributes'      => [
					'number'          => [
						'type'    => 'integer',
						'default' => 10,
						'minimum' => 1,
						'maximum' => 100,
					],
					'showLink'        => [
						'type'    => 'bool',
						'default' => true,
					],
					'showDescription' => [
						'type'    => 'bool',
						'default' => true,
					],
					'roles'           => [
						'type'    => 'array',
						'default' => [],
						'items'   => [
							'type' => 'string',
						],
					],
					'orderBy'         => [
						'type'    => 'string',
						'default' => 'post_count',
						'enum'    => [
							'post_count',
							'name',
							'first_name',
							'last_name',
						],
					],
					'order'           => [
						'type'    => 'string',
						'default' => 'desc',
						'enum'    => [ 'asc', 'desc' ],
					],
				],
			]
		);
	}

	/**
	 * Registers the REST API routes.
	 */
	public function register_rest_routes() {
		$controller = new REST\UserController();
		$controller->register_routes();

		$controller = new REST\UserRolesController();
		$controller->register_routes();
	}

	/**
	 * Render team list block.
	 *
	 * @param array $attributes Block attributes.
	 * @return string Block content
	 */
	public function render_team_list_block( $attributes ) {
		$attributes_mappings = [
			'number'          => 'number',
			'showDescription' => 'show_description',
			'order'           => 'order',
			'orderBy'         => 'orderby',
			'roles'           => 'role',
		];

		$prepared_args = [];

		foreach ( $attributes_mappings as $block_attr => $wp_param ) {
			if ( isset( $attributes[ $block_attr ] ) ) {
				$prepared_args[ $wp_param ] = $attributes[ $block_attr ];
			}
		}

		$html = wp_team_list()->render( $prepared_args );

		if ( ! empty( $attributes['postId'] ) ) {
			$html .= sprintf(
				'<a href="%s" class="show-all">%s</a>',
				esc_url( get_permalink( $attributes['postId'] ) ),
				esc_html__( 'Show all team members', 'wp-team-list' )
			);
		}

		return $html;
	}
}
