<?php
/**
 * Delete a product post
 * 
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta	
 */
function delete_product_post( $group_id ){    
    $groups_post_id = groups_get_groupmeta( $group_id, 'group_post_id' );
    
    wp_delete_post( $groups_post_id );
}
add_action( 'groups_before_delete_group', 'delete_product_post' );

/**
 * Locate a template
 * 
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta	
 */
function cgt_locate_template( $file ) {
	if( locate_template( array( $file ), false ) ) {
		locate_template( array( $file ), true );
	} else {
		include( BP_CGT_TEMPLATE_PATH .$file );
	}
}

function cgt_group_extension_link(){
	global $bp;
	echo bp_group_permalink().$bp->current_action.'/';
}

/**
 * Clean the input by type
 * 
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta	
 */
function app_clean_input( $input, $type ) {
	global $allowedposttags;
	
    $cleanInput = false;
    
    switch( $type ) {
		case 'text':
			$cleanInput = wp_filter_nohtml_kses( $input );
	        break;
			
        case 'checkbox':
            $input === '1'? $cleanInput = '1' : $cleanInput = '';
        	break;
			
		case 'html':
            $cleanInput = wp_kses( $input, $allowedposttags );
        	break;
			
    	default:
        	$cleanInput = false;
        	break;
    }
	
    return $cleanInput;
}

/**
 * Adds a form shortcode
 * 
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta	
 */
