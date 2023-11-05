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
<div class="buddyforms-list-posts-filter">
	<h3>Filter</h3>
	Let us create some fuking great ajax filters that are just BOOM
</div>
	<div id="buddyforms-list-view" class="buddyforms_posts_list buddyforms-posts-container">

		<?php if ( $the_lp_query->have_posts() ) : ?>

			<ul class="buddyforms-list buddyforms-posts-content" role="main">
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

					// Wie komme ich an dfas template

					// Wie kann ich das template als loop nutzen wen es gutenberg ist

					//echo get_the_content();

					$template_content = get_the_content( null, false, 13 );
					$template_content = apply_filters( 'the_content', $template_content );
					$template_content = str_replace( ']]>', ']]&gt;', $template_content );

					echo $template_content;

//			if ( has_blocks( $template_content = get_the_content('','',13) ) ) {
//
//				$template_content = apply_filters( 'the_content', $template_content );
//				$template_content = str_replace( ']]>', ']]&gt;', $template_content );
//
//				echo $template_content;

//				$blocks = parse_blocks( $template_content );
//				//print'<pre>';print_r($blocks);print'</pre>';
//				foreach( $blocks as $block ) {
//
//
//					echo apply_filters( 'the_content', render_block( $block ) );

//				}
//			}
//			the_title();
//			the_content();
			echo '--<br>';
					?>
<!--					<li id="bf_post_li_--><?php //the_ID(); ?><!--" class="bf-submission --><?php //echo esc_attr( $post_status_css ); ?><!-- bf_posts_--><?php //the_ID(); ?><!--">-->
<!---->
<!--						--><?php
//						// Create the modal for the submissions single view
//						if ( isset( $buddyforms[ $form_slug ]['post_type'] ) && $buddyforms[ $form_slug ]['post_type'] == 'bf_submissions' ) {
//							?>
<!--							<div style="display:none;" id="bf-submission-modal_--><?php //the_ID(); ?><!--">-->
<!--								--><?php //buddyforms_locate_template( 'submission-single', $form_slug ); ?>
<!--							</div>-->
<!--						--><?php //} ?>
<!---->
<!--						<div class="item">-->
<!--							<div class="item-thumb">-->
<!--								--><?php
//								$post_thumbnail = get_the_post_thumbnail(
//									get_the_ID(),
//									array(
//										150,
//										150,
//									),
//									array( 'class' => 'thumb' )
//								);
//								$post_thumbnail = apply_filters( 'buddyforms_loop_thumbnail', $post_thumbnail );
//
//								$the_title = get_the_title();
//
//								if ( $the_title == 'none' ) {
//									$the_subject = get_post_meta( get_the_id(), 'subject', true );
//
//									if ( $the_subject ) {
//										$the_title = $the_subject;
//									}
//								}
//								?>
<!---->
<!--								<a class="" data-id="--><?php //the_ID(); ?><!--"-->
<!--								   href="--><?php //echo esc_attr( $the_permalink ); ?><!--">--><?php //echo wp_kses( $post_thumbnail, buddyforms_wp_kses_allowed_atts() ); ?><!--</a>-->
<!--							</div>-->
<!---->
<!--							<div class="item-title">-->
<!--								<a class="--><?php //echo ( isset( $buddyforms[ $form_slug ]['post_type'] ) && $buddyforms[ $form_slug ]['post_type'] == 'bf_submissions' ) ? 'bf-submission-modal' : ''; ?><!-- "-->
<!--								   data-id="--><?php //the_ID(); ?><!--"-->
<!--								   href="--><?php //echo esc_attr( $the_permalink ); ?><!--"-->
<!--								   rel="bookmark"-->
<!--								   title="--><?php //esc_html_e( 'Permanent Link to', 'buddyforms' ); ?><!-- --><?php //the_title_attribute(); ?><!--">--><?php //echo wp_kses( $the_title, buddyforms_wp_kses_allowed_atts() ); ?>
<!--								</a>-->
<!--								--><?php //do_action( 'buddyforms_the_loop_item_title_after', get_the_ID() ); ?>
<!--							</div>-->
<!---->
<!--							<div class="item-desc">--><?php //echo wp_kses( wp_trim_words( buddyforms_get_the_excerpt(), 9, '...' ), buddyforms_wp_kses_allowed_atts() ); ?><!--</div>-->
<!---->
<!--							--><?php //do_action( 'buddyforms_the_loop_item_excerpt_after', get_the_ID() ); ?>
<!---->
<!--						</div>-->
<!---->
<!--						--><?php
//						$the_author_id = apply_filters( 'buddyforms_the_loop_item_author_id', get_the_author_meta( 'ID' ), $form_slug, get_the_ID() );
//						ob_start();
//						?>
<!---->
<!--						<div class="action">-->
<!--							<div class="meta">-->
<!--								<div class="item-status">--><?php //echo wp_kses( $post_status_name, buddyforms_wp_kses_allowed_atts() ); ?><!--</div>-->
<!--								--><?php //buddyforms_post_entry_actions( $form_slug ); ?>
<!--								--><?php //do_action( 'buddyforms_the_loop_after_actions', get_the_ID(), $form_slug ); ?>
<!--								<div class="publish-date">--><?php //esc_html_e( 'Created ', 'buddyforms' ); ?><!----><?php //the_time( $bf_date_time_format ); ?><!--</div>-->
<!--							</div>-->
<!--						</div>-->
<!---->
<!--						--><?php //echo wp_kses( apply_filters( 'buddyforms_the_loop_meta_html', ob_get_clean() ), buddyforms_wp_kses_allowed_atts() ); ?>
<!---->
<!--						--><?php //do_action( 'buddyforms_the_loop_li_last', get_the_ID(), $form_slug ); ?>
<!---->
<!--						<div class="clear"></div>-->
<!---->
<!--					</li>-->

					<?php do_action( 'buddyforms_after_loop_item', get_the_ID(), $form_slug ); ?>

				<?php endwhile; ?>

			</ul>

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
