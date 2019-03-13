<?php
/**
 * Holds the User REST Controller class.
 *
 * @package WP_Team_List
 */

namespace Required\WPTeamList\REST;

use WP_Error;
use WP_REST_Controller;
use WP_REST_Server;

/**
 * WP_Team_List class.
 */
class UserController extends WP_REST_Controller {

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
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			[
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_items' ],
					'permission_callback' => [ $this, 'get_items_permissions_check' ],
					'args'                => $this->get_collection_params(),
				],
			]
		);
	}

	/**
	 * Permissions check for getting all users.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return true|\WP_Error True if the request has read access, otherwise WP_Error object.
	 */
	public function get_items_permissions_check( $request ) {
		$can_view = false;
		$types    = get_post_types( [ 'show_in_rest' => true ], 'objects' );

		foreach ( $types as $type ) {
			if (
				post_type_supports( $type->name, 'author' ) &&
				current_user_can( $type->cap->edit_posts )
			) {
				$can_view = true;
			}
		}

		if ( ! $can_view ) {
			return new WP_Error( 'rest_forbidden_who', __( 'Sorry, you are not allowed to query users.', 'wp-team-list' ), [ 'status' => rest_authorization_required_code() ] );
		}

		return true;
	}

	/**
	 * Retrieves all users.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		// Retrieve the list of registered collection query parameters.
		$registered = $this->get_collection_params();

		$parameter_mappings = [
			'order'    => 'order',
			'orderby'  => 'orderby',
			'per_page' => 'number',
			'role'     => 'role',
		];

		$prepared_args = [];

		foreach ( $parameter_mappings as $api_param => $wp_param ) {
			if ( isset( $registered[ $api_param ], $request[ $api_param ] ) ) {
				$prepared_args[ $wp_param ] = $request[ $api_param ];
			}
		}

		$result = [];
		$users  = wp_team_list()->get_users( $prepared_args );

		add_filter( 'rest_avatar_sizes', [ $this, 'set_avatar_sizes' ] );
		foreach ( $users as $user_id ) {
			$user = get_userdata( $user_id );

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
		remove_filter( 'rest_avatar_sizes', [ $this, 'set_avatar_sizes' ] );


		return rest_ensure_response( $result );
	}

	/**
	 * Sets the pixel sizes for avatars.
	 *
	 * @return array List of pixel sizes for avatars.
	 */
	public function set_avatar_sizes() {
		return [ 90, 180 ];
	}

	/**
	 * Retrieves the query params for collections.
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		// Required for get_editable_roles().
		require_once ABSPATH . 'wp-admin/includes/user.php';

		$user_roles = [ 'all' ];
		$user_roles = array_merge(
			$user_roles,
			array_keys( get_editable_roles() )
		);

		return [
			'order'    => [
				'description' => __( 'Order sort attribute ascending or descending.', 'wp-team-list' ),
				'type'        => 'string',
				'default'     => 'desc',
				'enum'        => [ 'asc', 'desc' ],
			],
			'orderby'  => [
				'description' => __( 'Sort collection by object attribute.', 'wp-team-list' ),
				'type'        => 'string',
				'default'     => 'post_count',
				'enum'        => [
					'post_count',
					'name',
					'first_name',
					'last_name',
				],
			],
			'role'     => [
				'description' => __( 'Limit result set to users matching one specific role provided.', 'wp-team-list' ),
				'type'        => 'string',
				'default'     => 'all',
				'enum'        => $user_roles,
			],
			'per_page' => [
				'description'       => __( 'Maximum number of items to be returned in result set.', 'wp-team-list' ),
				'type'              => 'integer',
				'default'           => 10,
				'minimum'           => 1,
				'maximum'           => 100,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			],
		];
	}
}
