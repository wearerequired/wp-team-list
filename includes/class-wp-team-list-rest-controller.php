<?php
/**
 * Holds the WP Team List REST Controller class.
 *
 * @package WP_Team_List
 */

/**
 * WP_Team_List class.
 */
class WP_Team_List_REST_Controller extends WP_REST_Controller {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->namespace = 'wp-team-list/v1';
		$this->rest_base = 'users';
	}

	/**
	 * Registers the routes for the objects of the controller.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'                => [
					'order'    => [
						'description' => __( 'Order sort attribute ascending or descending.', 'wp-team-list' ),
						'type'        => 'string',
						'default'     => 'desc',
						'enum'        => array( 'asc', 'desc' ),
					],
					'orderby'  => [
						'description' => __( 'Sort collection by object attribute.', 'wp-team-list' ),
						'type'        => 'string',
						'default'     => 'post_count',
						'enum'        => array(
							'post_count',
							'name',
							'first_name',
							'last_name',
						),
					],
					'roles'    => [
						'description' => __( 'Limit result set to users matching at least one specific role provided. Accepts csv list or single role.', 'wp-team-list' ),
						'type'        => 'array',
						'items'       => array(
							'type' => 'string',
						),
					],
					'per_page' => array(
						'description'       => __( 'Maximum number of items to be returned in result set.', 'wp-team-list' ),
						'type'              => 'integer',
						'default'           => 10,
						'minimum'           => 1,
						'maximum'           => 100,
						'sanitize_callback' => 'absint',
						'validate_callback' => 'rest_validate_request_arg',
					),
				],
			],
		] );
	}

	/**
	 * Permissions check for getting all users.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, otherwise WP_Error object.
	 */
	public function get_items_permissions_check( $request ) {
		$can_view = false;
		$types    = get_post_types( array( 'show_in_rest' => true ), 'objects' );

		foreach ( $types as $type ) {
			if (
				post_type_supports( $type->name, 'author' ) &&
				current_user_can( $type->cap->edit_posts )
			) {
				$can_view = true;
			}
		}

		if ( ! $can_view ) {
			return new WP_Error( 'rest_forbidden_who', __( 'Sorry, you are not allowed to query users.', 'wp-team-list' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Retrieves all users.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		// Retrieve the list of registered collection query parameters.
		$registered = $this->get_collection_params();

		$parameter_mappings = [
			'order'    => 'order',
			'orderby'  => 'orderby',
			'per_page' => 'number',
			'roles'    => 'role',
		];

		$prepared_args = [];

		foreach ( $parameter_mappings as $api_param => $wp_param ) {
			if ( isset( $registered[ $api_param ], $request[ $api_param ] ) ) {
				$prepared_args[ $wp_param ] = $request[ $api_param ];
			}
		}

		$result = [];
		$users  = wp_team_list()->get_users( $prepared_args );

		foreach ( $users as $user ) {
			$result[] = [
				'id'                => $user->ID,
				'display_name'      => $user->display_name,
				'role'              => wp_team_list()->get_user_role( $user, 'name' ),
				'role_display_name' => wp_team_list()->get_user_role( $user, 'display_name' ),
				'description'       => $user->description,
				'link'              => get_author_posts_url( $user->ID, $user->user_nicename ),
				'post_count'        => count_user_posts( $user->ID ),
				'avatar_urls'       => rest_get_avatar_urls( $user->user_email ),
			];
		}

		return rest_ensure_response( $result );
	}
}
