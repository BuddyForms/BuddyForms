<?php
/**
 * A widget to display groups
 *
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta
 */
class BP_CGT_Groups_Widget extends WP_Widget
{
	/**
	 * Initialize the widget
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'widget_display_cgt_groups',
			'description' => __( 'A list of groups.', 'cgt' )
		);
		
		parent::__construct( false, __( 'CGT Groups', 'cgt' ), $widget_ops );
	}

	/**
	 * Display the widget
	 *
	 * @package BuddyPress Custom Group Types
	 * @since 0.1-beta
	 */
	public function widget( $args, $instance ) {
	   	global $post, $group_type, $wp_query, $cgt;

		extract( $args );

		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';

	   	$this_post_id = $post->ID;
	        
	   	if( bp_has_groups() ) : 
	   		while ( bp_groups() ) : bp_the_group();
	    		$group_type = groups_get_groupmeta( bp_get_group_id(), 'group_type' );
		            
	    		$attached_group_id = get_post_meta( $this_post_id, '_'. $group_type .'_attached', true );
	            $attached_tax_name = get_post_meta( $this_post_id, '_'. $group_type .'_attached_tax_name', true );
	            $term =  get_term_by( 'id', $attached_group_id, $attached_tax_name );
	            $group_slug = bp_get_group_slug();
	       	endwhile;
		endif; 

		echo $before_widget;
		
		if( ! empty( $title ) )
			echo $before_title . $title . $after_title;

		    if( bp_has_groups( 'slug='. $term->slug ) ) :
		       	while ( bp_groups() ) : bp_the_group();
		       		$firma_post_type_post_id = groups_get_groupmeta( bp_get_group_id(), 'group_post_id' );
		            
		            if ( $firma_post_type_post_id != '' && $group_type == 'product' && bp_group_is_visible()  ) {
		            	
						$com_query = new WP_Query( array( 'post_type' => 'firmen', 'p' => $firma_post_type_post_id ) );        
			
		                $get_the_post_thumbnail_attr = array(
		                    'class' => "avatar",
		                );
			        
		                if( $com_query->have_posts() ){
		                    $tmp .= '<div id="item-list" class="widget widget_from_firma"><!-- Begin Widget Firma -->'; 
		                    $tmp .= '<div><ul>';
		                    $tmp .= '<h3 class="widgettitle">Anbieter</h3>';
		            
		                    /**
							 * @TODO HTML is not semantic
							 */
		                    while ( $com_query->have_posts() ) : $com_query->the_post();
		                        if ( $this_post_id != $post->ID ) {
		                            $tmp .= '<a href="'.get_permalink().'" title="'.the_title_attribute(array('echo'=> 0)).'" class="clickable_box">';
			                            $tmp .= '<li>';
				                            $tmp .= get_the_post_thumbnail($post->ID , 'post-thumbnails' , $get_the_post_thumbnail_attr);
				                            $tmp .= '<h3 class="firma_name">'.get_the_title().'</h3>';
				                            $tmp .= '<div class="firma_slogan">'.get_post_meta(get_the_ID(), 'Slogen', true).'</div>';
			                            $tmp .= '</li>';
		                            $tmp .= '</a>';
		                            $tmp .= '<div class="clear"></div>';
		                        }
		                    endwhile;
			                
		                    $tmp .= '</ul></div>';
		                    $tmp .= '</div><!-- End Widget Ansprechpartner -->';
			                    
		                    $tmp .= '<div class="clear"></div>';
			                    
		                    echo $tmp;
			                    
		                    // Reset $tmp and the Query
		                    $tmp = '';
		                    wp_reset_query();
		                }	            
		            }
		       endwhile;
		   endif;
		   
			if ( bp_has_groups() ) : 
				while ( bp_groups() ) : bp_the_group();    
					if ( bp_group_is_visible() ) :
						?>
						<!-- Begin Widget Ansprechpartner -->
						<div id="item-list" class="widget widget_ansprechpartner">
							<div>
								<h3 style="" class="widgettitle">Ansprechpartner</h3>
								<?php 
								bp_group_list_admins();
								
								if( bp_group_has_moderators() ) :
									bp_group_list_mods();
								endif;
								?>
							</div>
						</div>
						<div class="clear"></div>
						<!-- End Widget Ansprechpartner -->
						<?php
					endif;
					
					if( $group_type != 'firmen' ) {
			    		$args = array(
			                'post_type'			=> 'product',
			                $attached_tax_name 	=> $term->slug,
			                'order'    			=> 'ASC',
			            );    
					} else {
					  $args = array(
			                'post_type'				  => 'product',
			                'product_attached_firmen' => $group_slug,
			                'order'    				  => 'ASC',
			            );    
			        }
					
					$gr_query = new WP_Query( $args );
			
					if( $gr_query->have_posts() ){
					    
					    if( $group_type == 'product' )
			                $h3_widget_title = '<h3 class="widgettitle">Andere Apps des Anbieters</h3>';
			    
						elseif( $group_type == 'firmen' )
			                $h3_widget_title .= '<h3 class="widgettitle">Apps des Anbieters</h3>';
						 
						$tmp .= '<div class="widget widget_apps_from_company"><!-- Begin Widget Apps des Anbieters / Andere Apps -->';
						$tmp .= '<div><ul>';
						
						$tmp .= $h3_widget_title;
						
		                /**
						 * @TODO HTML is not semantic
						 */
						while( $gr_query->have_posts() ) : $gr_query->the_post();
							if ( $this_post_id != $post->ID ) {
								$tmp .= '<a href="'.get_permalink().'" title="'.the_title_attribute(Array('echo'=> 0)).'" class="clickable_box">';
								$tmp .= '<li>';
								$tmp .= get_the_post_thumbnail($post->ID ,array(50, 50));
								$tmp .= '<h4>'.get_the_title().'</h4>';
								// $tmp .= '<div class="app_slogan">Hier kommt ein kurzer Slogan des Apps hin. </div>';
								$tmp .= '</li>';
								$tmp .= '</a>';
								$tmp .= '<div class="clear"></div>';
							}
						endwhile;
					
						$tmp .= '</ul></div></div>';				
						$tmp .= '<div class="clear"></div>';
						
						echo $tmp;
						
						// Reset $tmp and the Query
						$tmp = '';
						wp_reset_query();
					}	        
				endwhile;
			endif;
			
			do_action( 'bp_after_group_header' );
			do_action( 'template_notices' );	
		   
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