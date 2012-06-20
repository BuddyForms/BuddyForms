<div class="content">
	<?php 
	$customfields = get_option('cgt_custom_fields');

		
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

		
	    //If there is no error, send the form
		if(!isset($hasError)) {
			$tags = $_POST['editpost_tags'];
			$permalink = get_permalink( $_POST['editpost_id'] );
			$my_post = array(
				'ID'		=> $_POST['editpost_id'],
				'post_title' => $_POST['editpost_title'],
				'post_content' => $_POST['editpost_content'],
				'tags_input' => $_POST['editpost_tags'],
				'post_category' => array( (int)$_POST['editpost_cat'] ),
				'post_type' => $_POST['editpost_post_type'],
				'post_status' => 'publish'
				);
	 
			// insert the new form
			$post_id = wp_update_post($my_post);
			 
			//set the custom post type categories
			wp_set_post_terms( $post_id, $_POST['cat'], 'group_cat', false);
					
			// Do the wp_insert_post action to insert it
			do_action('wp_insert_post', 'wp_insert_post');
			
			foreach($customfields[$_POST['editpost_post_type']] as $customfield) : 
            	update_post_meta($post_id, $customfield, $_POST[$customfield] );
			endforeach;
			
		}
		
		if ( !empty( $_FILES ) ) {  
        require_once(ABSPATH . 'wp-admin/includes/admin.php');  
        $id = media_handle_upload('async-upload', $_POST['post_id'] ); //post id of Client Files page  

        unset($_FILES);  
        if ( is_wp_error($id) ) {  
            $errors['upload_error'] = $id;  
            $id = false;  
        }  
        set_post_thumbnail($_POST['post_id'], $id);
  
        
    }
		
	if(!isset($hasError)) { ?>
	<div class="thanks">
	<h1><?php _e('Saved', 'cgt')?></h1>
	<p><?php _e('Post has bean created.','cgt'); ?> </p>
	</div>
	<?php } 
		 
	} 
	
 
	?>

	<div class="clear"></div>
		
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
			 
			<input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e('Log In'); ?>" tabindex="100" />
			<input type="hidden" name="cgtcookie" value="1" />
			<input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
		 
		</form>
	 
	<?php else : ?>
<?php global $post, $group_type, $wc_query; ?>

<?php $groups_post_id = groups_get_groupmeta( bp_get_group_id(), 'group_post_id' ); ?>
<?php $group_type = groups_get_groupmeta( bp_get_group_id(), 'group_type' ); ?>

<?php $wc_query = new WP_Query( array('post_type' => $group_type, 'p' => $groups_post_id ) ); ?>

<?php if ( $wc_query->have_posts() ) while ( $wc_query->have_posts() ) : $wc_query->the_post(); ?>
	
		
	<?php if ( bp_group_is_member() ) { ?>
	
	<div class="gform_wrapper">
		<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data" id="editpost" method="POST">
			
	  
    <p>  
    <input type="hidden" name="post_id" id="post_id" value="<?php the_ID()?>" />  
    <?php wp_nonce_field('client-file-upload'); ?>  
    <input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />  
    </p>  
      
    
	
			<input type="hidden" name="editpost_id" value="<?php echo get_the_ID(); ?>" />
			<input type="hidden" name="editpost_post_type" value="<?php echo get_post_type( get_the_ID() ) ?>" />
			
			<ol class="forms">
			
			<li><div class="label"><label for="editpost_title"><?php _e('Title','cgt'); ?>:</label></div>
			<input type="text" name="editpost_title" id="editpost_title" value="<?php the_title(); ?>" class="requiredField" />
			<?php if($titleError != '') { ?>
				<span class="error"><?php echo $titleError;?></span>
			<?php } ?>
			</li>
			
			<div id="categories">
			<li><div class="label"><label for="editpost_category" class="inputlable"><?php _e('Category'); ?>:</label></div>
				<?php 
				
				foreach (get_object_taxonomies($post, 'names') as $tax_name ) {
				   		wp_dropdown_categories(array('taxonomy' => $tax_name, 'hide_empty' => 0, 'hierarchical' => 1, 'show_option_none' => 'Bitte ' . $tax_name . ' w&auml;hlen'));
			
				}
				
				?>
			</li>
			 
			 
	<div id="doc-content-textarea">
		<label id="content-label" for="doc[content]"><?php _e( 'Content', 'bp-docs' ) ?></label>        
		<div id="editor-toolbar">
			<?php /* No media support for now
			<div id="media-toolbar">
			    <?php  echo bpsp_media_buttons(); ?>
			</div>
			*/ ?>
			<?php 
				if ( function_exists( 'wp_editor' ) ) {
					wp_editor( get_the_content(), 'editpost_content', array(
						'media_buttons' => false,
						'dfw'		=> false
					) );
				}
			?>
		</div>
        </div>
			 
			<?php if(!empty($customfields[get_post_type( get_the_ID() )])){ ?>
				<?php foreach($customfields[get_post_type( get_the_ID() )] as $customfield) : ?>
					<?php $customfield_value = get_post_meta(get_the_ID(), $customfield, true); ?>
					<li><div class="label"><label for="<?php echo $customfield ?>"><?php _e($customfield, 'cgt');?>:</label></div>
					<input type="text" name="<?php echo $customfield ?>" id="link" value="<?php echo $customfield_value ?>" class="" />
					</li>
				<?php endforeach ?>
			<?php } ?>	 
			
			<li id="upload-img">  
    		<div class="label"><label for="upload-img">Featured image</label></div>  
   			 <input type="file" id="async-upload" name="async-upload"> 
    		</li>  
    
			
			<li class="buttons"><input type="hidden" name="submitted" id="submitted" value="true" /><button type="submit" id="submitted" class="button"><?php _e('Submit','cgt'); ?></button></li>
			</div> <!-- end #more -->
			</ol>
		</form>
		</div>
		
		<?php } else { _e('You do not have the correct rights to edit this post', 'cgt'); }?>
		
		<?php endwhile; endif;?>
	

</div>
