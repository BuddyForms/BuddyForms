<?php get_header(); ?>
<?php 
global $post, $cpt4bp;

$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ); ?>
	
	<div id="content">
		<div class="padder">
		
		<?php do_action( 'bp_before_taxonomy' ) ?>

		<div class="page" id="blog-taxonomy">
		    <?php if($term){ ?>
		        <h2 class="pagetitle"><?php printf( __( 'Category: %s', 'cpt4bp' ), '<span>' . $term->name . '</span>' ); ?></h2>
		   <?php } else { ?>
		          <h2 class="pagetitle"><span><?php echo $cpt4bp->bp_post_types[$post->post_type][name] ?></span></h2>
          <?php } ?>
			
			<?php cpt4bp_locate_template('wp/cpt4bp-loop.php'); ?>
						
			<?php wp_reset_query(); ?> 
		</div>

		<?php do_action( 'bp_after_taxonomy' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php cpt4bp_locate_template('wp/cpt4bp-sidebar.php'); ?>

<?php remove_sidebar_right() ?>		
<?php get_footer(); ?>