<?php 

global $the_lp_query, $tmp, $list_post_atts;


if ( $the_lp_query->have_posts() ) : ?>

	<div class="loop-default">

    <?php while ( $the_lp_query->have_posts() ) : $the_lp_query->the_post(); ?>

        <?php do_action( 'bp_before_blog_post' ) ?>

        <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <a href='<?php echo trailingslashit( bp_loggedin_user_domain() ).get_post_type().'?post_id='.get_the_ID(); ?>'>Edit</a> - 
        Delete <a href='<?php echo trailingslashit( bp_loggedin_user_domain() ).get_post_type().'?post_id='.get_the_ID().'&delete=true'; ?>'>x</a>
            
            <div class="author-box">
                <?php echo get_avatar( get_the_author_meta( 'user_email' ), '50' ); ?>
                <?php if(defined('BP_VERSION')){ ?>
                <p><?php printf( __( 'by %s', 'cc' ), bp_core_get_userlink( $post->post_author ) ) ?></p>
                <?php } ?>
            </div>

            <div class="post-content">
                
                <h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'cc' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
                
                <p class="date"><?php the_time('F j, Y') ?> <em><?php _e( 'in', 'cc' ) ?> <?php the_category(', ') ?><?php if(defined('BP_VERSION')){  printf( __( ' by %s', 'cc' ), bp_core_get_userlink( $post->post_author ) );}?></em></p>

                <div class="entry">
                    <?php do_action('blog_post_entry')?>
                </div>
                <?php $tags = get_the_tags(); if($tags) {  ?>
                    <p class="postmetadata"><span class="tags"><?php the_tags( __( 'Tags: ', 'cc' ), ', ', '<br />'); ?></span> <span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', 'cc' ), __( '1 Comment &#187;', 'cc' ), __( '% Comments &#187;', 'cc' ) ); ?></span></p>
                <?php } else {?>
                    <p class="postmetadata"><span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', 'cc' ), __( '1 Comment &#187;', 'cc' ), __( '% Comments &#187;', 'cc' ) ); ?></span></p>
                <?php } ?>
            </div>

        </div>
        

        <?php do_action( 'bp_after_blog_post' ) ?>

    <?php endwhile; ?>

    <div class="navigation">

    <?php if(function_exists('wp_pagenavi')) : wp_pagenavi(); else: ?>
        <div class="alignleft"><?php next_posts_link( __( '&larr; Previous Entries', 'cc' ) ) ?></div>
        <div class="alignright"><?php previous_posts_link( __( 'Next Entries &rarr;', 'cc' ) ) ?></div>
    <?php endif; ?>

    </div>

	</div>

<?php else : ?>

    <h2 class="center"><?php _e( 'Not Found', 'cc' ) ?></h2>
    <p class="center"><?php _e( 'Sorry, but you are looking for something that isn\'t here.', 'cc' ) ?></p>

    <?php locate_template( array( 'searchform.php' ), true ) ?>

<?php endif; ?>
