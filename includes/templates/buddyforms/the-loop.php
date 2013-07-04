<?php 

global $buddyforms, $bp, $the_lp_query, $current_user, $form_slug;
	get_currentuserinfo();	
// echo '<pre>';
// print_r($the_lp_query);
// echo '</pre>';


if ( $the_lp_query->have_posts() ) : ?>

	<ul id="buddyforms-list" class="item-list" role="main">

    <?php while ( $the_lp_query->have_posts() ) : $the_lp_query->the_post();
		
		if(get_post_status() == 'pending' || get_post_status() == 'draft'){
			$the_permalink = trailingslashit( bp_loggedin_user_domain() ).get_post_type().'?post_id='.get_the_ID();
		} else {
			$the_permalink = get_permalink();
		} 
		do_action( 'bp_before_blog_post' ) ?>

		<li class="<?php echo get_post_status(); ?>">
			<div class="item-avatar">
				<a href="<?php echo $the_permalink; ?>"><?php the_post_thumbnail( array(70,70),array('class'=>"avatar")); ?></a>
			</div>

			<div class="item">
				<div class="item-title"><a href="<?php echo $the_permalink; ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'cc' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></div>

				<div class="item-desc"><?php the_excerpt(); ?></div>

				<?php do_action( 'bp_directory_groups_item' ); ?>

			</div>

			<div class="action">
				<?php _e( 'Created', 'buddyforms' ); ?> <?php the_time('F j, Y') ?>
				
				
				<?php
			
				if (get_the_author_meta('ID') ==  get_current_user_id()){
					$permalink = get_permalink( $buddyforms['buddyforms'][$form_slug]['attached_page'] );
					
					$post_status = get_post_status();
					if( $post_status == 'publish')
						$post_status = 'published';
					?>
					<div class="meta">
						<div class="item-status"><?php echo $post_status; ?></div>
						<a title="Edit me" href='<?php echo $permalink.'edit/'.$form_slug.'/'.get_the_ID() ?>'><?php _e( 'Edit', 'buddyforms' ); ?></a>
						- <a title="Delete me" onclick="return confirm('Are you sure you want to delete this entry?');" href='<?php echo $permalink.'delete/'.$form_slug.'/'.get_the_ID() ?>'><?php _e( 'Delete', 'buddyforms' ); ?></a>
					</div>
				<?php } ?>
			</div>

			<div class="clear"></div>
		</li>

        <?php do_action( 'bp_after_blog_post' ) ?>

    <?php endwhile; ?>

    <div class="navigation">

    <?php if(function_exists('wp_pagenavi')) : wp_pagenavi(); else: ?>
        <div class="alignleft"><?php next_posts_link( __( '&larr; Previous Entries', 'buddyforms' ) ) ?></div>
        <div class="alignright"><?php previous_posts_link( __( 'Next Entries &rarr;', 'buddyforms' ) ) ?></div>
    <?php endif; ?>

    </div>

	</ul>

<?php else : ?>

	<div id="message" class="info">
		<p><?php _e( 'There were no posts found.', 'buddyforms' ); ?></p>
	</div>

<?php endif; ?>
