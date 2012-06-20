<?php

add_action( 'groups_before_delete_group', 'delete_product_post' );

function delete_product_post($group_id){
    
    $groups_post_id = groups_get_groupmeta( $group_id, 'group_post_id' );
    
    wp_delete_post($groups_post_id);
    
}

add_shortcode('create_group_type_form', 'create_group_type_form');
function create_group_type_form($atts,$content = null){
	global $cc_page_options, $post, $bp, $current_user, $_chosen_attributes, $woocommerce, $_attributes_array;
      
	get_currentuserinfo();	
 
	extract(shortcode_atts(array(
		'posttype' => $bp->current_component,
		'taxonomy' => $bp->current_component.'_category',
		'atrebute' => '', 
	), $atts));
	
   $attribute_taxonomies = $woocommerce->attribute_taxonomies; 
			
	$customfields = $bp->bp_cgt->cgt_custom_fields;
		
	foreach($customfields[$posttype] as $key => $value) {
			if($value == "") {
				unset($customfields[$posttype][$key]);
			}
	}
	if ( !function_exists( 'wp_editor' ) ) {
	require_once ABSPATH . '/wp-admin/includes/post.php' ;
	wp_tiny_mce();
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
				'post_status' => 'pending'
				);
	 
			// insert the new form
			$post_id = wp_insert_post($my_post);
			 
			//set the custom post type categories
			echo $taxonomy;
			wp_set_post_terms( $post_id, $_POST[$taxonomy], $taxonomy, false);
			 		 
			if ( $attribute_taxonomies ) : 
				foreach ($attribute_taxonomies as $tax) :
			    	
			    	$attribute_name = strtolower(sanitize_title($tax->attribute_name));
			    	$attribute_tax = $woocommerce->attribute_taxonomy_name($attribute);  
					
					//set the custom post type categories
					wp_set_post_terms( $post_id, $_POST[$attribute_tax.$attribute_name], $attribute_tax.$attribute_name, false);
		
			 	endforeach;    	
		    endif;
			
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
	
	<div class="hinzufuegen">
	

	<?php if($titleError != '') { ?>
		<div class="error"><?php echo $titleError;?></div>
	<?php } ?>
	
	<?php if($contentError != '') { ?>
		<div class="error"><?php echo $contentError; ?></div>
	<?php } ?>
	
	<?php if($fileError != '') { ?>
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
							wp_editor( '', 'editpost_content', array(
								'media_buttons' => false,
								'dfw'		=> false
							) );
						}
					?>
				</div>
	        </div>

			<div id="categories">
			<li><div class="label"><label for="editpost_category" class="inputlable"><?php _e('Category', 'cgt'); ?>:</label></div>
				<?php wp_dropdown_categories(array('taxonomy' => $taxonomy, 'hide_empty' => 0, 'hierarchical' => 1, 'show_option_none' => 'Bitte ' . $posttype . ' kategorie w&auml;hlen', 'id' => $taxonomy, 'name' => $taxonomy)); ?>
			</li>
			</div> 
			
			<?php if($posttype == 'product') { ?>
				
				<div id="atrebutes">
				<li><div class="label"><label for="editpost_atrebutes" class="inputlable"><?php _e('Atrebute', 'cgt'); ?>:</label></div>
				<?php  
				
				
				if ( $attribute_taxonomies ) : 
					foreach ($attribute_taxonomies as $tax) :
				    	
				    	$attribute_name = strtolower(sanitize_title($tax->attribute_name));
				    	$attribute_tax = $woocommerce->attribute_taxonomy_name($attribute);  
						
						wp_dropdown_categories(array(  'taxonomy' => $attribute_tax.$attribute_name, 'hide_empty' => 0, 'hierarchical' => 1, 'show_option_none' => 'Bitte ' . $attribute_name . ' atrebut w&auml;hlen', 'id' => $attribute_name, 'name' => $attribute_tax.$attribute_name));
				  
				 	endforeach;    	
			    endif;  
				?>
			
				</li>
				</div>
			
			<?php } ?>
						 			 
			<?php if(!empty($customfields[$posttype])){ ?>
				<?php foreach($customfields[$posttype] as $customfield) : ?>
					<?php $customfield_value = get_post_meta(get_the_ID(), $customfield, true); ?>
					<li><div class="label"><label for="<?php echo $customfield ?>"><?php _e($customfield, 'cgt');?>:</label></div>
					<input type="text" name="<?php echo $customfield ?>" id="link" value="<?php if(isset($_POST[$customfield])) { if(function_exists('stripslashes')) { echo stripslashes($_POST[ $customfield ]); } else { echo $_POST[ $customfield ]; } } ?>" class="" />
					</li>
				<?php endforeach ?>
			<?php } ?>	 
			
			<li id="upload-img">  
    		<div class="label"><label for="upload-img">Neues Featured Image hochladen</label></div>  
   			 <input type="file" id="async-upload" name="async-upload"> 
    		</li>  
    
			
			<li class="buttons"><input type="hidden" name="submitted" id="submitted" value="true" class="requiredField" /><button type="submit" id="submitted" class="button"><?php _e('Submit','cgt'); ?></button></li>
			</ol>
		</form>
	</div>

	<?php endif; ?>
	
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


add_action('edit_form_advanced', 'app_post_metabox');
function app_post_metabox(){    
    global $post;
    
    if(!isset($post))
        return;
    
    if ($post->post_type != 'product')
        return;
        
    $app_post_options=app_get_post_meta();
    ?>

    <div id="app_page_metabox" class="postbox">
    <div class="handlediv" title="<?php _e('klick','buddypress'); ?>">
        <br />
    </div>
    <h3 class="hndle"><?php _e('Dieses Produkt geh&ouml;rt zu einer Firma')?></h3>
    <div class="inside">
    
    <?php wp_nonce_field('app_post_metabox','app_post_meta_nonce'); ?>

    <p>Firma w&auml;hlen:<br />
            <ul class="reg_groups_list">
                    <select id="app_from_company" name="app_from_company">
    <option value="0">--</option>       
                <?php $i = 0; ?>
                <?php if ( bp_has_groups('type=alphabetical') ) : while ( bp_groups() ) : bp_the_group(); ?>
                    <?php if ( bp_get_group_status() == ('public' || 'private')) { ?>
                    <?php if(groups_get_groupmeta( bp_get_group_id(), 'group_type' ) == 'firma'){?>
                    
                    <option value="<?php bp_group_id(); ?>" <?php selected( $app_post_options['app_from_company'], bp_get_group_id() ); ?>><?php bp_group_name(); ?></option>       
    
                    <?php } ?>
                    <?php } ?>
                <?php $i++; ?>
                <?php endwhile; /* endif; */ ?>
                </select>
                <?php else: ?>
                <p class="reg_groups_none">No selections are available at this time.</p>
                <?php endif; ?>
            </ul>
    </div>  
    </div>
<?php
}
 
add_action('save_post','app_add_post_meta');
function app_add_post_meta(){

    global $post;
    
    if(!isset($post))
        return;
    
    if ($post->post_type != 'product')
        return;
    
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
    {
         return $post_id;
    }
    
    $app_post_options=app_get_post_meta();
    
        update_post_meta($post->ID, "app_from_company",app_clean_input( $_POST["app_from_company"], 'text') );
        
        $company_apps = groups_get_groupmeta( $app_post_options['app_from_company'], 'company_apps' );
        if(isset($company_apps[$post->ID])){
            unset($company_apps[$post->ID]); 
            groups_update_groupmeta( $app_post_options['app_from_company'], 'company_apps', $company_apps);
        }
        
        $company_apps = groups_get_groupmeta( $_POST["app_from_company"], 'company_apps' );
        $company_apps[$post->ID] = $post->ID;
        groups_update_groupmeta( app_clean_input( $_POST["app_from_company"], 'text'), 'company_apps', $company_apps);
}
 
function app_get_post_meta(){
    global $post;
    $app_page['app_from_company']=get_post_meta($post->ID,"app_from_company", true);
    return $app_page;
} 


function app_clean_input( $input, $type ) {

    global $allowedposttags;
    $cleanInput = false;
    
    switch ($type) {
      case 'text':
        $cleanInput = wp_filter_nohtml_kses ( $input );
        break;
          case 'checkbox':
            $input === '1'? $cleanInput = '1' : $cleanInput = '';
        break;
          case 'html':
            $cleanInput = wp_kses( $input, $allowedposttags);
        break;
    default:
        $cleanInput = false;
        break;
    }
    return $cleanInput;
}
?>