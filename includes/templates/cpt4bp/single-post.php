<?php
 global $post, $group_type, $wc_query, $bp, $cpt4bp, $product;
$groups_post_id = groups_get_groupmeta( bp_get_group_id(), 'group_post_id' );
$group_type = groups_get_groupmeta( bp_get_group_id(), 'group_type' );

$content_post = get_post($groups_post_id);

?>
<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
              <div class="entry">
              	<?php echo $content_post->post_content; ?>
              	</div>      

            <div class="entry">
                
                <?php 
				if(!empty($cpt4bp['bp_post_types'][$group_type]['form_fields'])){ ?>
                    <table class="shop_attributes">
                     <tbody>
                        <?php foreach($cpt4bp['bp_post_types'][$group_type]['form_fields'] as $key => $customfield) : 
						    $customfield_value = get_post_meta($groups_post_id, sanitize_title($customfield['name']) , true);
                            if( $customfield_value != '') :
                                    echo '<tr>';
                                    echo '<th>' . $customfield['name'] . '</th>';
                                    echo "<td><p><a href='".$customfield_value."' " . $customfield['name']. ">". $customfield_value ." </a></p></td>";
                                    echo '</tr>';
                                endif;
                            ?>
                        <?php endforeach ?>
                    </tbody></table>
                <?php } ?>   
            
            </div>
        </div>
