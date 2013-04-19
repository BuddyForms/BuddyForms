<?php
/**
 * A widget to display products
 *
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta
 */
class CPT4BP_Product_Widget extends WP_Widget
{
	/**
	 * Initialize the widget
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'widget_display_cpt4bp_products',
			'description' => __( 'A list of products.', 'cpt4bp' )
		);
		
		parent::__construct( false, __( 'CPT4BP Proucts', 'cpt4bp' ), $widget_ops );
	}

	/**
	 * Display the widget
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */
	public function widget( $args, $instance ) {
		global $post, $group_type, $wc_query;

		extract( $args );

		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';

		$groups_post_id = groups_get_groupmeta( bp_get_group_id(), 'group_post_id' );
		$group_type 	= groups_get_groupmeta( bp_get_group_id(), 'group_type'    );
	
		$wc_query = new WP_Query( array( 'post_type' => $group_type, 'p' => $groups_post_id ) );

		if ( $wc_query->have_posts() ) :
			echo $before_widget;

			if( ! empty( $title ) )
				echo $before_title . $title . $after_title;

			while ( $wc_query->have_posts() ) : $wc_query->the_post();
				switch( $group_type ) {
					case 'product':
						do_action( 'woocommerce_before_single_product' );
						
						?>			
						<div itemscope itemtype="http://schema.org/Product" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>
						
							<?php do_action( 'woocommerce_group_header_before_single_product_summary' ); ?>
						
							<div class="summary">
								
								<?php do_action( 'woocommerce_single_product_summary' ); ?>
							
							</div>
						
							<?php do_action( 'woocommerce_group_header_after_single_product_summary' ); ?>
						</div> 
						<?php
						break;
							
					default:
			           	echo get_the_post_thumbnail(get_the_ID(), array( 222, 160 ) );
			           	?>
			            <div class="summary">
			            	<h2 class="pagetitle"><a href="<?php the_permalink() ?>" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				            <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				                <div class="entry">
				                    <?php echo get_the_excerpt(); ?>
				                </div>
				            </div>
			            </div>
			            <?php
						break;
				}
			endwhile;
					
			echo $after_widget;
		endif;
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