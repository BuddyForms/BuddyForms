<?php
/**
 * Adds a form shortcode for the create and edit sreen
 * @var $args = posttype, the_post, post_id
 * 
 * @package buddyforms
 * @since 0.1-beta	
*/
function buddyforms_create_edit_form( $args = array() ) {
    global $post, $bp, $current_user, $buddyforms, $post_id;
	
	// hook for plugins to overwrite the $args.
	$args = apply_filters('buddyforms_create_edit_form_args',$args);
	
	extract(shortcode_atts(array(
		'posttype' => $bp->current_component,
		'the_post' => 0,
		'post_id' => $post_id
	), $args));
	get_currentuserinfo();	
  	
	// if post edit screen is displayed
	if($_GET[post_id]) { 
    			
    	$post_id		= $_GET[post_id]; 
        $posttype		= $bp->current_component;
		if($_GET[revision_id]) {
			$the_post		= get_post( $_GET[revision_id] );
		} else {
			$the_post		= get_post( $_GET[post_id] );
		}
       	
       	if ($the_post->post_author != $current_user->ID){
			echo '<div class="error alert">You are not allowed to edit this Post what are you doing here?</div>';
			return;	
		}
	// If post_id == 0 a new post is created 	
	} elseif($post_id == 0){
		
		$the_post = new stdClass;
		$the_post->ID 			= $post_id;
		$the_post->post_type 	= $bp->current_component;
		$the_post->post_title 	= '';
	
	}
     
   	if( empty( $posttype ) )
   	   $posttype = $the_post->post_type;
	
	$customfields = $buddyforms['bp_post_types'][$posttype]['form_fields'];
		
	// If the form is submitted we will get in action
	if( isset( $_POST['submitted'] ) ) {
			
		$comment_status = $buddyforms['bp_post_types'][$posttype]['comment_status'];
		
		if(isset($_POST['comment_status']))
				$comment_status = $_POST['comment_status'];
			
        // check if post is new or edit 
        if( isset( $_POST['new_post_id'] ) && ! empty( $_POST['new_post_id'] ) ) {
        	                     
			$my_post = array(
                'ID'        		=> $_POST['new_post_id'],
                'post_title' 		=> $_POST['editpost_title'],
                'post_content' 		=> $_POST['editpost_content'],
                'post_type' 		=> $posttype,
                'post_status' 		=> 'publish',
                'comment_status'	=> $comment_status,
			);
                
            // update the new post
            $post_id = wp_update_post( $my_post );
			
		} else {
			
			  $my_post = array(
                'post_author' 		=> $current_user->ID,
                'post_title' 		=> $_POST['editpost_title'],
                'post_content' 		=> $_POST['editpost_content'],
                'post_type' 		=> $posttype,
                'post_status' 		=> $buddyforms['bp_post_types'][$posttype]['status'],
                'comment_status'	=> $comment_status,
            );   
                
            // insert the new form
            $post_id = wp_insert_post($my_post);
			
		}
		// if the post has post meta / custom fields 
        if(isset($customfields)){
        	
			foreach( $customfields as $key => $customfield ) : 
			   
				if( $customfield['type'] == 'Taxonomy' ){
					
					// check if the custom field is a taxonomy
					// We need to check if the tax is a normal category, because categories want id's and custom taxonomies slugs... ;-()
					if($customfield['taxonomy'] == 'category'){
				
						wp_set_post_terms( $post_id, $_POST[ sanitize_title( $customfield['name'] ) ], $customfield['taxonomy'], false );
				
					} else {
				
						$slug = Array();
						$postCategories = $_POST[ sanitize_title( $customfield['name'] ) ];
						
						foreach ( $postCategories as $postCategory ) {
							$term = get_term_by('id', $postCategory, $customfield['taxonomy']);
							$slug[] = $term->slug;
						}
							
						wp_set_post_terms( $post_id, $slug, $customfield['taxonomy'], false );
				
					}
					
				}
				// update meta do action to hook into. This can be interesting if you added new Form Element and want to manipulate how they get saved.
				do_action('buddyforms_update_post_meta',$customfield,$post_id,$_POST);
               
	
			   	if(isset($customfield['slug'])){
			   		$slug = $customfield['slug'];
			   	} else {
			  		$slug = sanitize_title($customfield['name']);
			   	}
               // update the post
				update_post_meta($post_id, $slug, $_POST[$slug] ); 
				                   
            endforeach;
    	}
		// Featured image ? If yes save via media_handle_upload and set the post thumbnail
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
		
		// Display the message  
		if( empty( $hasError ) ) {
	
			?>
			<div class="thanks">
				<?php if($_GET['post_id']){ ?>
		            <h1><?php _e('Saved', 'buddyforms')?></h1>
		            <p><?php _e('Post has been updated.', 'buddyforms'); ?> </p>
	   			<?php } else { ?>
	   				<h1><?php _e('Saved', 'buddyforms')?></h1>
		    	    <p><?php _e('Post has been created.', 'buddyforms'); ?> </p>
				<?php } ?>
			</div>
			<?php
	
		}
	}     
	?>
	<div class="the_buddyforms_form">
		<style>
			.post-revisions li {
			float: left;
			padding: 5px;
			width: 100%;
			}
		
			.standard-form textarea, .standard-form input[type=text], .standard-form input[type=url],.standard-form input[type=link],.standard-form input[type=email], .standard-form input[type=password]{
				width: 75%;
				border: 1px inset #ccc;
				-moz-border-radius: 3px;
				-webkit-border-radius: 3px;
				border-radius: 3px;
				color: #888;
				font: inherit;
				font-size: 14px;
				padding: 6px;
			}
			
		/** BuddyPress Fix  for the Theme Compatibility **/
			#buddypress table tr td, #buddypress table tr th {
				padding: 0px;
				vertical-align: middle;
			}
			
		</style>
		
		<?php if ( !is_user_logged_in() ) : ?>
			
			<form name="login-form" id="sidebar-login-form" class="standard-form" action="<?php echo site_url( 'wp-login.php', 'login_post' ) ?>" method="post">
				
				<div style="float:left; margin-right:10px;">
					<label><?php _e( 'Username', 'buddyforms' ) ?><br />
					<input type="text" style="width:200px;" name="log" id="sidebar-user-login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" tabindex="97" /></label>
				</div>
				<div style="float:left;margin-right:10px;">
					<label><?php _e( 'Password', 'buddyforms' ) ?><br />
					<input type="password" style="width:200px;" name="pwd" id="sidebar-user-pass" class="input" value="" tabindex="98" /></label>
				</div>
				<label><input name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" tabindex="99" /> <?php _e( 'Remember Me', 'buddyforms' ) ?></label>	 
				<input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e('Log In', 'buddyforms'); ?>" tabindex="100" />
				<input type="hidden" name="buddyformscookie" value="1" />
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
			<div class="form_wrapper">

				<?php // Form starts
				
				$form = new Form("editpost");
				$form->configure(array("prevent" => array("bootstrap", "jQuery", "focus"), "action" => $_SERVER['REQUEST_URI'], "view" => new View_Vertical,'class' => 'standard-form'));
	
				$form->addElement(new Element_HTML(wp_nonce_field('client-file-upload', '_wpnonce', true, false)));
				$form->addElement(new Element_Hidden("new_post_id", $post_id, array('value' => $post_id, 'id' => "new_post_id")));
				$form->addElement(new Element_Hidden("redirect_to", $_SERVER['REQUEST_URI']));
				
				// this if needs to be changed to be a hook so the if can be done in the groups extension plugin
				if ($bp->current_component != 'groups') {
					
					$form->addElement(new Element_HTML('<div class="label"><label>Title</label></div>'));
					$form->addElement(new Element_Textbox("Title:", "editpost_title", array('lable' => 'enter a title', "required" => 1, 'value' => $editpost_title)));
					$form->addElement(new Element_HTML('<div class="label"><label>Content</label></div>'));

					ob_start();
						$settings = array('wpautop' => true, 'media_buttons' => true, 'wpautop' => true, 'tinymce' => true, 'quicktags' => true, 'textarea_rows' => 18);
						$post = get_post($post_id, 'OBJECT');
						wp_editor($post->post_content, 'editpost_content', $settings);
						$wp_editor = ob_get_contents();
					ob_clean();
					
					$form->addElement(new Element_HTML($wp_editor));
					
				} else {
					$post = get_post($post_id, 'OBJECT');
					$form->addElement(new Element_Hidden("editpost_title", $editpost_title));
					$form->addElement(new Element_Hidden("editpost_content", $post->post_content));
				}
				
				// if the form have custom field to save as post meta data they get displayed here 
				if ($customfields) {
					foreach ($customfields as $key => $customfield) :
						if($customfield['slug'] != ''){
							$slug = $customfield['slug'];
						} else {
							$slug = sanitize_title($customfield['name']);
						}

						if ($_POST[$slug] != '') {
							$customfield_val = $_POST[$slug];
						} else {
							$customfield_val = get_post_meta($post_id, $slug, true);
						}

						switch( $customfield['type'] ) {
							case 'Mail' :
								$element_attr = $customfield['required'] ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input') : array('value' => $customfield_val, 'class' => 'settings-input');
								$form->addElement(new Element_Email($customfield['name'] . ':<p><smal>' . $customfield['description'] . '</smal></p>', $slug, $element_attr));
								break;
	
							case 'Radiobutton' :
								$element_attr = $customfield['required'] ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input') : array('value' => $customfield_val, 'class' => 'settings-input');
								$form->addElement(new Element_Radio($customfield['name'] . ':<p><smal>' . $customfield['description'] . '</smal></p>', $slug, explode(",", $customfield['Values']), $element_attr));
								break;
	
							case 'Checkbox' :
								$element_attr = $customfield['required'] ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input') : array('value' => $customfield_val, 'class' => 'settings-input');
								$form->addElement(new Element_Checkbox($customfield['name'] . ':<p><smal>' . $customfield['description'] . '</smal></p>', $slug, explode(",", $customfield['Values']), $element_attr));
								break;
	
							case 'Dropdown' :
								$element_attr = $customfield['required'] ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input') : array('value' => $customfield_val, 'class' => 'settings-input');
								$form->addElement(new Element_Select($customfield['name'] . ':', $slug, explode(",", $customfield['Values']), $element_attr));
								break;
							
							case 'Comments' :
								$element_attr = $customfield['required'] ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input') : array('value' => $customfield_val, 'class' => 'settings-input');
								$form->addElement(new Element_Select($customfield['name'] . ':', 'comment_status', array('open','closed'), $element_attr));
								break;
	
							case 'Textarea' :
								$element_attr = $customfield['required'] ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input') : array('value' => $customfield_val, 'class' => 'settings-input');
								$form->addElement(new Element_Textarea($customfield['name'] . ':<p><smal>' . $customfield['description'] . '</smal></p>', $slug, $element_attr));
								break;
	
							case 'Hidden' :
								$form->addElement(new Element_Hidden($customfield['name'], $customfield['value']));
								break;
	
							case 'Text' :
								$element_attr = $customfield['required'] ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input') : array('value' => $customfield_val, 'class' => 'settings-input');
								$form->addElement(new Element_Textbox($customfield['name'] . ':<p><smal>' . $customfield['description'] . '</smal></p>', $slug, $element_attr));
								break;
	
							case 'Link' :
								$element_attr = $customfield['required'] ? array('required' => true, 'value' => $customfield_val, 'class' => 'settings-input') : array('value' => $customfield_val, 'class' => 'settings-input');
								$form->addElement(new Element_Url($customfield['name'] . ':<p><smal>' . $customfield['description'] . '</smal></p>', $slug, $element_attr));
	
								break;
	
							case 'Taxonomy' :
								$term_list = wp_get_post_terms($post_id, $customfield['taxonomy'], array("fields" => "ids"));
	
								$args = array('multiple' => $customfield['multiple'], 'selected_cats' => $term_list, 'hide_empty' => 0, 'id' => $key, 'child_of' => 0, 'echo' => FALSE, 'selected' => false, 'hierarchical' => 1, 'name' => $slug . '[]', 'class' => 'postform', 'depth' => 0, 'tab_index' => 0, 'taxonomy' => $customfield['taxonomy'], 'hide_if_empty' => FALSE, );
	
								$dropdown = wp_dropdown_categories($args);
	
								if (is_array($customfield['multiple'])) {
									$dropdown = str_replace('id=', 'multiple="multiple" id=', $dropdown);
								}
								if (is_array($term_list)) {
									foreach ($term_list as $value) {
										$dropdown = str_replace(' value="' . $value . '"', ' value="' . $value . '" selected="selected"', $dropdown);
									}
								}
	
								$form->addElement(new Element_HTML($customfield['taxonomy'] . ':<p><smal>' . $customfield['description'] . '</smal></p>'));
								$form->addElement(new Element_HTML($dropdown));
								break;
								
							default:
								
								// hook to add your form element
								apply_filters('buddyforms_create_edit_form_display_element',$form,$post_id,$posttype,$customfield,$customfield_val);
								
								break;
	
						}

					endforeach;
				}
	
				// Display Upload Field for Featured image if required is selected for this form
				if ($buddyforms['bp_post_types'][$posttype]['featured_image']['required'][0] == 'Required'){
					if ($post_id == 0) {
						$file_attr = array("required" => 1, 'id' => "async-upload");
					} else {
						$file_attr = array('id' => "async-upload");
					}
					$form->addElement(new Element_File('Featured Image:', 'async-upload', $file_attr));
				}
	
				$form->addElement(new Element_Hidden("submitted", 'true', array('value' => 'true', 'id' => "submitted")));
				$form->addElement(new Element_Button('submitted', 'submit', array('id' => 'submitted', 'name' => 'submitted')));
				
				// thats it render the form!
				$form->render(); ?>
			</div>
			<?php 
			if($buddyforms['bp_post_types'][$posttype]['revision']){ ?>

					<?php buddyforms_wp_list_post_revisions($post_id ); ?>

			<?php } ?>
	</div>		
	<?php endif;
}

