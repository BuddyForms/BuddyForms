<?php
global $buddyforms, $bp, $the_lp_query, $current_user, $form_slug;
	get_currentuserinfo();

if ( $the_lp_query->have_posts() ) : ?>

	<ul class="buddyforms-list" role="main">

    <?php while ( $the_lp_query->have_posts() ) : $the_lp_query->the_post();
		
        $the_permalink = get_permalink();


		$post_status = get_post_status();

		$post_status_css =  $post_status_name  = $post_status;

        if( $post_status == 'pending')
            $post_status_css = 'bf-pending';

		if( $post_status == 'publish')
			$post_status_name = 'published';


		$post_status_css = apply_filters('bf_post_status_css',$post_status_css,$form_slug);


		do_action( 'bp_before_blog_post' ) ?>

		<li class="<?php echo $post_status_css; ?>">
			<div class="item-avatar">
				
				<?php 
				$post_thumbnail = get_the_post_thumbnail( get_the_ID(), array(70,70),array('class'=>"avatar"));
				$post_thumbnail = apply_filters( 'buddyforms_loop_thumbnail', $post_thumbnail);
				?>
				
				<a href="<?php echo $the_permalink; ?>"><?php echo $post_thumbnail ?></a>
			</div>

			<div class="item">
				<div class="item-title"><a href="<?php echo $the_permalink; ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddyforms' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></div>

				<div class="item-desc"><?php echo get_the_excerpt(); ?></div>

			</div>

			<div class="action">
				<?php _e( 'Created', 'buddyforms' ); ?> <?php the_time('F j, Y') ?>
				
				
				<?php
				if (get_the_author_meta('ID') ==  get_current_user_id()){
					$permalink = get_permalink( $buddyforms['buddyforms'][$form_slug]['attached_page'] ); ?>

					<div class="meta">
						<div class="item-status"><?php echo $post_status_name; ?></div>
                        <?php if( current_user_can('buddyforms_'.$form_slug.'_edit') ) {

                            if(isset($buddyforms['buddyforms'][$form_slug]['edit_link']) && $buddyforms['buddyforms'][$form_slug]['edit_link'] == 'my-posts-list') { ?>
                                <a title="Edit me" href='<?php echo $permalink.'edit/'.$form_slug.'/'.get_the_ID() ?>'><?php _e( 'Edit', 'buddyforms' ); ?></a>
                            <?php } else { ?>
                                <? echo 'sa'.$buddyforms['buddyforms'][$form_slug]['edit_link']; edit_post_link('Edit'); ?>
                            <?php } ?>
                        <?php } ?>
                        <?php if( current_user_can('buddyforms_'.$form_slug.'_delete') ) { ?>
						    - <a title="Delete me" onclick="return confirm(__('Are you sure you want to delete this entry?', 'buddyforms'));" href='<?php echo $permalink.'delete/'.$form_slug.'/'.get_the_ID() ?>'><?php _e( 'Delete', 'buddyforms' ); ?></a>
					    <?php } ?>
                    </div>
				<?php } ?>
			</div>

			<div class="clear"></div>
		</li>

        <?php do_action( 'bf_after_loop_item' ) ?>


    <?php endwhile; ?>

    <div class="navigation">
    <?php if(function_exists('wp_pagenavi')) : wp_pagenavi(); else: ?>
        <div class="alignleft"><?php next_posts_link( '&larr;' . __( ' Previous Entries', 'buddyforms' ), $the_lp_query->max_num_pages ) ?></div>
        <div class="alignright"><?php previous_posts_link( __( 'Next Entries ', 'buddyforms' ) . '&rarr;' ) ?></div>
    <?php endif; ?>

    </div>

	</ul>

<?php else : ?>

	<div id="message" class="info">
		<p><?php _e( 'There were no posts found.', 'buddyforms' ); ?></p>
	</div>

<?php endif; ?>
