<?php
/**
 * WP Team List: Widget
 *
 * Creates a WordPress widget with support for caching that handles the display of the WP Team
 * List in your sidebars.
 */

namespace Required\WPTeamList;

use WP_Widget;

/**
 * Class WP_Team_List_Widget
 */
class Widget extends WP_Widget {
	/**
	 * Register the widget and setup the defaults.
	 */
	public function __construct() {
		$widget_ops = [
			'classname'             => 'widget_wp_team_list',
			'description'           => __( 'Display users as team members.', 'wp-team-list' ),
			'show_instance_in_rest' => true,
		];

		parent::__construct( 'wp-team-list', __( 'WP Team List', 'wp-team-list' ), $widget_ops );
	}

	/**
	 * Display the widget's content.
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Instance data.
	 */
	public function widget( $args, $instance ) {
		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		// Prepare options.
		$title     = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Editors', 'wp-team-list' );
		$role      = ! empty( $instance['role'] ) ? $instance['role'] : 'editor';
		$show_link = isset( $instance['show_link'] ) ? $instance['show_link'] : false;
		$page_link = isset( $instance['page_link'] ) ? absint( $instance['page_link'] ) : 0;
		$number    = ! empty( $instance['number'] ) ? max( 1, absint( $instance['number'] ) ) : 3;

		/**
		 * Filter the team list widget title.
		 *
		 * @param string  $title    The widget title.
		 * @param   array $instance An array of the widget's settings.
		 * @param string  $id_base  The widget ID.
		 */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		// A filter for all instances of this widget.
		$team_query_args = [
			'role'   => $role,
			'number' => $number,
		];

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		?>
		<div class="wp-team-list-widget-content">
			<?php
			echo wp_team_list()->render( $team_query_args ); // phpcs:ignore WordPress.Security.EscapeOutput
			if ( $show_link && $page_link ) {
				printf(
					'<a href="%s" class="show-all">%s</a>',
					esc_url( get_permalink( $page_link ) ),
					esc_html__( 'Show all team members', 'wp-team-list' )
				);
			}
			?>
		</div>
		<?php
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @param array $new_instance Old widget settings.
	 * @param array $old_instance New widget settings.
	 * @return array The widget settings to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance              = $old_instance;
		$instance['title']     = sanitize_text_field( $new_instance['title'] );
		$instance['role']      = sanitize_text_field( $new_instance['role'] );
		$instance['number']    = absint( $new_instance['number'] );
		$instance['show_link'] = isset( $new_instance['show_link'] );
		$instance['page_link'] = absint( $new_instance['page_link'] );

		return $instance;
	}

	/**
	 * Display the widget's form.
	 *
	 * @param array $instance Current widget instance.
	 * @return void
	 */
	public function form( $instance ) {
		$title     = isset( $instance['title'] ) ? $instance['title'] : '';
		$role      = isset( $instance['role'] ) ? $instance['role'] : 'editor';
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 3;
		$show_link = isset( $instance['show_link'] ) ? (bool) $instance['show_link'] : false;
		$page_link = isset( $instance['page_link'] ) ? absint( $instance['page_link'] ) : 0;
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'wp-team-list' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"/>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'role' ) ); ?>"><?php esc_html_e( 'Role:', 'wp-team-list' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'role' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'role' ) ); ?>" class="widefat">
				<option value="all" <?php selected( 'all', $role ); ?>><?php esc_html_e( 'All', 'wp-team-list' ); ?></option>
				<?php
				// Check if function exists when used with REST API.
				if ( ! function_exists( 'wp_dropdown_roles' ) ) {
					require_once ABSPATH . 'wp-admin/includes/template.php';
				}
				// Used by wp_dropdown_roles.
				if ( ! function_exists( 'get_editable_roles' ) ) {
					require_once ABSPATH . 'wp-admin/includes/user.php';
				}
				wp_dropdown_roles( $role );
				?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number of members to show:', 'wp-team-list' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3"/>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $show_link ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_link' ) ); ?>"/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_link' ) ); ?>"><?php esc_html_e( 'Show link to team page?', 'wp-team-list' ); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'page_link' ) ); ?>"><?php esc_html_e( 'Link to:', 'wp-team-list' ); ?></label>
			<?php
			wp_dropdown_pages(
				[
					'selected' => absint( $page_link ),
					'name'     => esc_attr( $this->get_field_name( 'page_link' ) ),
					'id'       => esc_attr( $this->get_field_id( 'page_link' ) ),
				]
			);
			?>
		</p>
		<?php
	}
}
