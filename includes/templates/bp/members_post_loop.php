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

			<div id="item-body">
				
				
			<?php 
			if ( bp_has_groups('user_id='.bp_displayed_user_id()) ) : 
 
			$groups_post_ids = array();
				
			while ( bp_groups() ) : bp_the_group(); 
			 
				$groups_post_id = groups_get_groupmeta( bp_get_group_id(), 'group_post_id' );
				$group_type = groups_get_groupmeta( bp_get_group_id(), 'group_type' );
				
				if($group_type == 'product'){
					$groups_post_ids[] = $groups_post_id;
				}
				
			endwhile;

  
			do_action( 'bp_after_groups_loop' ) ?>
 
			<?php else: ?>
			 
			    <div id="message" class="info">
			        <p><?php _e( 'There were no groups found.', 'buddypress' ) ?></p>
			    </div>
			 
			<?php endif; ?>
				
            <?php
            global $list_post_atts, $list_post_query, $wp_query, $tkf;
            $arrayindex = $tkf->woocommerce_list_products_style;
			$list_post_atts = create_template_builder_args($arrayindex);
			
			echo list_posts_template_builder_css();
			//echo list_posts_template_builder_js();	// hier muss das js für mause over noch rauß geholt werden

			$wp_query = new WP_Query( array( 'post_type' => 'product', 'post__in' => $groups_post_ids ) );

	
			if ($wp_query->have_posts()) : while ($wp_query->have_posts()) : $wp_query->the_post();
				get_template_part( 'the-loop-item' );
			endwhile; endif;

			 do_action( 'bp_after_postsonprofile_body' ) ?>                

			</div><!-- #item-body -->

			<?php do_action( 'bp_after_postsonprofile_content' ) ?>
			

		</div><!-- .padder -->
	</div><!-- #content -->
<?php get_footer() ?>
