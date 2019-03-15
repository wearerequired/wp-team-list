<?php
/**
 * Holds the User Roles REST Controller class.
 *
 * @package WP_Team_List
 */

namespace Required\WPTeamList\REST;

use WP_Error;
use WP_REST_Controller;
use WP_REST_Server;

/**
 * UserRolesController class.
 */
class UserRolesController extends WP_REST_Controller {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->namespace = 'wp-team-list/v1';
		$this->rest_base = 'roles';
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
				],
			]
		);
	}

	/**
	 * Permissions check for getting all user roles.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return true|\WP_Error True if the request has read access, otherwise WP_Error object.
	 */
	public function get_items_permissions_check( $request ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		if ( ! current_user_can( 'edit_posts' ) ) {
			return new WP_Error( 'rest_forbidden_list_roles', __( 'Sorry, you are not allowed to list user roles.', 'wp-team-list' ), [ 'status' => rest_authorization_required_code() ] );
		}

		return true;
	}

	/**
	 * Retrieves all user roles.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		// Required for get_editable_roles().
		require_once ABSPATH . 'wp-admin/includes/user.php';

		$user_roles = [];

		foreach ( get_editable_roles() as $role => $data ) {
			$user_roles[] = [
				'value' => $role,
				// phpcs:ignore WordPress.WP.I18n
				'label' => translate_with_gettext_context( $data['name'], 'User role', 'wp-team-list' ),
			];
		}

		return rest_ensure_response( $user_roles );
	}
}
