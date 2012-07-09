<?php get_header(); ?>
<?php 
global $post, $cgt;

$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ); ?>
	
	<div id="content">
		<div class="padder">
		
		<?php do_action( 'bp_before_taxonomy' ) ?>

		<div class="page" id="blog-taxonomy">
		    <?php if($term){ ?>
		        <h2 class="pagetitle"><?php printf( __( 'Category: %s', 'cgt' ), '<span>' . $term->name . '</span>' ); ?></h2>
		   <?php } else { ?>
		          <h2 class="pagetitle"><span><?php echo $cgt->new_group_types[$post->post_type][name] ?></span></h2>
          <?php } ?>
			
			<?php cgt_locate_template('wp/cgt-loop.php'); ?>
						
			<?php wp_reset_query(); ?> 
		</div>

		<?php do_action( 'bp_after_taxonomy' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php cgt_locate_template('wp/cgt-sidebar.php'); ?>

<?php remove_sidebar_right() ?>		
<?php get_footer(); ?>