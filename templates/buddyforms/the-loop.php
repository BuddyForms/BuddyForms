<?php
global $buddyforms, $bp, $the_lp_query, $current_user, $form_slug;

$current_user = wp_get_current_user();

?>

<div id="buddyforms-list-view" class="buddyforms_posts_list">

	<?php if ( $the_lp_query->have_posts() ) : ?>

		<ul class="buddyforms-list" role="main">

			<?php while ( $the_lp_query->have_posts() ) : $the_lp_query->the_post();

				$the_permalink      = get_permalink();
				$post_status        = get_post_status();

				$post_status_css    = bf_get_post_status_css_class( $post_status, $form_slug );
				$post_status_name   = bf_get_post_status_readable( $post_status );

				do_action( 'bp_before_blog_post' );

				?>

				<li id="bf_post_li_<?php the_ID() ?>" class="bf-submission <?php echo $post_status_css; ?>">
					<div class="item">
						<div class="item-avatar">
							<?php
							$post_thumbnail = get_the_post_thumbnail( get_the_ID(), array(
								70,
								70
							), array( 'class' => "avatar" ) );
							$post_thumbnail = apply_filters( 'buddyforms_loop_thumbnail', $post_thumbnail );
							?>
							<a href="<?php echo $the_permalink; ?>"><?php echo $post_thumbnail ?></a>
						</div>

						<div class="item-title"><a href="<?php echo $the_permalink; ?>" rel="bookmark"
						                           title="<?php _e( 'Permanent Link to', 'buddyforms' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a>
						</div>

						<div class="item-desc"><?php echo get_the_excerpt(); ?></div>

						<?php do_action( 'buddyforms_the_loop_item_last', get_the_ID() ); ?>

					</div>

					<?php
					if ( is_user_logged_in() && get_the_author_meta( 'ID' ) == get_current_user_id() && $buddyforms[$form_slug]['post_type'] != 'buddyforms_submissions' ) {
						ob_start();
						?>
						<div class="action">
							<span><?php _e( 'Created', 'buddyforms' ); ?> <?php the_time( 'F j, Y' ) ?></span>
							<div class="meta">
								<div class="item-status"><?php echo $post_status_name; ?></div>
								<?php bf_post_entry_actions( $form_slug ); ?>
							</div>
						</div>
						<?php
						$meta_tmp = ob_get_clean();
						echo apply_filters( 'buddyforms_the_loop_meta_html', $meta_tmp );
					}
					?>
					<?php do_action('buddyforms_the_loop_li_last', get_the_ID()); ?>
					<div class="clear"></div>
				</li>

				<?php do_action( 'bf_after_loop_item' ) ?>


			<?php endwhile; ?>

		</ul>

		<div class="navigation">
			<?php if ( function_exists( 'wp_pagenavi' ) ) : wp_pagenavi();
			else: ?>
				<div
					class="alignleft"><?php next_posts_link( '&larr;' . __( 'Previous Entries', 'buddyforms' ), $the_lp_query->max_num_pages ) ?></div>
				<div
					class="alignright"><?php previous_posts_link( __( 'Next Entries ', 'buddyforms' ) . '&rarr;' ) ?></div>
			<?php endif; ?>

		</div>

	<?php else : ?>

		<div id="message" class="info">
			<p><?php _e( 'There were no posts found. Create your first post now!', 'buddyforms' ); ?></p>
		</div>

	<?php endif; ?>

	<div class="bf_modal">
		<div style="display: none;"><?php wp_editor( '', 'editpost_content' ); ?></div>
	</div>

</div>
<?php wp_reset_query();