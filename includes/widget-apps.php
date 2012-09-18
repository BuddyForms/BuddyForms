<?php
/**
 * A widget to display apps
 *
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta
 */
class BP_CGT_Apps_Widget extends WP_Widget
{
	/**
	 * Initialize the widget
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'widget_display_cgt_apps',
			'description' => __( 'A list of apps.', 'cgt' )
		);
		
		parent::__construct( false, __( 'CGT Apps', 'cgt' ), $widget_ops );
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
			<script type="text/javascript">
			jQuery(document).ready(function($){
				$("#accordion_filter div.swap1").hide();
					$("#accordion_filter h3").click(function(){
						$(this).next("div.swap1").slideToggle("slow").siblings("div.swap1:visible").slideUp("slow");
						$(this).toggleClass("active");
						$(this).siblings("h3").removeClass("active");
					});
			});
			</script>
			
			<ul>
				<?php
				wp_list_categories( array(
					'orderby' 		=> 'name',
					'show_count' 	=> 0,
					'pad_counts' 	=> 0,
					'hierarchical' 	=> 1,
					'taxonomy' 		=> 'product_cat',
					'title_li' 		=> '',
					'hide_empty' 	=> 0,
					'show_count' 	=> 1
				) );
				?>
			</ul>

			<div id="categories-apps-filter">
				<h4>Suche verfeinern</h4>
				<?php 
				$categories = get_categories( array(
					'type'			=> 'post',
					'hierarchical'	=> true,
					'taxonomy'		=> 'product_filter'		
				
				) );
				?>
			
				<form class="taxonomy-drilldown-dropdowns" action="" method="get">
					<div id="accordion_filter" class="accordion">
					<?php
					foreach( $categories as $category ) {
						if( $category->category_parent == 0 ){
							echo '<h3><span><b>'. $category->cat_name .'</b></span></h3>';
							
							$sub_cats = get_categories( array(
								'type'			=> 'post',
								'child_of' 		=> $category->cat_ID,
								'hide_empty' 	=> false,
								'hierarchical'	=> true,
								'taxonomy'		=> 'product_filter'		
							) );
							
							echo '<div class="swap1" style="display: none;">';
								echo '<ul>';
								foreach( $sub_cats as $sub_cat ) {
									if( $sub_cat->category_parent != 0 ){
										echo ' <li> ';
											echo ' <input type="checkbox" name="filter[]" '. filter_checked($sub_cat->cat_name) .' value="'. $sub_cat->cat_name .'" />';
											echo ' <span>'. $sub_cat->cat_name.' ('.  $sub_cat->count.' )</span>';
										echo ' </li> '; 
									}		
								}
								echo '</ul>';
							echo '</div>';
							echo '<div id="filter_border" class="border"></div>';					
						}
					}
					?>
					</div>
					<input type="submit" value="Submit">
				</form>
			</div>
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
					<?php _e( 'Title:', 'cgt' ) ?>
					<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title ?>" />
				</label>
			</p>
		</div>
		<?php
	}
}