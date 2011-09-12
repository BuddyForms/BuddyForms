<?php
add_shortcode('create_group_type_form', 'create_group_type_form');
function create_group_type_form($atts,$content = null){
	global $cc_page_options, $post, $bp, $current_user;
      
	get_currentuserinfo();	
	
	extract(shortcode_atts(array(
		'posttype' => 'group',
		'taxonomy' => 'group_cat',
	), $atts));
	
	$customfields = $bp->bp_cgt->cgt_custom_fields;
		
	foreach($customfields[$posttype] as $key => $value) {
			if($value == "") {
				unset($customfields[$posttype][$key]);
			}
	}
	
	//If the form is submitted
	if(isset($_POST['submitted'])) {
	   
		//Check to make sure that the post title field is not empty
		if(trim($_POST['editpost_title']) === '') {
			$titleError = _('Please enter a title','cgt');
			$hasError = true;
        } else {
            $title = trim($_POST['editpost_title']);
        }
 
        //Check to make sure that content is submitted
        if(trim($_POST['editpost_content']) === '')  {
            $contentError = _('Please enter a content','cgt');
            $hasError = true;
        } else {
            $content = trim($_POST['editpost_content']);
        }
		
		if($_FILES['async-upload']['error'] != 0) {
		 	$fileError = 'Please select an image';
          	$hasError = true;
	    }
	    
	    //If there is no error, send the form
		if(!isset($hasError)) {
			$tags = $_POST['editpost_tags'];
			$permalink = get_permalink( $_POST['editpost_id'] );
			$my_post = array(
				'post_author' => $current_user->ID,
				'post_title' => $_POST['editpost_title'],
				'post_content' => $_POST['editpost_content'],
				'post_type' => $posttype,
				'post_status' => 'publish'
				);
	 
			// insert the new form
			$post_id = wp_insert_post($my_post);
			 
			//set the custom post type categories
			wp_set_post_terms( $post_id, $_POST['cat'], 'group_cat', false);
			 
			//set the custom post type cats
			//wp_set_post_terms($post_id, $tags, 'group-tag', false );
			// Do the wp_insert_post action to insert it
			do_action('wp_insert_post', 'wp_insert_post');
			
			foreach($customfields[$posttype] as $customfield) : 
				update_post_meta($post_id, $customfield, $_POST[$customfield] );
			endforeach;
			
			if ( !empty( $_FILES ) ) {  
		        require_once(ABSPATH . 'wp-admin/includes/admin.php');  
		        $id = media_handle_upload('async-upload', $post_id ); //post id of Client Files page  
		
		        unset($_FILES);  
		        if ( is_wp_error($id) ) {  
		            $errors['upload_error'] = $id;  
		            $id = false;  
		        }  
		        set_post_thumbnail($post_id, $id);
		      
		        if ($errors) {  
		            $fileError ="<p>There has bean an error uploading the image.</p>";  
		            $hasError = true;
		        }  
    		}        
    	} 
    	if(empty($hasError)) { ?>
		<div class="thanks">
		<h1><?php _e('Saved', 'cgt')?></h1>
	<p><?php _e('Post has bean created.','cgt'); ?> </p>
		</div>
	<?php } 	
	} 
	?>
	
	<div class="tier_hinzufuegen">
	

	<?php if($titleError != '') { ?>
		<div class="error"><?php echo $titleError;?></div>
	<?php } ?>
	
	<?php if($contentError != '') { ?>
		<div class="error"><?php echo $contentError; ?></div>
	<?php } ?>
	
	<?php if($fileError != '') { ?>
		<div class="error"><?php echo $fileError; ?></div>
	<?php } ?>
	
	
	<?php echo do_shortcode('[cc_accordion_start id="1"]'); ?>
	<h3>Create Post</h3>
	<?php echo do_shortcode('[cc_a_content_start  id="1"]'); ?>

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
	 
	<?php else : ?>
	
	<div class="gform_wrapper">
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data" id="editpost" method="POST">
			
	  
    <?php wp_nonce_field('client-file-upload'); ?>  
    <input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />  
    </p>  
      		<ol class="forms">
			
			<li><div class="label"><label for="editpost_title"><?php _e('Name','cgt'); ?>:</label></div>
			<input type="text" name="editpost_title" id="editpost_title" value="<?php if(isset($_POST['editpost_title'])) { if(function_exists('stripslashes')) { echo stripslashes($_POST['editpost_title']); } else { echo $_POST['editpost_title']; } } ?>" class="requiredField" />
			</li>
			
			<div id="categories">
			<li><div class="label"><label for="editpost_category" class="inputlable"><?php _e('Tierart', 'cgt'); ?>:</label></div>
				<?php wp_dropdown_categories(array('taxonomy' => 'tierart', 'hide_empty' => 0, 'hierarchical' => 1, 'show_option_none' => 'Bitte Tierart w&auml;hlen')); ?>
			</li>
			 
			<li class="textarea"><div class="label"><label for="editpost_content"><?php _e('Beschreibung', 'cgt');?>:</label></div>
			<textarea name="editpost_content" id="editpost_content" rows="20" cols="30" class="requiredField"><?php if(isset($_POST['editpost_content'])) { if(function_exists('stripslashes')) { echo stripslashes($_POST['editpost_content']); } else { echo $_POST['editpost_content']; } } ?></textarea>
			</li>
			 
			<?php if(!empty($customfields[$posttype])){ ?>
				<?php foreach($customfields[$posttype] as $customfield) : ?>
					<?php $customfield_value = get_post_meta(get_the_ID(), $customfield, true); ?>
					<li><div class="label"><label for="<?php echo $customfield ?>"><?php _e($customfield, 'cgt');?>:</label></div>
					<input type="text" name="<?php echo $customfield ?>" id="link" value="<?php if(isset($_POST[$customfield])) { if(function_exists('stripslashes')) { echo stripslashes($_POST[ $customfield ]); } else { echo $_POST[ $customfield ]; } } ?>" class="" />
					</li>
				<?php endforeach ?>
			<?php } ?>	 
			
			<li id="upload-img">  
    		<div class="label"><label for="upload-img">Neues Tierbild hochladen</label></div>  
   			 <input type="file" id="async-upload" name="async-upload"> 
    		</li>  
    
			
			<li class="buttons"><input type="hidden" name="submitted" id="submitted" value="true" class="requiredField" /><button type="submit" id="submitted" class="button"><?php _e('Submit','cgt'); ?></button></li>
			</div> <!-- end #more -->
			</ol>
		</form>
		</div>

	<?php endif; ?>
	<?php echo do_shortcode('[cc_a_content_end]'); ?>
	<?php echo do_shortcode('[cc_accordion_end]'); ?>	
</div>
<?php 
}

function cgt_locate_template($file){
	if (locate_template( array( $file ), false )) {
	locate_template( array( $file ), true );
	} else {
		include( BP_CGT_TEMPLATE_PATH .$file );
	}
}

		
?>