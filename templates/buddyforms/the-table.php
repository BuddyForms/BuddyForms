<?php
global $buddyforms, $bp, $the_lp_query, $current_user, $form_slug;
	get_currentuserinfo(); ?>

    <div class="buddyforms_posts_table"

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
            <th class="actions">
              <span><?php _e( 'Actions', 'buddyforms' ); ?></span>
            </th>
          </tr>
         </thead>

			<?php while ( $the_lp_query->have_posts() ) : $the_lp_query->the_post();

				$the_permalink = get_permalink();
				$post_status = get_post_status();

				$post_status_css =  $post_status_name  = $post_status;

				if( $post_status == 'pending')
					$post_status_css = 'bf-pending';

				if( $post_status == 'publish')
					$post_status_name = __('Published', 'buddyforms');

				if( $post_status == 'draft')
					$post_status_name = __('Draft', 'buddyforms');

				if( $post_status == 'pending')
					$post_status_name = __('Pending Review', 'buddyforms');

				if( $post_status == 'future')
					$post_status_name = __('Scheduled', 'buddyforms');

				$post_status_css = apply_filters('bf_post_status_css',$post_status_css,$form_slug);

				do_action( 'bp_before_blog_post' ) ?>

        <tbody>
          <tr id="bf_post_li_<?php the_ID() ?>" class="<?php echo $post_status_css; ?>">
            <td>
              <span class="mobile-th"><?php _e( 'Created', 'buddyforms' ); ?></span>
              <?php the_time('F j, Y') ?>
            </td>
            <td>
              <span class="mobile-th"><?php _e( 'Title', 'buddyforms' ); ?></span>
              <a href="<?php echo $the_permalink; ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddyforms' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a>
              <?php do_action('buddyforms_the_loop_item_last', get_the_ID()); ?>
            </td>
            <td>
              <span class="mobile-th"><?php _e( 'Status', 'buddyforms' ); ?></span>
              <div class="table-item-status"><?php echo $post_status_name ?></div>
              <div class="item-status-action">published 29/04/2016</div>
            </td>
            <td>
            <div class="meta">
              <span class="mobile-th"><?php _e( 'Actions', 'buddyforms' ); ?></span>
              <ul class="edit_links">
                <?php
    						if (get_the_author_meta('ID') ==  get_current_user_id()){
    							$permalink = get_permalink( $buddyforms[$form_slug]['attached_page'] );
    							$permalink = apply_filters('buddyforms_the_loop_edit_permalink', $permalink, $buddyforms[$form_slug]['attached_page']);

                  ob_start();
                    if( current_user_can('buddyforms_'.$form_slug.'_edit') ) {
                      ?><li><?php
                      if(isset($buddyforms[$form_slug]['edit_link']) && $buddyforms[$form_slug]['edit_link'] != 'none') {
    										echo apply_filters( 'bf_loop_edit_post_link','<a title="Edit" id="' . get_the_ID() . '" class="bf_edit_post" href="' . $permalink . 'edit/' . $form_slug. '/' .get_the_ID() . '">' . __( 'Edit', 'buddyforms' ) .'</a>', get_the_ID());
    									 } else {
    										echo apply_filters( 'bf_loop_edit_post_link', bf_edit_post_link('Edit'), get_the_ID() );
    									 }
                       ?></li><?php
                    }

    								if( current_user_can('buddyforms_'.$form_slug.'_delete') ) {
    									echo ' <li> <a title="Delete"  id="' . get_the_ID() . '" class="bf_delete_post" href="#">' . __( 'Delete', 'buddyforms' ) . '</a></li>';
    								 }
    								do_action('buddyforms_the_loop_actions', get_the_ID());
    							$meta_tmp = ob_get_clean();

                  echo apply_filters('buddyforms_the_loop_meta_html', $meta_tmp);
    						} ?>
              </ul>
            </div>
            </td>
          </tr>
          <?php //do_action('buddyforms_the_loop_li_last', get_the_ID()); ?>
        </tbody>

				<?php do_action( 'bf_after_loop_item' ) ?>

			<?php endwhile; ?>

			<div class="navigation">
			<?php if(function_exists('wp_pagenavi')) : wp_pagenavi(); else: ?>
				<div class="alignleft"><?php next_posts_link( '&larr;' . __( ' Previous Entries', 'buddyforms' ), $the_lp_query->max_num_pages ) ?></div>
				<div class="alignright"><?php previous_posts_link( __( 'Next Entries ', 'buddyforms' ) . '&rarr;' ) ?></div>
			<?php endif; ?>

			</div>

		</ul>
  </table>
	<?php else : ?>

		<div id="message" class="info">
			<p><?php _e( 'There were no posts found.', 'buddyforms' ); ?></p>
		</div>

	<?php endif; ?>
	<div class="bf_modal"><div style="display: none;"><?php wp_editor('','editpost_content'); ?></div></div>
</div>
<?php
wp_reset_query();
