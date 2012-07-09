<?php if ( have_posts() ) : ?>

	<div class="navigation">

		<div class="alignleft"><?php next_posts_link( __( '&larr; Previous Entries', 'cgt' ) ) ?></div>
		<div class="alignright"><?php previous_posts_link( __( 'Next Entries &rarr;', 'cgt' ) ) ?></div>

	</div>

	<div class="first-post-border"></div>
	<?php while (have_posts()) : the_post(); ?>

		<?php do_action( 'bp_before_blog_post' ) ?>
			
		<div class="listposts posts-img-left-content-right">
			<a href="<?php echo get_permalink() ?>" title="<?php the_title_attribute(Array('echo'=> 1)) ?>" class="clickable_box"><?php echo get_the_post_thumbnail() ?>
				<h3><?php the_title() ?></h3>
				<p><?php the_excerpt() ?></p>
			
				<div class="clear"></div>
			</a>
		</div>

		<?php do_action( 'bp_after_blog_post' ) ?>

	<?php endwhile; ?>
	<div class="last-post-border"></div>

	<div class="navigation">

		<div class="alignleft"><?php next_posts_link( __( '&larr; Previous Entries', 'cgt' ) ) ?></div>
		<div class="alignright"><?php previous_posts_link( __( 'Next Entries &rarr;', 'cgt' ) ) ?></div>

	</div>

<?php else : ?>

	<h2 class="center"><?php _e( 'Nothing found', 'cgt' ) ?></h2>
	
<?php endif; ?>