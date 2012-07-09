<?php
### widget for the Apps Categories navigation
function filter_checked( $cat_name ){
 	if($_GET['filter']){
		$filters = $_GET['filter'];

		foreach($filters as $filter){
			$check = checked( $filter, $cat_name, false );
		}
	}
	return $check;
}

function widget_apps_nav() { ?>

<script type="text/javascript">
jQuery.noConflict();jQuery(document).ready(function(){
	jQuery("#accordion_filter div.swap1").hide();
		jQuery("#accordion_filter h3").click(function(){
			jQuery(this).next("div.swap1").slideToggle("slow").siblings("div.swap1:visible").slideUp("slow");
			jQuery(this).toggleClass("active");
			jQuery(this).siblings("h3").removeClass("active");
		});
}); </script>



<div id="categories-apps" class="widget widget_categories">

	<h3 class="widgettitle">App Kategorien</h3>
		
		<?php
		$orderby = 'name';
		$show_count = 0; // 1 for yes, 0 for no
		$pad_counts = 0; // 1 for yes, 0 for no
		$hierarchical = 1; // 1 for yes, 0 for no
		$taxonomy = 'product_cat';
		$title = '';
		$hide_empty = 0;
		$show_count = 1;
		
		$args = array(
			'orderby' => $orderby,
			'show_count' => $show_count,
			'pad_counts' => $pad_counts,
			'hierarchical' => $hierarchical,
			'taxonomy' => $taxonomy,
			'title_li' => $title,
			'hide_empty' => $hide_empty,
			'show_count' => $show_count
		);
		?>
		<ul>
		<?php
		wp_list_categories($args);
		?>
		</ul>
</div> 

<div id="categories-apps-filter" class="widget widget_categories">

		<h3 class="widgettitle">Suche verfeinern</h3>
	<?php 
	
	$args = array(
		'type'			=> 'post',
		'hierarchical'	=> true,
		'taxonomy'		=> 'product_filter'		
	
	);
	$categories=get_categories($args);
 
	?>

	<?php 
	
	
	echo '<form class="taxonomy-drilldown-dropdowns" action="" method="get">';
	echo '<div id="accordion_filter" class="accordion" style="">';
	foreach($categories as $category) {
		if($category->category_parent == 0){
			echo ' <h3> ';
			//echo ' <input type="checkbox" name="filter[]" '. filter_checked($category->cat_name) .' value="'. $category->cat_name .'" />';
			//echo ' <span>'. $category->cat_name.' ('.  $category->count.' )</span>';
			echo ' <span><b>'. $category->cat_name.'</b></span>';
			echo ' </h3> ';
			
			$args = array(
				'type'			=> 'post',
				'child_of' => $category->cat_ID,
				'hide_empty' => false,
				'hierarchical'	=> true,
				'taxonomy'		=> 'product_filter'		
			);
			
			$sub_cats=get_categories($args);
			echo '<div class="swap1" style="display: none;"><ul>';
			foreach($sub_cats as $sub_cat) {
				if($sub_cat->category_parent != 0){
					echo ' <li> ';
					echo ' <input type="checkbox" name="filter[]" '. filter_checked($sub_cat->cat_name) .' value="'. $sub_cat->cat_name .'" />';
					echo ' <span>'. $sub_cat->cat_name.' ('.  $sub_cat->count.' )</span>';
					echo ' </li> '; 
				}		
			}
			echo '</div></ul>';
			echo ' <div id="filter_border" class="border"></div>';
			
		}
	}//end foreach
	echo '</div>';
	echo '<input type="submit" value="Submit"></form>';
	?>
	
</div>	
 
<?php } ?>
<?php
if ( function_exists('widget_apps_nav') )
    wp_register_sidebar_widget( 'widget_apps_nav', 'Apps Kategorien', 'widget_apps_nav', '' );


### widget for the Company Categories navigation

