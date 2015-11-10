<?php
/**
 * WP Team List: Widget
 *
 * Creates a WordPress widget with support for caching that handles the display of the WP Team
 * List in your sidebars.
 *
 * @package WP_Team_List
 */

/**
 * Class WP_Team_List_Widget
 */
class WP_Team_List_Widget extends WP_Widget {
	/**
	 * Register the widget and setup the defaults.
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'widget_wp_team_list',
			'description' => __( 'Display users as team members.', 'wp-team-list' ),
		);

		parent::__construct( 'wp-team-list', __( 'WP Team List', 'wp-team-list' ), $widget_ops );

		// Our option name.
		$this->alt_option_name = 'widget_wp_team_list';

		// Flush the cache whenever it could change the widget content.
		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
		add_action( 'show_user_profile', array( $this, 'flush_widget_cache' ) );
		add_action( 'edit_user_profile', array( $this, 'flush_widget_cache' ) );
	}

	/**
	 * Display the widget's content.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Instance data.
	 */
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

		// Prepare options.
		$title     = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Editors', 'wp-team-list' );
		$role      = ( ! empty( $instance['role'] ) ) ? $instance['role'] : 'editor';
		$show_link = isset( $instance['show_link'] ) ? $instance['show_link'] : false;
		$page_link = isset( $instance['page_link'] ) ? absint( $instance['page_link'] ) : 0;
		$number    = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 3;

		// Filter the title.
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		// Set a default number.
		if ( ! $number ) {
			$number = 3;
		}

		// A filter for all instances of this widget.
		$team_query_args = apply_filters( 'rplus_wp_team_list_widget_args', array( 'role'   => $role,
		                                                                           'number' => $number
		), $instance, $this->id_base );

		// Allows you to filter the template file name.
		$team_widget_template = apply_filters( 'rplus_wp_team_list_widget_template', 'rplus-wp-team-list-widget.php', $instance, $this->id_base );


		echo $args['before_widget'];

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		echo apply_filters( 'rplus_wp_team_list_widget_before', '<div class="mini-team-list">' );

		/*
		 * Renders each individual team member.
		 *
		 * This is the basic template function or call it template tag to render the
		 * team list according to the args set.
		 *
		 * @uses WP_Team_List->render_team_list( $args, $echo, $template );
		 */
		rplus_wp_team_list( $team_query_args, true, $team_widget_template );

		if ( $show_link && $page_link ) : ?>
			<a href="<?php echo esc_url( get_permalink( $page_link ) ); ?>" class="show-all"><?php esc_html_e( 'Show all Team Members', 'wp-team-list' ); ?></a><?php
		endif;

		echo apply_filters( 'rplus_wp_team_list_widget_after', '</div>' );

		echo $args['after_widget'];

		// Handle the widget cache and flush it!
		$cache[ $args['widget_id'] ] = ob_get_flush();
		wp_cache_set( 'widget_wp_team_list', $cache, 'widget' );
	}

	/**
	 * Form submission handler.
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
		$instance['show_link'] = (bool) $new_instance['show_link'];
		$instance['page_link'] = absint( $new_instance['page_link'] );

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );

		if ( isset( $alloptions['widget_wp_team_list'] ) ) {
			delete_option( 'widget_wp_team_list' );
		}

		return $instance;
	}

	/**
	 * Flush the whole widget cache.
	 */
	public function flush_widget_cache() {
		wp_cache_delete( 'widget_wp_team_list', 'widget' );
	}

	/**
	 * Display the widget's form.
	 *
	 * @param array $instance Current widget instance.
	 * @return void
	 */
	public function form( $instance ) {
		$title     = isset( $instance['title'] )     ? $instance['title']            : '';
		$role      = isset( $instance['role'] )      ? $instance['role']             : 'editor';
		$number    = isset( $instance['number'] )    ? absint( $instance['number'] ) : 3;
		$show_link = isset( $instance['show_link'] ) ? (bool) $instance['show_link'] : false;
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'wp-team-list' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'role' ) ); ?>"><?php esc_html_e( 'Role:', 'wp-team-list' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'role' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'role' ) ); ?>" class="widefat">
				<option value="all" <?php selected( 'all', $role ); ?>><?php esc_html_e( 'All', 'wp-team-list' ); ?></option>
				<?php wp_dropdown_roles( $role ); ?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number of members to show:', 'wp-team-list' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" />
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $show_link ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_link' ) ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_link' ) ); ?>"><?php esc_html_e( 'Show link to team page?', 'wp-team-list' ); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'page_link' ) ); ?>"><?php esc_html_e( 'Link to:', 'wp-team-list' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'page_link' ) ); ?>" name="<?php esc_html( $this->get_field_name( 'page_link' ) ); ?>" class="widefat">
				<?php
				$pages = get_pages( array( 'orderby' => 'name', 'parent' => 0 ) );
				foreach ( $pages as $page ) {
					printf(
						'<option value="%s" %s>%s</option>',
						esc_attr( $page->ID ),
						selected( $instance['page_link'], $page->ID ),
						get_the_title( $page )
					);
				}
				?>
			</select>
		</p>
	<?php
	}
}
