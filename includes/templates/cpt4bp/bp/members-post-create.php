<?php get_header() ?>

    <div id="content">
        <div class="padder">

            <?php do_action( 'bp_before_postsonprofile_content' ) ?>

            <div id="item-header">
                <?php locate_template( array( 'members/single/member-header.php' ), true ) ?>
            </div>

            <div id="item-nav">
                <div class="item-list-tabs no-ajax" id="object-nav">
                    <ul>
                        <?php bp_get_displayed_user_nav() ?>
                    </ul>
                </div>
            </div>
            <div class="item-list-tabs no-ajax" id="subnav" role="navigation">
                <ul>
        
                    <?php bp_get_options_nav(); ?>
        
                </ul>
            </div><!-- .item-list-tabs -->

            <div id="item-body">
            
          	<?php do_shortcode('[create_group_type_form]'); ?>
           
            </div><!-- #item-body -->

            <?php do_action( 'bp_after_postsonprofile_content' ) ?>
            

        </div><!-- .padder -->
    </div><!-- #content -->
<?php get_footer() ?>