function create_group_type_form( $atts = array(), $content = null ) {
    global $cc_page_options, $post, $bp, $current_user, $cgt;
   
    get_currentuserinfo();	
    
    if( $bp->current_component  == 'groups' ) {
       	$groups_post_id = groups_get_groupmeta( bp_get_group_id(), 'group_post_id' ); 
        
       	$posttype = groups_get_groupmeta( bp_get_group_id(), 'group_type' ); 
       
       	$the_post = get_post( $groups_post_id );
		$post_id = $the_post->ID;
	   
		extract( shortcode_atts( array(
    		'posttype' => $the_post->post_type,
    		'taxonomy' => $the_post->post_type .'_category',
    	), $atts ) );   
    } else {
		$post_id = 0;
       	$the_post = new stdClass;
		$the_post->ID = $post_id;
		$the_post->post_type = $bp->current_component;
		$the_post->post_title = '';
		
        extract( shortcode_atts( array(
            'posttype' => $bp->current_component,
            'taxonomy' => $bp->current_component .'_category',
        ), $atts ) );       
    }
     
   	if( empty( $posttype ) )
   	   $posttype = $the_post->post_type;
	
	$customfields = $cgt->custom_field_slug[$posttype];
		
	foreach( (array) $customfields as $key => $value ) {
		if( ! $value ) {
			unset($customfields[$key]);
		}
	}

	if( ! function_exists( 'wp_editor' ) ) {
    	require_once ABSPATH . '/wp-admin/includes/post.php' ;
    	wp_tiny_mce();
    }
	
	//If the form is submitted
	if( isset( $_POST['submitted'] ) ) {
		$hasError = false;
			   
		//Check to make sure that the post title field is not empty
		$title = trim( $_POST['editpost_title'] );
		
		if( empty( $title ) ) {
			$titleError = __('Please enter a title','cgt');
			$hasError = true;
        }
 
        //Check to make sure that content is submitted
        $content = trim( $_POST['editpost_content'] );
		
        if( empty( $content ) )  {
            $contentError = __('Please enter a content','cgt');
            $hasError = true;
        }
		
		if( ! $the_post->ID && $_FILES['async-upload']['error'] != 0 ) {
		 	$fileError = 'Please select an image';
          	$hasError = true;
	    }
		
		foreach( (array) $customfields as $key => $customfield ) : 
            if( $cgt->custom_field_required[$posttype][$key] == 'on' && empty( $_POST[$customfield] ) ){
                $custom_field_Error .= 'Please enter '. $customfield .' value. <br />';
                $hasError = true;
            }
        endforeach;
		
	    //If there is no error, send the form
		if(! $hasError ) {
			$tags = $_POST['editpost_tags'];
			$permalink = get_permalink( $_POST['editpost_id'] );
            
            if( isset( $_POST['new_post_id'] ) && ! empty( $_POST['new_post_id'] ) ) {                     
				$my_post = array(
                    'ID'        	=> $_POST['new_post_id'],
                    'post_title' 	=> $_POST['editpost_title'],
                    'post_content' 	=> $_POST['editpost_content'],
                    'tags_input' 	=> $_POST['editpost_tags'],
                    'post_category' => array( (int)$_POST['editpost_cat'] ),
                    'post_type' 	=> $posttype,
                    'post_status' 	=> 'publish'
				);
                    
                // insert the new form
                $post_id = wp_update_post( $my_post );
                
               foreach( (array) $customfields as $key => $customfield ) : 
                    if( $cgt->custom_field_type[$posttype][$key] == 'Taxonomy' ){                           
                       $custom_field_taxonomy = $cgt->custom_field_taxonomy[$posttype][$key];
                       
                       wp_set_post_terms( $post_id, $_POST[$customfield], $custom_field_taxonomy, false );
                        
                        if( substr( $custom_field_taxonomy, 0, 3 ) == 'pa_') {                           
                            if( $cgt->custom_field_display[$posttype][$key] == 'on' )
                                $custom_field_display = 1;
                            
                            $product_attributes 		= get_post_meta($post_id, '_product_attributes', false);
                            $product_attributes_count 	= count( $product_attributes[0] );
                            $product_attributes_count++;
							
                            $product_attributes[0][$custom_field_taxonomy] = array(
                                'name' 			=> $custom_field_taxonomy,
                                'value' 		=> '',
                                'position' 		=> $product_attributes_count,
                                'is_visible' 	=> $custom_field_display,
                                'is_taxonomy' 	=> 1
                            );
                            
                            update_post_meta( $post_id, '_product_attributes', $product_attributes[0] );                            
                        }                 
                    }

                    if( $cgt->custom_field_type[$posttype][$key] == 'AttachGroupType' ){
                       	$custom_field_attach_group = $cgt->custom_field_attach_group[$posttype][$key];
						
                        wp_set_post_terms( $post_id, $_POST[$posttype.'_attached_'.$custom_field_attach_group], $posttype.'_attached_'.$custom_field_attach_group, false);
                        
                        update_post_meta( $post_id, '_'.$posttype.'_attached', $_POST[$posttype.'_attached_'.$custom_field_attach_group] );
                        update_post_meta( $post_id, '_'.$posttype.'_attached_tax_name', $posttype.'_attached_'.$custom_field_attach_group );
                   }

                   update_post_meta($post_id, $customfield, $_POST[$customfield] );                    
                endforeach;
            } else {                    
                $my_post = array(
                    'post_author' 	=> $current_user->ID,
                    'post_title' 	=> $_POST['editpost_title'],
                    'post_content' 	=> $_POST['editpost_content'],
                    'post_type' 	=> $posttype,
                    'post_status' 	=> $cgt->new_group_types[$posttype]['status']
                );   
                    
                // insert the new form
                $post_id = wp_insert_post($my_post);
                
               	foreach( (array) $customfields as $key => $customfield ) : 
                    if( $cgt->custom_field_type[$posttype][$key] == 'Taxonomy' ){                           
                       	$custom_field_taxonomy = $cgt->custom_field_taxonomy[$posttype][$key];
                        wp_set_post_terms( $post_id, $_POST[$customfield], $custom_field_taxonomy, false );
                        
                        if( substr( $custom_field_taxonomy, 0, 3 ) == 'pa_' ){                           
                            if($cgt->custom_field_display[$posttype][$key] == 'on')
                                $custom_field_display = 1;
                            
                            $product_attributes 	  = get_post_meta( $post_id, '_product_attributes', false );
                            $product_attributes_count = count( $product_attributes[0] );
                            $product_attributes_count++;
                            $product_attributes[0][$custom_field_taxonomy] = array(
                                'name' 			=> $custom_field_taxonomy,
                                'value' 		=> '',
                                'position' 		=> $product_attributes_count,
                                'is_visible' 	=> $custom_field_display,
                                'is_taxonomy'	=> 1
                            );
                            
                            update_post_meta( $post_id, '_product_attributes', $product_attributes[0] );                            
                        }                 
                    } 

                    if( $cgt->custom_field_type[$posttype][$key] == 'AttachGroupType' ){
                       	$custom_field_attach_group = $cgt->custom_field_attach_group[$posttype][$key];
                        
                        wp_set_post_terms( $post_id, $_POST[$posttype.'_attached_'.$custom_field_attach_group], $posttype.'_attached_'.$custom_field_attach_group, false);
                        
                        update_post_meta($post_id, '_'.$posttype.'_attached', $_POST[$posttype.'_attached_'.$custom_field_attach_group] );
                        update_post_meta($post_id, '_'.$posttype.'_attached_tax_name', $posttype.'_attached_'.$custom_field_attach_group );
                   }

                   update_post_meta($post_id, $customfield, $_POST[$customfield] );                        
                endforeach;
            }       
        
        	// Do the wp_insert_post action to insert it
            do_action( 'wp_insert_post', 'wp_insert_post' );
			
			if( ! empty( $_FILES ) ) {  
		        require_once(ABSPATH . 'wp-admin/includes/admin.php');  
		        $id = media_handle_upload('async-upload', $post_id ); //post id of Client Files page  
		
		        unset( $_FILES );  
		        if( is_wp_error( $id ) ) {  
		            $errors['upload_error'] = $id;  
		            $id = false;  
		        } 
				
		        set_post_thumbnail($post_id, $id);
		      
               	if( ! $the_post->ID){
	               	if( $errors ) {  
			            $fileError 	= '<p>There has bean an error uploading the image.</p>';  
			            $hasError 	= true;
			        }  
               	}
    		}        
    	} 

    	if( empty( $hasError ) ) {
    		?>
			<div class="thanks">
				<?php if($_POST['editpost_id']){ ?>
		    		<h1><?php _e('Saved', 'cgt')?></h1>
		    	    <p><?php _e('Post has been created.','cgt'); ?> </p>
	   			<?php } else { ?>
		            <h1><?php _e('Saved', 'cgt')?></h1>
		            <p><?php _e('Post has been updated.','cgt'); ?> </p>
				<?php } ?>
			</div>
			<?php
		} 	
	}

	?>
	<div class="hinzufuegen">
	    <?php if(isset($custom_field_Error) && $custom_field_Error != ''){ ?>
	        <div class="error"><?php echo $custom_field_Error;?></div>
	    <?php } ?>
	
		<?php if(isset($titleError) && $titleError != '') { ?>
			<div class="error"><?php echo $titleError;?></div>
		<?php } ?>
		
		<?php if(isset($contentError) && $contentError != '') { ?>
			<div class="error"><?php echo $contentError; ?></div>
		<?php } ?>
		
		<?php if(isset($fileError) && $fileError != '') { ?>
			<div class="error"><?php echo $fileError; ?></div>
		<?php } ?>

		<?php if ( !is_user_logged_in() ) : ?>
			<form name="login-form" id="sidebar-login-form" class="standard-form" action="<?php echo site_url( 'wp-login.php', 'login_post' ) ?>" method="post">
				
				<div style="float:left; margin-right:10px;">
					<label><?php _e( 'Username', 'cgt' ) ?><br />
					<input type="text" style="width:200px;" name="log" id="sidebar-user-login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" tabindex="97" /></label>
				</div>
				<div style="float:left;margin-right:10px;">
					<label><?php _e( 'Password', 'cgt' ) ?><br />
					<input type="password" style="width:200px;" name="pwd" id="sidebar-user-pass" class="input" value="" tabindex="98" /></label>
				</div>
				<label><input name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" tabindex="99" /> <?php _e( 'Remember Me', 'cgt' ) ?></label>	 
				<input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e('Log In', 'cgt'); ?>" tabindex="100" />
				<input type="hidden" name="cgtcookie" value="1" />
				<input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
			</form>	 
		<?php else :
			if( isset( $_POST['editpost_title'])) {
				if(function_exists('stripslashes')) {
					$editpost_title = stripslashes($_POST['editpost_title']);
				} else {
					$editpost_title = $_POST['editpost_title'];
				}
			} else {
				$editpost_title =  $the_post->post_title;
			}			

			if( isset( $_POST['editpost_content'] ) ){
				$editpost_content_val = $_POST['editpost_content'];
			} else {
				$editpost_content_val = $the_post->post_title;
			}
			?>	
			<div class="gform_wrapper">
			<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data" id="editpost" method="POST">

			    <?php wp_nonce_field('client-file-upload'); ?>  
			    <input type="hidden" name="new_post_id" id="new_post_id" value="<?php echo $post_id ?>" />  
			    <input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />  

				<ol class="forms">
					<li>
						<div class="label"><label for="editpost_title"><?php _e('Name','cgt'); ?>:</label></div>
						<?php echo tk_textfield( array('id' => 'editpost_title','name' => 'editpost_title', 'value' => $editpost_title ) ) ?>
					</li>					
					<li>
						<div id="doc-content-textarea">
							<label id="content-label" for="doc[content]"><?php _e( 'Content', 'bp-docs' ) ?></label>        
							<div id="editor-toolbar">
								<?php
								/* No media support for now
								<div id="media-toolbar">
								    <?php  echo bpsp_media_buttons(); ?>
								</div>
								*/
								if( function_exists( 'wp_editor' ) ) {
									wp_editor($editpost_content_val, 'editpost_content', array(
										'media_buttons' => false,
										'dfw'		=> false
									) );
								}
								?>
							</div>
				        </div>
				    </li>
					<?php
					if( $customfields ){
						foreach( $customfields as $key => $customfield ) :
							if( isset( $_POST[$customfield] ) ) {
			                    if( function_exists( 'stripslashes' ) ) {
			                       $customfield_val = $_POST[ $customfield ];
			                    } else {
			                       $customfield_val = $_POST[ $customfield ];
			                    }
			                    
			                    if( $customfield_val == 'on' ){
			                        $checked = true;
			                    } else {
			                      $checked = false;  
			                    }
			                    
			                } else {
			                    $customfield_val = get_post_meta($the_post->ID, $customfield, true);
			                }
							
			                if( $cgt->custom_field_name[$posttype][$key] ){
			                    $field_name = $cgt->custom_field_name[$posttype][$key];
			                } else {
			                    $field_name = $cgt->custom_field_slug[$posttype][$key];
			                }
							
							$custom_field_type = isset( $cgt->custom_field_type[$posttype][$key] ) ? $cgt->custom_field_type[$posttype][$key] : '';
							
			               	switch( $custom_field_type ) {
								case 'AttachGroupType':
									?>
			                        <li>
			                        	<div class="label"><label for="<?php echo $field_name ?>"><?php _e($field_name, 'cgt');?>:</label></div>
			                           	<p><?php echo $cgt->custom_field_discription[$posttype][$key] ?></p>
			                              
			                            <?php
			                            $customfield_val = get_post_meta($post_id, '_'.$posttype.'_attached', false);
			                            $args = array(
				                            'hide_empty'         => 0,
				                            'id'                 => $posttype.'_attached_'.$cgt->custom_field_attach_group[$posttype][$key],
				                            'child_of'           => 0,
				                            'echo'               => FALSE,
				                            'selected'           => $customfield_val[0],
				                            'hierarchical'       => TRUE, 
				                            'name'               => $posttype.'_attached_'.$cgt->custom_field_attach_group[$posttype][$key],
				                            'class'              => 'postform',
				                            'depth'              => 0,
				                            'tab_index'          => 0,
				                            'taxonomy'           => $posttype.'_attached_'.$cgt->custom_field_attach_group[$posttype][$key],
				                            'hide_if_empty'      => FALSE 
			                            );
			                        
			                          	echo wp_dropdown_categories( $args );
			                            ?> 
									</li>
			                        <?php
			                        break;
									
			                    case 'Mail':
		                    		?>
		                            <li>
		                            	<div class="label"><label for="<?php echo $field_name ?>"><?php _e($field_name, 'cgt');?>:</label></div>
		                            	<p><?php echo $cgt->custom_field_discription[$posttype][$key] ?></p>
		                             	<?php echo tk_textfield(Array('id' => $customfield,'name' => $customfield, 'value' => $customfield_val)); ?>
		                            </li>
		                        	<?php
		                        	break;
										
			                    case 'Radiobutton':
		                    		?>
		                            <li>
		                            	<div class="label"><label for="<?php echo $field_name ?>"><?php _e($field_name, 'cgt');?>:</label></div>
		                            	<p><?php echo $cgt->custom_field_discription[$posttype][$key] ?></p>
		                             	<?php echo tk_radiobutton(Array('id' => $customfield,'name' => $customfield, 'value' => $customfield_val, 'checked' => $checked)); ?>
		                            </li>
		                        	<?php
		                        	break;
										
			                    case 'Checkbox':
		                    		?>
		                            <li>
		                            	<div class="label"><label for="<?php echo $field_name ?>"><?php _e($field_name, 'cgt');?>:</label></div>
		                            	<p><?php echo $cgt->custom_field_discription[$posttype][$key] ?></p>
		                             	<?php echo tk_checkbox(Array('id' => $customfield,'name' => $customfield, 'value' => $customfield_val, 'checked' => $checked)); ?>
		                            </li>
		                        	<?php
		                        	break;
										
			                    case 'Dropdown':
			                    	?>
			                        <li>
			                        	<div class="label"><label for="<?php echo $field_name ?>"><?php _e($field_name, 'cgt');?>:</label></div>
			                            <p><?php echo $cgt->custom_field_discription[$posttype][$key] ?></p>
			                                
			                             <?php
			                             if($cgt->custom_field_m_select[$posttype][$key] == 'on'){
			                                 $custom_field_m_select = TRUE;
			                             } else {
			                                $custom_field_m_select = FALSE; 
			                             }
			                          
			                            $new_field_type = new tk_form_select( array('value' => $customfield_val, 'multiselect' => $custom_field_m_select, 'name' => $customfield, 'id' => $customfield, 'elements' => $elements));
			                             
			                            $custom_field_select = explode(',', $cgt->custom_field_select[$posttype][$key]);
			                            foreach( $custom_field_select as $key => $value) {
			                            	$new_field_type->add_option($value);
			                                $elements[$key] = array(  'value'=> $value, 'option_name' => $value );
			                            }
			                             
			                            //echo tk_select(Array('multiselect' => $custom_field_m_select, 'id' => $customfield,'name' => $customfield, 'value' => $customfield_val, 'elements' => $elements)); 
			                           	echo $new_field_type->get_html();
			                           	?>
									</li>
			                        <?php
			                        break;
										
			                    case 'Textarea':
			                    	?>
			                        <li>
			                        	<div class="label"><label for="<?php echo $field_name ?>"><?php _e($field_name, 'cgt');?>:</label></div>
			                            <p><?php echo $cgt->custom_field_discription[$posttype][$key] ?></p>
			                            <?php echo tk_textarea(Array('id' => $customfield,'name' => $customfield, 'value' => $customfield_val)); ?>
			                        </li>
			                        <?php
			                        break;
									
			                    case 'Hidden':
			                    	?>
			                        <li style="display: none">  
			                        	<?php echo tk_textfield(Array('id' => $customfield,'name' => $customfield, 'value' => $cgt->custom_field_hidden_val[$posttype][$key])); ?>
			                        </li>
			                        <?php
			                        break;
									
			                    case 'Text':
			                    	?>
			                        <li>
			                        	<div class="label"><label for="<?php echo $field_name ?>"><?php _e($field_name, 'cgt');?>:</label></div>
			                            <p><?php echo $cgt->custom_field_discription[$posttype][$key] ?></p>
			                            <?php echo tk_textfield(Array('id' => $customfield,'name' => $customfield, 'value' => $customfield_val)); ?>
			                        </li>
			                        <?php
			                        break;
									
			                    case 'Link':
			                    	?>
			                        <li>
			                        	<div class="label"><label for="<?php echo $field_name ?>"><?php _e($field_name, 'cgt');?>:</label></div>
			                            <p><?php echo $cgt->custom_field_discription[$posttype][$key] ?></p>
			                            <?php echo tk_textfield(Array('id' => $customfield,'name' => $customfield, 'value' => $customfield_val)); ?>
			                        </li>
			                        <?php
			                        break;
									
			                    case 'Taxonomy':
			                    	?>
			                        <li>
			                        	<div class="label"><label for="<?php echo $field_name ?>"><?php _e($field_name, 'cgt');?>:</label></div>
			                            <p><?php echo $cgt->custom_field_discription[$posttype][$key] ?></p>
			                                
			                            <?php
			                            if($cgt->custom_field_m_select[$posttype][$key] == 'on'){
			                                $customfield_name = $customfield . '[]';
			                            } else {
			                                $customfield_name = $customfield;
			                            }
			                            
			                            $args = array(
				                            'hide_empty'         => 0,
				                            'id'                 => $customfield,
				                            'child_of'           => 0,
				                            'echo'               => FALSE,
				                            'selected'           => $customfield_val,
				                            'hierarchical'       => TRUE, 
				                            'name'               => $customfield_name,
				                            'class'              => 'postform',
				                            'depth'              => 0,
				                            'tab_index'          => 0,
				                            'taxonomy'           => $cgt->custom_field_taxonomy[$posttype][$key],
				                            'hide_if_empty'      => FALSE 
			                            );
			                        
			                            //echo wp_dropdown_categories( $args );
			                            $select_cats = wp_dropdown_categories( $args  );
			                            
			                            if($cgt->custom_field_m_select[$posttype][$key] == 'on'){
			                                 $select_cats = str_replace( 'id=', 'multiple="multiple" id=', $select_cats );
			                            } 
			                            echo $select_cats;
			                            
			                             // $categories=  get_categories($args); 
			                             // if($cgt->custom_field_m_select[$posttype][$key] == 'on'){
			                                 // $custom_field_m_select = TRUE;
			                             // } else {
			                                // $custom_field_m_select = FALSE; 
			                             // }
			                              // $new_field_type = new tk_form_select( array('value' => $customfield_val, 'multiselect' => $custom_field_m_select, 'name' => $customfield_name, 'id' => $customfield_name));
			                              // foreach ($categories as $category) {
			                                // $new_field_type->add_option($category->cat_name);
			                              // }
			                              // echo $new_field_type->get_html();
			                            ?>
			                        </li>			 
			                        <?php
			                        break;
									
			                    default:									
									break;
							}							
						endforeach;
					}
					?>					
					<li id="upload-img">  
						<div class="label"><label for="upload-img">Neues Featured Image hochladen</label></div>  
						<input type="file" id="async-upload" name="async-upload"> 
					</li>
					
					<li class="buttons">
						<input type="hidden" name="submitted" id="submitted" value="true" class="requiredField" />
						<button type="submit" id="submitted" class="button"><?php _e('Submit','cgt'); ?></button>
					</li>
				</ol>
			</form>
		</div>
	</div>		
	<?php 
	endif;
}
add_shortcode('create_group_type_form', 'create_group_type_form');