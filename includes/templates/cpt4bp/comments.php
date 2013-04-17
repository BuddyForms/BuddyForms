<?php

/**
 * Mostly borrowed Boon how borrowed from BuddyPress Default
 *
 */

$num_comments = 0;
$num_trackbacks = 0;
foreach ( (array)$comments as $comment ) {
	if ( 'comment' != get_comment_type() )
		$num_trackbacks++;
	else
		$num_comments++;
}

?>

	<div id="comments">
		<h3>
			<?php printf( _e( 'Discussion (%d)', 'cpt4bp' ), $num_comments ) ?>
		</h3>
		
		<?php do_action( 'bp_before_blog_comment_list' ) ?>

		<?php if ( have_comments() ) : ?>
	
			<ol class="commentlist">
				<?php bp_docs_list_comments() ?>
			</ol><!-- .comment-list -->
	
			<?php do_action( 'bp_after_blog_comment_list' ) ?>
	
			<?php if ( get_option( 'page_comments' ) ) : ?>
				<div class="comment-navigation paged-navigation">
					<?php paginate_comments_links() ?>
				</div>
			<?php endif; ?>
		
		<?php else : ?>

			<p class="comments-closed comments-empty">
				<?php _e( 'There are no comments for this doc yet.', 'cpt4bp' ) ?>
			</p>

		<?php endif ?>

	</div><!-- #comments -->


<?php if ( comments_open()  ) : ?>
	<?php comment_form() ?>
<?php else : ?>
	<p class="comments-closed comment-posting-disabled">
		<?php _e( 'Comment posting has been disabled on this doc.', 'cpt4bp' ) ?>
	</p>
<?php endif; ?>
