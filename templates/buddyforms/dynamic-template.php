<?php

/**
 * The users submissions loop
 *
 * This template can be overridden by copying it to yourtheme/buddyforms/the-loop.php.
 */

$wp_date_format = get_option( 'date_format' );
if ( empty( $wp_date_format ) ) {
	$wp_date_format = 'M j, Y';
}


ob_start();
require BUDDYFORMS_INCLUDES_PATH . '/resources/pfbc/Style/LoopStyle.php';
$css = ob_get_clean();
$the_loop_css = buddyforms_minify_css( $css );
file_put_contents( dirname( BUDDYFORMS_INCLUDES_PATH ) . '/assets/css/bf-the-loop-css-' . $form_slug . '.css', $the_loop_css );
$the_loop_css_url = BUDDYFORMS_ASSETS . 'css/bf-the-loop-css-' . $form_slug . '.css';
wp_register_style( 'bf-the-loop-css-' . $form_slug, $the_loop_css_url );
wp_enqueue_style( 'bf-the-loop-css-' . $form_slug );
?>

	<div id="buddyforms-list-view" class="buddyforms_posts_list buddyforms-posts-container">

		<?php if ( $the_lp_query->have_posts() ) : ?>


				<?php
				while ( $the_lp_query->have_posts() ) :
					$the_lp_query->the_post();

					$form_slug = apply_filters( 'buddyforms_loop_form_slug', $form_slug, get_the_ID() );

					$bf_date_time_format = apply_filters( 'buddyforms_the_loop_date_format', $wp_date_format, $form_slug );

					$the_permalink = get_permalink();
					if ( isset( $buddyforms[ $form_slug ]['post_type'] ) && $buddyforms[ $form_slug ]['post_type'] == 'bf_submissions' ) {
						$the_permalink = '#';
					}

					$the_permalink = apply_filters( 'buddyforms_post_link_on_the_loop', $the_permalink, get_the_ID(), $form_slug );

					$post_status      = get_post_status();
					$post_status_css  = buddyforms_get_post_status_css_class( $post_status, $form_slug );
					$post_status_name = buddyforms_get_post_status_readable( $post_status );

					$template_content = get_the_content( null, false, 7 );

					$template_content = apply_filters( 'the_content', $template_content );
					$template_content = str_replace( ']]>', ']]&gt;', $template_content );

				  $template_content = buddyforms_get_field_value_from_string( $template_content, $post->ID, $form_slug, true );
					echo $template_content;
					?>


					<?php do_action( 'buddyforms_after_loop_item', get_the_ID(), $form_slug ); ?>

				<?php endwhile; ?>


			<div class="navigation">
				<?php if ( function_exists( 'wp_pagenavi' ) ) : ?>
					<?php wp_pagenavi(); ?>

					<?php
				else :
					$next_posts_link    = get_next_posts_link( '&larr;' . __( 'Previous Entries', 'buddyforms' ), $the_lp_query->max_num_pages );
					$previos_posts_link = get_previous_posts_link( __( 'Next Entries', 'buddyforms' ) . '&rarr;' );
					?>
					<div class="alignright"><?php echo wp_kses( apply_filters( 'buddyforms_previos_posts_link', $previos_posts_link, $form_slug ), buddyforms_wp_kses_allowed_atts() ); ?></div>
					<div class="alignleft"><?php echo wp_kses( apply_filters( 'buddyforms_next_posts_link', $next_posts_link, $form_slug ), buddyforms_wp_kses_allowed_atts() ); ?></div>
				<?php endif; ?>

			</div>

		<?php else : ?>

			<div id="message" class="info">
				<p><?php echo wp_kses( $empty_post_message, buddyforms_wp_kses_allowed_atts() ); ?></p>
			</div>

		<?php endif; ?>

		<div class="bf_modal">
			<div style="display: none;"><?php wp_editor( '', 'buddyforms_form_content' ); ?></div>
		</div>

	</div>

<?php
wp_reset_query();
