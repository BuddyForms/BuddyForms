<?php
/**
 * Adds a form shortcode
 * 
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta	
 */
function create_group_type_form( $atts = array(), $content = null ) {
    global $cc_page_options, $post, $bp, $current_user, $cpt4bp;

    get_currentuserinfo();	
  	
    if( $bp->current_component  == 'groups' ) {
       	$groups_post_id	= groups_get_groupmeta( bp_get_group_id(), 'group_post_id' ); 
        $posttype		= groups_get_groupmeta( bp_get_group_id(), 'group_type' ); 
		$the_post		= get_post( $groups_post_id );
		$post_id		= $the_post->ID;
	   
		extract( shortcode_atts( array(
    		'posttype' => $the_post->post_type,
    		'taxonomy' => $the_post->post_type .'_category',
    	), $atts ) );   

    } elseif($_GET[post_id]) { 
    			
    	$groups_post_id	= $_GET[post_id]; 
        $posttype		= $bp->current_component;
       	$the_post		= get_post( $groups_post_id );
		$post_id		= $the_post->ID;
	   
		if ($the_post->post_author != $current_user->ID){
			echo '<div class="error alert">You are not allowed to edit this Post what are you doing here?</div>';
			return;	
		}
		
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
	
	$customfields = $cpt4bp['bp_post_types'][$posttype]['form_fields'];
		
	// foreach( $customfields as $key => $value ) {
		// if( ! $value ) {
			// unset($customfields[$key]);
		// }
	// }

	// if( ! function_exists( 'wp_editor' ) ) {
    	// require_once ABSPATH . '/wp-admin/includes/post.php' ;
    	// wp_tiny_mce();
    // }
	
	//If the form is submitted
	if( isset( $_POST['submitted'] ) ) {
		$hasError = false;
			   
		//Check to make sure that the post title field is not empty
		$title = trim( $_POST['editpost_title'] );
		
		if( empty( $title ) ) {
			$titleError = __('Please enter a title','cpt4bp');
			$hasError = true;
        }
 
        //Check to make sure that content is submitted
        $content = trim( $_POST['editpost_content'] );
		
        if( empty( $content ) )  {
            $contentError = __('Please enter a content','cpt4bp');
            $hasError = true;
        }
		
		if( ! $the_post->ID && $_FILES['async-upload']['error'] != 0 ) {
		 	$fileError = 'Please select an image';
          	$hasError = true;
	    }
		
		// foreach( $customfields as $key => $customfield ) : 
            // if( $cpt4bp->custom_field_required[$posttype][$key] == 'on' && empty( $_POST[$customfield] ) ){
                // $custom_field_Error .= 'Please enter '. $customfield .' value. <br />';
                // $hasError = true;
            // }
        // endforeach;
		
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
                
               foreach( $customfields as $key => $customfield ) : 
				   
				    if( $customfield['type'] == 'Taxonomy' ){
				       do_action('cpt4bp_before_wp_set_post_terms',$customfield);
                       wp_set_post_terms( $post_id, $_POST[ sanitize_title( $customfield['name'] ) ], $customfield['taxonomy'], false );
                       do_action('cpt4bp_after_wp_set_post_terms',$customfield);
                    }

                    if( $customfield['type'] == 'AttachGroupType' ){
                       	$custom_field_attach_group = $cpt4bp->custom_field_attach_group[$posttype][$key];
						
                        wp_set_post_terms( $post_id, $_POST[$posttype.'_attached_'.$custom_field_attach_group], $posttype.'_attached_'.$custom_field_attach_group, false);
                        
                        update_post_meta( $post_id, '_'.$posttype.'_attached', $_POST[$posttype.'_attached_'.$custom_field_attach_group] );
                        update_post_meta( $post_id, '_'.$posttype.'_attached_tax_name', $posttype.'_attached_'.$custom_field_attach_group );
                   }
                    update_post_meta($post_id, sanitize_title($customfield['name']), $_POST[sanitize_title($customfield['name'])] );                    
                endforeach;
            } else {                    
                $my_post = array(
                    'post_author' 	=> $current_user->ID,
                    'post_title' 	=> $_POST['editpost_title'],
                    'post_content' 	=> $_POST['editpost_content'],
                    'post_type' 	=> $posttype,
                    'post_status' 	=> $cpt4bp['bp_post_types'][$posttype]['status']
                );   
                    
                // insert the new form
                $post_id = wp_insert_post($my_post);
                
               foreach( $customfields as $key => $customfield ) : 
				   
				    if( $customfield['type'] == 'Taxonomy' ){                   
                       do_action('cpt4bp_before_wp_set_post_terms',$customfield);
                       wp_set_post_terms( $post_id, $_POST[ sanitize_title( $customfield['name'] ) ], $customfield['taxonomy'], false );
                       do_action('cpt4bp_after_wp_set_post_terms',$customfield);
                    } 

                    if( $cpt4bp->custom_field_type[$posttype][$key] == 'AttachGroupType' ){
                       	$custom_field_attach_group = $cpt4bp->custom_field_attach_group[$posttype][$key];
                        
                        wp_set_post_terms( $post_id, $_POST[$posttype.'_attached_'.$custom_field_attach_group], $posttype.'_attached_'.$custom_field_attach_group, false);
                        
                        update_post_meta($post_id, '_'.$posttype.'_attached', $_POST[$posttype.'_attached_'.$custom_field_attach_group] );
                        update_post_meta($post_id, '_'.$posttype.'_attached_tax_name', $posttype.'_attached_'.$custom_field_attach_group );
                   }
                   update_post_meta($post_id, sanitize_title($customfield['name']), $_POST[sanitize_title($customfield['name'])] );                    
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
		    		<h1><?php _e('Saved', 'cpt4bp')?></h1>
		    	    <p><?php _e('Post has been created.','cpt4bp'); ?> </p>
	   			<?php } else { ?>
		            <h1><?php _e('Saved', 'cpt4bp')?></h1>
		            <p><?php _e('Post has been updated.','cpt4bp'); ?> </p>
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
					<label><?php _e( 'Username', 'cpt4bp' ) ?><br />
					<input type="text" style="width:200px;" name="log" id="sidebar-user-login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" tabindex="97" /></label>
				</div>
				<div style="float:left;margin-right:10px;">
					<label><?php _e( 'Password', 'cpt4bp' ) ?><br />
					<input type="password" style="width:200px;" name="pwd" id="sidebar-user-pass" class="input" value="" tabindex="98" /></label>
				</div>
				<label><input name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" tabindex="99" /> <?php _e( 'Remember Me', 'cpt4bp' ) ?></label>	 
				<input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e('Log In', 'cpt4bp'); ?>" tabindex="100" />
				<input type="hidden" name="cpt4bpcookie" value="1" />
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
				$editpost_content_val = $the_post->post_content;
			}
			?>	
			<div class="gform_wrapper">
				
			<?php 
				// Form starts
	
 	    
			$form = new Form("editpost");
			$form->configure(array(
				"prevent" => array("bootstrap", "jQuery", "focus"),
				"action" => $_SERVER['REQUEST_URI'],
				"view" => new View_Vertical
			));

			wp_enqueue_style('bootstrapcss', plugins_url('PFBC/Resources/bootstrap/css/bootstrap.min.css', __FILE__));


			$form->addElement(new Element_HTML(wp_nonce_field('client-file-upload','_wpnonce',true,false)));
			$form->addElement(new Element_Hidden("new_post_id", $post_id, array('value' => $post_id, 'id' => "new_post_id")));
			$form->addElement(new Element_Hidden("redirect_to",  $_SERVER['REQUEST_URI']));
			
			$form->addElement(new Element_HTML('<div class="label"><label>Title</label></div>'));					
			$form->addElement(new Element_Textbox("Title:", "editpost_title",array('lable' => 'enter a title', "required" => 1, 'value' => $editpost_title)));
			
			$form->addElement(new Element_HTML('<div class="label"><label>Content</label></div>'));					
			$form->addElement(new Element_TinyMCE("Content:", "editpost_content", array('lable' => 'enter some content', "required" => 1, 'value' => $editpost_content_val, 'id' => "editpost_content")));

			// echo '<pre>';
			// print_r($customfields);
			// echo '<pre>';
			if( $customfields ){
				foreach( $customfields as $key => $customfield ) :
					if( isset( $_POST[sanitize_title($customfield['name'])] ) ) {
			            if( function_exists( 'stripslashes' ) ) {
			               $customfield_val = $_POST[ sanitize_title($customfield['name']) ];
			            } else {
			               $customfield_val = $_POST[ sanitize_title($customfield['name']) ];
			            }
			            
			            if( $customfield_val == 'on' ){
			                $checked = true;
			            } else {
			              $checked = false;  
			            }
			            
			        } else {
			            $customfield_val = get_post_meta($the_post->ID, sanitize_title($customfield['name']), true);
			        }
					
			       	
			       	switch( $customfield['type'] ) {
						case 'AttachGroupType':
							if($cpt4bp->custom_field_required[$posttype][$key] == 'on')
								$required[$posttype][$key] = '<span class="required">* </span>';
							
							$form->addElement(new Element_HTML('<div class="label"><label for="' . $field_name . '">' . __($field_name, 'cpt4bp') . ':</label></div><label for="' . $field_name . '">' . $required[$posttype][$key] . $cpt4bp->custom_field_discription[$posttype][$key] . '</label>'));
							?>
			            
			                	
			                      
			                    <?php
			                    $customfield_val = get_post_meta($post_id, '_'.$posttype.'_attached', false);
			                    $args = array(
			                        'hide_empty'         => 0,
			                        'id'                 => $posttype.'_attached_'.$cpt4bp->custom_field_attach_group[$posttype][$key],
			                        'child_of'           => 0,
			                        'echo'               => FALSE,
			                        'selected'           => $customfield_val[0],
			                        'hierarchical'       => TRUE, 
			                        'name'               => $posttype.'_attached_'.$cpt4bp->custom_field_attach_group[$posttype][$key],
			                        'class'              => 'postform',
			                        'depth'              => 0,
			                        'tab_index'          => 0,
			                        'taxonomy'           => $posttype.'_attached_'.$cpt4bp->custom_field_attach_group[$posttype][$key],
			                        'hide_if_empty'      => FALSE 
			                    );
			                
			                $form->addElement(new Element_HTML(wp_dropdown_categories( $args )));
			

			                    ?> 
						
			                <?php
			                break;
							
			            case 'Mail':
							if($cpt4bp->custom_field_required[$posttype][$key] == 'on')
								$required[$posttype][$key] = 1;
							
							$form->addElement(new Element_Email($field_name.':', $customfield, array("required" => $required[$posttype][$key], 'label' => $cpt4bp->custom_field_discription[$posttype][$key], 'id' => $customfield, 'value' => $customfield_val)));
			        	break;
						
						case 'Radiobutton':
							break;
								
			            case 'Checkbox':
							break;
								
			            case 'Dropdown':
							break;
								
			            case 'Textarea':
							 break;
							
			            case 'Hidden':
							break;
						
						case 'Text':
							 $form->addElement(new Element_Textbox($customfield['name'].':',  sanitize_title($customfield['name']), array('value' => $customfield_val)));
							 break;
							
			            case 'Link':
							break;
							
			            case 'Taxonomy':
							
							//Returns All Term Items for "my_term"
							$term_list = wp_get_post_terms($post_id, $customfield['taxonomy'], array("fields" => "ids"));

							$args = array(
							    'multiple' => $customfield['multiple'],
							    'selected_cats' => $term_list,
							    'hide_empty' => 0,
							    'id' => $key,
							    'child_of' => 0,
							    'echo' => FALSE,
							    'selected' => false,
							    'hierarchical' => 1,
							    'name' => sanitize_title($customfield['name']).'[]',
							    'class' => 'postform',
							    'depth' => 0,
							    'tab_index' => 0,
							    'taxonomy' => $customfield['taxonomy'],
							    'hide_if_empty' => FALSE,
							);
							
							
							$dropdown = wp_dropdown_categories( $args );
							
							if ( is_array($customfield['multiple']) ) {
							    $dropdown = str_replace( 'id=', 'multiple="multiple" id=', $dropdown );
							}
							if ( is_array( $term_list ) ) {
							    foreach ( $term_list as $value ) {
								$dropdown = str_replace( ' value="' . $value . '"', ' value="' . $value . '" selected="selected"', $dropdown );
							    }
							}
						
							$form->addElement(new Element_HTML($dropdown));
						break;

				}							
			endforeach;
		}

		if(! $the_post->ID)
			$required[$posttype][$key] = 1;
							
		$form->addElement(new Element_File("File:", "async-upload", array("required" => $required[$posttype][$key], 'id' => "async-upload")));
	
		// $form->addElement(new PFBC\Element\HTML('<li id="upload-img">  
			    // <div class="label"><label for="upload-img">Neues Featured Image hochladen</label></div>  
			    // <input type="file" id="async-upload" name="async-upload"></li> '));

		$form->addElement(new Element_Hidden("submitted", 'true', array('value' => 'true', 'id' => "submitted")));
		$form->addElement(new Element_Button('submitted','submit',array('id' => 'submitted', 'name' => 'submitted')));
		$form->render();
		?>
		</div>
	</div>		
	<?php 
	endif;
}
add_shortcode('create_group_type_form', 'create_group_type_form');