function buddyforms_wp_list_post_revisions( $post_id = 0, $type = 'all' ) {
	if ( ! $post = get_post( $post_id ) )
		return;

	// $args array with (parent, format, right, left, type) deprecated since 3.6
	if ( is_array( $type ) ) {
		$type = ! empty( $type['type'] ) ? $type['type']  : $type;
		_deprecated_argument( __FUNCTION__, '3.6' );
	}

	if ( ! $revisions = buddyforms_wp_get_post_revisions( $post->ID ) )
		return;

	$rows = '';
	foreach ( $revisions as $revision ) {
		if ( ! current_user_can( 'read_post', $revision->ID ) )
			continue;

		$is_autosave = wp_is_post_autosave( $revision );
		if ( ( 'revision' === $type && $is_autosave ) || ( 'autosave' === $type && ! $is_autosave ) )
			continue;

		$rows .= "\t<li>" . buddyforms_wp_post_revision_title_expanded( $revision,$post_id ) . "</li>\n";
	}
	echo '<div class="revision">';
	echo '<h3>Revision</h3>';
	echo "<ul class='post-revisions'>\n";
	echo $rows;

	// if the post was previously restored from a revision
	// show the restore event details
	if ( $restored_from_meta = get_post_meta( $post->ID, '_post_restored_from', true ) ) {
		$author = get_user_by( 'id', $restored_from_meta[ 'restored_by_user' ] );
		/* translators: revision date format, see http://php.net/date */
		$datef = _x( 'j F, Y @ G:i:s', 'revision date format');
		$date = date_i18n( $datef, strtotime( $restored_from_meta[ 'restored_time' ] ) );
		$time_diff = human_time_diff( $restored_from_meta[ 'restored_time' ] ) ;
		?>
		<hr />
		<div id="revisions-meta-restored">
			<?php
			printf(
				/* translators: restored revision details: 1: gravatar image, 2: author name, 3: time ago, 4: date */
				__( 'Previously restored by %1$s %2$s, %3$s ago (%4$s)' ),
				get_avatar( $author->ID, 24 ),
				$author->display_name,
				$time_diff,
				$date
			);
			?>
		</div>
		<?php
	}
	echo "</ul>";
	echo "</div>";
	
}
function buddyforms_wp_revisions_to_keep( $post ) {
	$num = WP_POST_REVISIONS;
	
	if ( true === $num )
		$num = -1;
	else
		$num = intval( $num );

	if ( ! post_type_supports( $post->post_type, 'revisions' ) )
		$num = 0;

	return (int) apply_filters( 'wp_revisions_to_keep', $num, $post );
}
function buddyforms_wp_revisions_enabled( $post ) {
	return buddyforms_wp_revisions_to_keep( $post ) != 0;
}
function buddyforms_wp_get_post_revisions( $post_id = 0, $args = null ) {
	$post = get_post( $post_id );
	if ( ! $post || empty( $post->ID ) || ! buddyforms_wp_revisions_enabled( $post ) )
		return array();

	$defaults = array( 'order' => 'DESC', 'orderby' => 'date' );
	$args = wp_parse_args( $args, $defaults );
	$args = array_merge( $args, array( 'post_parent' => $post->ID, 'post_type' => 'revision', 'post_status' => 'inherit' ) );

	if ( ! $revisions = get_children( $args ) )
		return array();

	return $revisions;
}
function buddyforms_wp_post_revision_title_expanded( $revision,$post_id, $link = true ) {
	if ( !$revision = get_post( $revision ) )
		return $revision;

	if ( !in_array( $revision->post_type, array( 'post', 'page', 'revision' ) ) )
		return false;

	$author = get_the_author_meta( 'display_name', $revision->post_author );
	/* translators: revision date format, see http://php.net/date */
	$datef = _x( 'j F, Y @ G:i:s', 'revision date format');

	$gravatar = get_avatar( $revision->post_author, 24 );

	$date = date_i18n( $datef, strtotime( $revision->post_modified ) );
	if ( $link && current_user_can( 'edit_post', $revision->ID ) && $link = trailingslashit( bp_loggedin_user_domain() ).get_post_type($post_id).'?post_id='.$post_id.'&revision_id='.$revision->ID  )
		$date = "<a href='$link'>$date</a>";

	$revision_date_author = sprintf(
		/* translators: post revision title: 1: author avatar, 2: author name, 3: time ago, 4: date */
		_x( '%1$s %2$s, %3$s ago (%4$s)', 'post revision title' ),
		$gravatar,
		$author,
		human_time_diff( strtotime( $revision->post_modified ), current_time( 'timestamp' ) ),
		$date
	);

	$autosavef = __( '%1$s [Autosave]' );
	$currentf  = __( '%1$s [Current Revision]' );

	if ( !wp_is_post_revision( $revision ) )
		$revision_date_author = sprintf( $currentf, $revision_date_author );
	elseif ( wp_is_post_autosave( $revision ) )
		$revision_date_author = sprintf( $autosavef, $revision_date_author );

	return $revision_date_author;
}
// shortcode to add the form everywere easely ;-)
add_shortcode('buddyforms_create_edit_form', 'buddyforms_create_edit_form');