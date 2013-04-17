<?php get_header() ?>

	<div id="content">

		<div class="padder">

		<?php do_action( 'bp_before_blog_page' ) ?>

		<div class="page" id="blog-page">

			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

				<h2 class="pagetitle"><?php the_title(); ?></h2>

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="entry">
						<?php the_content( __( '<p>Read the rest of this page &rarr;</p>', 'cpt4bp' ) ); ?>
						<?php wp_link_pages( array( 'before' => __( '<p><strong>Pages:</strong> ', 'buddypress' ), 'after' => '</p>', 'next_or_number' => 'number')); ?>
					</div>
				</div>

			<?php endwhile; endif; ?>

		</div><!-- .page -->

		<?php 	

		query_posts(array('post_type' =>  get_post_meta(get_the_ID(), 'post_type', true)));	
		cpt4bp_locate_template('wp/cpt4bp-loop.php');

		?>		
		
		<div class="clear"></div>
		<?php do_action( 'bp_after_blog_page' ) ?>
		<?php edit_post_link( __( 'Edit this entry.', 'cpt4bp' ), '<p>', '</p>'); ?>
		<?php comments_template(); ?>
		</div><!-- .padder -->
	</div><!-- #content -->

		<?php cpt4bp_locate_template('wp/cpt4bp-sidebar.php'); ?>
<?php get_footer(); ?>