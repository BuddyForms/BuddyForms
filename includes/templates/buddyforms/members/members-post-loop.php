<?php 

global $bp, $the_lp_query;

if ( $the_lp_query->have_posts() ) : ?>

	<ul id="groups-list" class="item-list" role="main">

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
				Created <?php the_time('F j, Y') ?>
				<?php if ($bp->displayed_user->id ==  get_current_user_id()){ ?>
					<div class="meta">
						<div class="item-status"><?php echo get_post_status(); ?></div>
						<a href='<?php echo trailingslashit( bp_loggedin_user_domain() ).get_post_type().'?post_id='.get_the_ID(); ?>'>Edit</a>
						- <a onclick="return confirm('Are you sure you want to delete this entry?');" href='<?php echo trailingslashit( bp_loggedin_user_domain() ).get_post_type().'?delete='.get_the_ID() ?>'>Delete</a>
					</div>
				<?php } ?>
			</div>

			<div class="clear"></div>
		</li>

        <?php do_action( 'bp_after_blog_post' ) ?>

    <?php endwhile; ?>

    <div class="navigation">

    <?php if(function_exists('wp_pagenavi')) : wp_pagenavi(); else: ?>
        <div class="alignleft"><?php next_posts_link( __( '&larr; Previous Entries', 'cc' ) ) ?></div>
        <div class="alignright"><?php previous_posts_link( __( 'Next Entries &rarr;', 'cc' ) ) ?></div>
    <?php endif; ?>

    </div>

	</ul>

<?php else : ?>

	<div id="message" class="info">
		<p><?php _e( 'There were no posts found.', 'buddypress' ); ?></p>
	</div>

<?php endif; ?>