function widget_firmen_nav() { ?>
<div id="categories-apps" class="widget widget_categories">
	<h3 class="widgettitle">Firmen Kategorien</h3>
		
		<?php
		$orderby = 'name';
		$show_count = 0; // 1 for yes, 0 for no
		$pad_counts = 0; // 1 for yes, 0 for no
		$hierarchical = 1; // 1 for yes, 0 for no
		$taxonomy = 'firmen_category';
		$title = '';
		$hide_empty = 1;
		
		$args = array(
			'orderby' => $orderby,
			'show_count' => $show_count,
			'pad_counts' => $pad_counts,
			'hierarchical' => $hierarchical,
			'taxonomy' => $taxonomy,
			'title_li' => $title,
			'hide_empty' => $hide_empty
		);
		?>
		<ul>
		<?php
		wp_list_categories($args);
		?>
		</ul>
</div>  
<?php } ?>
<?php
if ( function_exists('widget_firmen_nav') )
    wp_register_sidebar_widget( 'widget_firmen_nav', 'Firmen Kategorien', 'widget_firmen_nav', '' );

    
### widget for the Company Categories navigation

function groups_header_product_widget() {?>
<?php global $post, $group_type, $wc_query; ?>

<?php $groups_post_id = groups_get_groupmeta( bp_get_group_id(), 'group_post_id' ); ?>
<?php $group_type = groups_get_groupmeta( bp_get_group_id(), 'group_type' ); ?>

<?php $wc_query = new WP_Query( array('post_type' => $group_type, 'p' => $groups_post_id ) ); ?>

<?php
if ( $wc_query->have_posts() ) while ( $wc_query->have_posts() ) : $wc_query->the_post();
	switch ($group_type) {
		case 'product': ?>
			<?php do_action('woocommerce_before_single_product'); ?>
	
			<div itemscope itemtype="http://schema.org/Product" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>
			
			<?php do_action('woocommerce_group_header_before_single_product_summary'); ?>
			
			<div class="summary">
				
				<?php do_action( 'woocommerce_single_product_summary'); ?>
			
			</div>
			
			<?php do_action('woocommerce_group_header_after_single_product_summary'); ?>
	
		</div> 
		<?php
		break;	
		default:
           echo get_the_post_thumbnail(get_the_ID(), array (222, 160)); ?>
            <div class="summary">
            	<h2 class="pagetitle"><a href="<?php the_permalink() ?>" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
	            <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	                <div class="entry">
	                    <?php echo get_the_excerpt(); ?>
	                </div>
	            </div>
            </div>
            <?php
		break;
	}
endwhile
?>


<?php } ?>
<?php if ( function_exists('groups_header_product_widget') )
    wp_register_sidebar_widget( 'groups_header_product_widget', 'groups header product widget', 'groups_header_product_widget', '' );
     

 function cc_appknight_groups_widget() {
    	global $post, $group_type, $wp_query, $cgt;
    
    	$this_post_id = $post->ID;
        
     	if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group();
    		$group_type = groups_get_groupmeta( bp_get_group_id(), 'group_type' );
            
    		$attached_group_id = get_post_meta($this_post_id, '_'.$group_type.'_attached', true);
            $attached_tax_name = get_post_meta($this_post_id, '_'.$group_type.'_attached_tax_name', true);
            $term =  get_term_by('id', $attached_group_id, $attached_tax_name);
            $group_slug = bp_get_group_slug();
        endwhile; endif; 
             
        if ( bp_has_groups( 'slug=' . $term->slug) ) : while ( bp_groups() ) : bp_the_group();
        $firma_post_type_post_id = groups_get_groupmeta( bp_get_group_id(), 'group_post_id' );
            
            if ( $firma_post_type_post_id != '' && $group_type == 'product' && bp_group_is_visible()  ) { 

                // The Query
                query_posts( array( 'post_type' => 'firmen', 'p' => $firma_post_type_post_id) );
        

                $get_the_post_thumbnail_attr = array(
                    'class' => "avatar",
                );
        
                if(have_posts()){
                    $tmp .= '<div id="item-list" class="widget widget_from_firma"><!-- Begin Widget Firma -->'; 
                    $tmp .= '<div><ul>';
                    $tmp .= '<h3 class="widgettitle">Anbieter</h3>';
            
                    // The Loop
                    while ( have_posts() ) : the_post();
                        if ( $this_post_id != $post->ID ) {
                            $tmp .= '<a href="'.get_permalink().'" title="'.the_title_attribute(Array('echo'=> 0)).'" class="clickable_box">';
                            $tmp .= '<li>';
                            $tmp .= get_the_post_thumbnail($post->ID , 'post-thumbnails' , $get_the_post_thumbnail_attr);
                            $tmp .= '<h3 class="firma_name">'.get_the_title().'</h3>';
                            $tmp .= '<div class="firma_slogan">'.get_post_meta(get_the_ID(), 'Slogen', true).'</div>';
                            $tmp .= '</li>';
                            $tmp .= '</a>';
                            $tmp .= '<div class="clear"></div>';
                        }
                    endwhile;
                
                    $tmp .= '</ul></div>';
                    $tmp .= '</div><!-- End Widget Ansprechpartner -->';
                    
                    $tmp .= '<div class="clear"></div>';
                    
                    echo $tmp;
                    
                    // Reset $tmp and the Query
                    $tmp = '';
                    wp_reset_query();
                }
            
            }
       endwhile; endif;
       
       if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group();
      
		if ( bp_group_is_visible() ) : ?>
			
		<div id="item-list" class="widget widget_ansprechpartner"><!-- Begin Widget Ansprechpartner -->
			<div>
				<h3 style="" class="widgettitle">Ansprechpartner</h3>
				
				<?php bp_group_list_admins() ?>
				<?php if ( bp_group_has_moderators() ) : ?>
					<?php bp_group_list_mods() ?>
				<?php endif; ?>
			</div>
		</div><!-- End Widget Ansprechpartner -->
		<div class="clear"></div>
		<?php endif; ?>
		<?php 
		
		if($group_type != 'firmen') {
    		$args = array(
                'post_type'=> 'product',
                $attached_tax_name => $term->slug,
                'order'    => 'ASC',
            );    
		} else {
		  $args = array(
                'post_type'=> 'product',
                'product_attached_firmen' => $group_slug,
                'order'    => 'ASC',
            );    
        }
		
     	// The Query 
		query_posts( $args );

		if(have_posts()){
		    
		    if ( $group_type == 'product' ) {
                $h3_widget_title = '<h3 class="widgettitle">Andere Apps des Anbieters</h3>';
            } 
    
            if ( $group_type == 'firmen' ) { 
                $h3_widget_title .= '<h3 class="widgettitle">Apps des Anbieters</h3>';
            }
            
			 
			$tmp .= '<div class="widget widget_apps_from_company"><!-- Begin Widget Apps des Anbieters / Andere Apps -->';
			$tmp .= '<div><ul>';
			
			$tmp .= $h3_widget_title;
			
			// The Loop
			while ( have_posts() ) : the_post();
				if ( $this_post_id != $post->ID ) {
					$tmp .= '<a href="'.get_permalink().'" title="'.the_title_attribute(Array('echo'=> 0)).'" class="clickable_box">';
					$tmp .= '<li>';
					$tmp .= get_the_post_thumbnail($post->ID ,array(50, 50));
					$tmp .= '<h4>'.get_the_title().'</h4>';
					// $tmp .= '<div class="app_slogan">Hier kommt ein kurzer Slogan des Apps hin. </div>';
					$tmp .= '</li>';
					$tmp .= '</a>';
					$tmp .= '<div class="clear"></div>';
				}
			endwhile;
		
			$tmp .= '</ul></div></div>';
			
			$tmp .= '<div class="clear"></div>';
			
			echo $tmp;
			
			// Reset $tmp and the Query
			$tmp = '';
			wp_reset_query();
		}
        
	endwhile; endif;
	
	do_action( 'bp_after_group_header' );
	do_action( 'template_notices' );
	
}

if ( function_exists('cc_appknight_groups_widget') )
    wp_register_sidebar_widget( 'cc_appknight_groups_widget', 'AppKnight Groups Widget', 'cc_appknight_groups_widget', '' );
?>