<?php
/**
 * A widget to display categories
 *
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta
 */
class CPT4BP_Categories_Widget extends WP_Widget
{
	/**
	 * Initialize the widget
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'widget_display_cpt4bp_categories',
			'description' => __( 'A list of categories.', 'cpt4bp' )
		);
		
		parent::__construct( false, __( 'CPT4BP Categories', 'cpt4bp' ), $widget_ops );
	}

	/**
	 * Display the widget
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */
	public function widget( $args, $instance ) {
		extract( $args );

		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';

		echo $before_widget;

		if( ! empty( $title ) )
			echo $before_title . $title . $after_title;
			
			?>
			<ul>
				<?php
				wp_list_categories( array(
					'orderby' 		=> 'name',
					'show_count' 	=> 0,
					'pad_counts' 	=> 0,
					'hierarchical' 	=> 1,
					'taxonomy' 		=> 'firmen_category',
					'title_li' 		=> '',
					'hide_empty' 	=> 1
				) );
				?>
			</ul>
			<?php 
			
		echo $after_widget;
	}

	/**
	 * Update any widget options
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Show the widget options form
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		
		?>
		<div>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>">
					<?php _e( 'Title:', 'cpt4bp' ) ?>
					<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title ?>" />
				</label>
			</p>
		</div>
		<?php
	}
}