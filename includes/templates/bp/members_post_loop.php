<?php get_header() ?>

    <div id="content">
        <div class="padder">

            <?php do_action( 'bp_before_postsonprofile_content' ) ?>

            <div id="item-header">
                <?php locate_template( array( 'members/single/member-header.php' ), true ) ?>
            </div>

            <div id="item-nav">
                <div class="item-list-tabs no-ajax" id="object-nav">
                    <ul>
                        <?php bp_get_displayed_user_nav() ?>
                    </ul>
                </div>
            </div>
            <div class="item-list-tabs no-ajax" id="subnav" role="navigation">
                <ul>
        
                    <?php bp_get_options_nav(); ?>
        
                </ul>
            </div><!-- .item-list-tabs -->

            <div id="item-body">
            
            <?php 
            global $wp_query, $bp;
           
		   	$cgt_post_ids = array();
            if ( bp_has_groups('user_id='.bp_displayed_user_id()) ) : 
                
                $groups_post_ids = array();
                
                while ( bp_groups() ) : bp_the_group(); 
                  
                    $group_post_id = groups_get_groupmeta( bp_get_group_id(), 'group_post_id' );
                    $group_type = groups_get_groupmeta( bp_get_group_id(), 'group_type' );
                    
                    if($group_type == $bp->current_component){
                        $cgt_post_ids[] = $group_post_id;
                    }
                    
                endwhile;
    
                do_action( 'bp_after_groups_loop' );
     
            endif;
			
            if( count( $cgt_post_ids ) <= 0 ) :
				echo '<div id="message" class="info"><p>Es wurden keine Resultate gefunden</p></div>';
				
			else :
	            $wp_query = new WP_Query( array( 'post_type' => $bp->current_component, 'post__in' => $cgt_post_ids ) );
	    
	            if ($wp_query->have_posts()) : 
	            	while ($wp_query->have_posts()) : $wp_query->the_post();
	                	
						?>
						<a class="clickable" href="<?php the_permalink() ?>" title="<?php _e( 'Permanent Link to', 'cc' ) . the_title_attribute(); ?>">			
							<?php the_title() ?>
						</a>
						<?php
	            	endwhile;
	            endif;
			endif;

             do_action( 'bp_after_postsonprofile_body' ) ?>                

            </div><!-- #item-body -->

            <?php do_action( 'bp_after_postsonprofile_content' ) ?>
            

        </div><!-- .padder -->
    </div><!-- #content -->
<?php get_footer() ?>