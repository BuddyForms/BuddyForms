<?php get_header(); ?>
<?php $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ); ?>
	
	<div id="content">
		<div class="padder">
		
		<?php do_action( 'bp_before_taxonomy' ) ?>

		<div class="page" id="blog-taxonomy">
			<h2 class="pagetitle"><?php printf( __( 'Group Category: %s', 'cgt' ), '<span>' . $term->name . '</span>' ); ?></h2>

			<?php cgt_locate_template('wp/cgt-loop.php'); ?>
						
			<?php wp_reset_query(); ?> 
		</div>

		<?php do_action( 'bp_after_taxonomy' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php cgt_locate_template('wp/cgt-sidebar.php'); ?>

		
<?php get_footer(); ?>