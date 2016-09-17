<?php
global $buddyforms, $bp, $the_lp_query, $current_user, $form_slug;

$current_user = wp_get_current_user(); ?>

<div id="buddyforms-table-view" class="buddyforms_posts_table">

	<?php if ( $the_lp_query->have_posts() ) : ?>

		<table class="table table-striped">
			<thead>
			<tr>
				<th class="created">
					<span><?php _e( 'Created', 'buddyforms' ); ?></span>
				</th>
				<th class="title">
					<span><?php _e( 'Title', 'buddyforms' ); ?></span>
				</th>
				<th class="status">
					<span><?php _e( 'Status', 'buddyforms' ); ?></span>
				</th>
				<?php if ( is_user_logged_in() ) { ?>
					<th class="actions">
						<span><?php _e( 'Actions', 'buddyforms' ); ?></span>
					</th>
				<?php } ?>
			</tr>
			</thead>
			<tbody>

			<?php while ( $the_lp_query->have_posts() ) : $the_lp_query->the_post();

				$the_permalink      = get_permalink();
				$post_status        = get_post_status();

				$post_status_css    = bf_get_post_status_css_class( $post_status, $form_slug );
				$post_status_name   = bf_get_post_status_readable( $post_status );

				do_action( 'bp_before_blog_post' ) ?>

				<tr id="bf_post_li_<?php the_ID() ?>" class="<?php echo $post_status_css; ?>">
					<td>
						<span class="mobile-th"><?php _e( 'Created', 'buddyforms' ); ?></span>
						<?php the_time( 'F j, Y' ) ?>
					</td>
					<td>
						<span class="mobile-th"><?php _e( 'Title', 'buddyforms' ); ?></span>
						<a href="<?php echo $the_permalink; ?>" rel="bookmark"
						   title="<?php _e( 'Permanent Link to', 'buddyforms' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a>
						<?php do_action( 'buddyforms_the_loop_item_last', get_the_ID() ); ?>
					</td>
					<td colspan="2" class="table-wrapper">
						<table class="table table-inner">
							<tbody>
							<tr class="<?php echo $post_status_css; ?>">
								<td>
	                                <span class="mobile-th"><?php _e( 'Status', 'buddyforms' ); ?></span>
									<div class="status-item">
										<div class="table-item-status"><?php echo $post_status_name ?></div>
									</div>
								</td>
								<?php if ( is_user_logged_in() && get_the_author_meta( 'ID' ) == get_current_user_id() && $buddyforms[$form_slug]['post_type'] != 'bf_submissions') { ?>
									<td>
										<div class="meta">
											<span class="mobile-th"><?php _e( 'Actions', 'buddyforms' ); ?></span>
											<?php bf_post_entry_actions($form_slug); ?>
										</div>
									</td>
								<?php } ?>
							</tr>
							<?php do_action( 'buddyforms_the_table_inner_tr_last', get_the_ID() ); ?>
							</tbody>
						</table>
					</td>
				</tr>
				<?php do_action( 'buddyforms_the_table_tr_last', get_the_ID() ); ?>


				<?php do_action( 'bf_after_loop_item' ) ?>

			<?php endwhile; ?>

			<div class="navigation">
				<?php if ( function_exists( 'wp_pagenavi' ) ) : wp_pagenavi();
				else: ?>
					<div
						class="alignleft"><?php next_posts_link( '&larr;' . __( ' Previous Entries', 'buddyforms' ), $the_lp_query->max_num_pages ) ?></div>
					<div
						class="alignright"><?php previous_posts_link( __( 'Next Entries ', 'buddyforms' ) . '&rarr;' ) ?></div>
				<?php endif; ?>

			</div>

			</tbody>
		</table>

	<?php else : ?>

		<div id="message" class="info">
			<p><?php _e( 'There were no posts found.', 'buddyforms' ); ?></p>
		</div>

	<?php endif; ?>

	<div class="bf_modal">
		<div style="display: none;"><?php wp_editor( '', 'editpost_content' ); ?></div>
	</div>

</div>
<?php wp_reset_query();
