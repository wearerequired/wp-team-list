<?php

/**
 * WP Team List: Widget
 *
 * Creates a WordPress widget with support for caching that handles the display of the WP Team
 * List in your sidebars.
 *
 * @since 0.4.0
 */
class WP_Team_List_Widget extends WP_Widget {

	public function __construct() {

		$widget_ops = array(
			'classname'   => 'widget_wp_team_list',
			'description' => __( 'Display users as team members.', 'wp-team-list' )
		);

		parent::__construct( 'wp-team-list', __( 'WP Team List', 'wp-team-list' ), $widget_ops );

		// Our option name
		$this->alt_option_name = 'widget_wp_team_list';

		// Flush the cache whenever it could change the widget content
		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
		add_action( 'show_user_profile', array( $this, 'flush_widget_cache' ) );
		add_action( 'edit_user_profile', array( $this, 'flush_widget_cache' ) );
	}

	public function widget( $args, $instance ) {

		$cache = wp_cache_get( 'widget_wp_team_list', 'widget' );

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];

			return;
		}

		ob_start();

		extract( $args );

		// Prepare options
		$title     = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Editors', 'wp-team-list' );
		$role      = ( ! empty( $instance['role'] ) ) ? $instance['role'] : 'editor';
		$show_link = isset( $instance['show_link'] ) ? $instance['show_link'] : false;
		$page_link = isset( $instance['page_link'] ) ? absint( $instance['page_link'] ) : 7;
		$number    = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 3;

		// Filter the title
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		// Set a default number
		if ( ! $number ) {
			$number = 3;
		}

		// A filter for all instances of this widget
		$team_query_args = apply_filters( 'rplus_wp_team_list_widget_args', array( 'role'   => $role,
		                                                                           'number' => $number
		), $instance, $this->id_base );

		// Allows you to filter the template file name
		$team_widget_template = apply_filters( 'rplus_wp_team_list_widget_template', 'rplus-wp-team-list-widget.php', $instance, $this->id_base );


		// START: Widget Content
		echo $before_widget;

		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		echo apply_filters( 'rplus_wp_team_list_widget_before', '<div class="mini-team-list">' );

		/**
		 * Renders each individual team member.
		 *
		 * This is the basic template function or call it template tag to render the
		 * team list according to the args set.
		 *
		 * @uses WP_Team_List->render_team_list( $args, $echo, $template );
		 */
		rplus_wp_team_list( $team_query_args, $echo = true, $team_widget_template );

		if ( $show_link ) : ?>
			<a href="<?php echo esc_url( get_permalink( $page_link ) ); ?>" class="show-all"><?php _e( 'Show all Team Members', 'wp-team-list' ); ?></a><?php
		endif;

		echo apply_filters( 'rplus_wp_team_list_widget_after', '</div>' );

		echo $after_widget;
		// END: Widget Content


		// Handle the widget cache and flush it!
		$cache[ $args['widget_id'] ] = ob_get_flush();
		wp_cache_set( 'widget_wp_team_list', $cache, 'widget' );
	}

	public function update( $new_instance, $old_instance ) {

		$instance              = $old_instance;
		$instance['title']     = strip_tags( $new_instance['title'] );
		$instance['role']      = strip_tags( $new_instance['role'] );
		$instance['number']    = (int) $new_instance['number'];
		$instance['show_link'] = isset( $new_instance['show_link'] ) ? (bool) $new_instance['show_link'] : false;
		$instance['page_link'] = (int) $new_instance['page_link'];

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );

		if ( isset( $alloptions['widget_wp_team_list'] ) ) {
			delete_option( 'widget_wp_team_list' );
		}

		return $instance;
	}

	public function flush_widget_cache() {

		wp_cache_delete( 'widget_wp_team_list', 'widget' );

	}

	public function form( $instance ) {

		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$role      = isset( $instance['role'] ) ? esc_attr( $instance['role'] ) : 'editor';
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 3;
		$show_link = isset( $instance['show_link'] ) ? (bool) $instance['show_link'] : false;
		$page_link = isset( $instance['page_link'] ) ? (bool) $instance['page_link'] : 7;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'wp-team-list' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'role' ); ?>"><?php _e( 'Role:', 'wp-team-list' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'role' ); ?>" name="<?php echo $this->get_field_name( 'role' ); ?>" class="widefat">
				<?php wp_dropdown_roles( $role ); ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of members to show:', 'wp-team-list' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" />
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $show_link ); ?> id="<?php echo $this->get_field_id( 'show_link' ); ?>" name="<?php echo $this->get_field_name( 'show_link' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_link' ); ?>"><?php _e( 'Show link to team page?', 'wp-team-list' ); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'page_link' ); ?>"><?php _e( 'Link to:', 'wp-team-list' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'page_link' ); ?>" name="<?php echo $this->get_field_name( 'page_link' ); ?>" class="widefat">
				<?php
				$pages = get_pages( array( 'orderby' => 'name', 'parent' => 0 ) );
				foreach ( $pages as $page_link ) {
					$option = '<option value="' . $page_link->ID . '" ' . selected( $instance['page_link'], $page_link->ID ) . '>';
					$option .= $page_link->post_title;
					$option .= '</option>';
					echo $option;
				}
				?>
			</select>
		</p>
	<?php
	}
}