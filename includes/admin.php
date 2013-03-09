<?php
/**
 * Create "CGT Options" sub nav menu under the Buddypress main admin nav
 *
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta
 */
function cgt_create_menu() {
	add_menu_page( 'CGT Options', 'CGT Options', 'edit_posts', 'cgt_options_page', 'cgt_options_content' );
}  
add_action('admin_menu', 'cgt_create_menu');

function save_item_order() {
    global $wpdb;
	
	$cgt_options = get_option('cgt_options');
    $order = explode(',', $_POST['order']);
    $counter = 0;
	
	foreach ($order as $item_id) {
    	$item_id = explode('/', $item_id);
	    $cgt_options[$item_id[0]][$item_id[1]][$item_id[2]][$item_id[3]][$item_id[4]] = $counter;
		$counter++;
 	}
	
	update_option("cgt_options", $cgt_options);
    die();
}
add_action('wp_ajax_item_sort', 'save_item_order');
add_action('wp_ajax_nopriv_item_sort', 'save_item_order');

function item_delete(){
	$post_args = explode('/', $_POST['post_args']);
	
	$cgt_options = get_option('cgt_options');
	
	
	unset( $cgt_options[$post_args[0]][$post_args[1]][form_fields][$post_args[3]] );
    
	update_option("cgt_options", $cgt_options);
    die();
}
add_action('wp_ajax_item_delete', 'item_delete');
add_action('wp_ajax_nopriv_item_delete', 'item_delete');

function view_form_fields($args){
	$post_args = explode('/', $_POST['post_args']);
	$numItems = $_POST['numItems'];
	$cgt_options = get_option('cgt_options');
	
	if(is_array($args)){
		extract($args);
		$post_args[0] = $field_type;
		$post_args[1] = $post_type;
	}
	
	if($field_id == '')
		$field_id = $mod5 = substr(md5(time() * rand()), 0, 10);;
		
	if($field_position =='')
		$field_position = $numItems;
	
	$form_fields_new = Array();
	
	switch ($post_args[0]) {
		case 'Text':
		
			$form_fields_new[0] = new Element_Textbox("Name:", "cgt_options[new_group_types][".$post_args[1]."][form_fields][".$field_id."][name]", array('value' => $cgt_options['new_group_types'][$post_args[1]][form_fields][$field_id][name]));
			$form_fields_new[1] = new Element_Hidden("cgt_options[new_group_types][".$post_args[1]."][form_fields][".$field_id."][type]", 'Text');
			$form_fields_new[2] = new Element_Hidden("cgt_options[new_group_types][".$post_args[1]."][form_fields][".$field_id."][order]", $field_position, array('id' => 'new_group_types/' . $post_args[1] .'/form_fields/'. $field_id .'/order'));
			
			$form_fields_new[3] = new Element_Textbox("Test 1:", "cgt_options[new_group_types][".$post_args[1]."][form_fields][".$field_id."][test1]", array('value' => $cgt_options['new_group_types'][$post_args[1]][form_fields][$field_id][test1]));
		
		break;
		case 'Textarea':
			$field_value = 'Felder';
			break;
		case 'Link':
			$field_value = 'Felder';
			break;
		case 'Mail':
			$field_value = 'Felder';
			break;
		case 'Dropdown':
			$field_value = 'Felder';
			break;
		case 'Radiobutton':
			$field_value = 'Felder';
			break;
		case 'Checkbox':
			$field_value = 'Felder';
			break;
		case 'Taxonomy':
			$field_value = 'Felder';
			break;
		case 'Hidden':

			break;
		case 'AttachGroupType':
			$field_value = 'Felder';
			break;
	}

	ob_start(); ?>
	<li id="new_group_types/<?php echo $post_args[1] ?>/form_fields/<?php echo $field_id ?>/order" class="list_item <?php echo $field_id ?>">
	<div class="accordion_fields">
		<div class="accordion-group">
			<div class="accordion-heading"><a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion_text" href="#accordion_<?php echo $post_args[1]; ?>_<?php echo $post_args[0].'_'.$field_id; ?>"><?php echo $post_args[0]; ?></a> - <a class="delete" id="<?php echo $field_id ?>" href="new_group_types/<?php echo $post_args[1] ?>/form_fields/<?php echo $field_id ?>/order">X</a></div>
			<div id="accordion_<?php echo $post_args[1]; ?>_<?php echo $post_args[0].'_'.$field_id; ?>" class="accordion-body collapse">
				<div class="accordion-inner">
					<?php 	
					//print_r($form_fields_new);
					foreach ($form_fields_new as $key => $value) {
						$form_fields_new[$key]->render();	
					}
					?>
				</div>
	    	</div>
		</div>
	<div>
	</li>	
	<?php	
	$field_html = ob_get_contents();
	ob_end_clean();

	if(is_array($args)){
		return $field_html;
	}else{
		echo $field_html;
		die();
	}


}
add_action( 'wp_ajax_view_form_fields', 'view_form_fields' );
add_action( 'wp_ajax_nopriv_view_form_fields', 'view_form_fields' );

/**
 * Display the settings page
 *
 * @package BuddyPress Custom Group Types
 * @since 0.2-beta
 */
function cgt_options_content() { 
		session_start();
	?>
     
     <script type="text/javascript">
	
</script>
     
	<script>
	jQuery(document).ready(function(jQuery) {        
	    var itemList = jQuery('#sortable');
		
		jQuery(".delete").click(function(){
			
			 var del_id = jQuery(this).attr('id');
			 var action = jQuery(this); 
			if (confirm('Delete Permanently'))
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "item_delete", "post_args": action.attr('href')},
					success: function(data){
						jQuery("." + del_id).remove();
					}
				});
			else return false;
			
			
			return false;
		});
		
		jQuery('.action').click(function(){
			var numItems = jQuery('.list_item').length;
		
			var action = jQuery(this);
			
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "view_form_fields", "post_args": action.attr('href'), 'numItems': numItems},
				success: function(data){
				jQuery('#sortable').append(data);
			}
			});
			return false;
		});
	
	    itemList.sortable({
	        update: function(event, ui) {
	            jQuery('#loading-animation').show(); // Show the animate loading gif while waiting
	
	            opts = {
	                url: ajaxurl,
	                type: 'POST',
	                async: true,
	                cache: false,
	                dataType: 'json',
	                data:{
	                    action: 'item_sort', // Tell WordPress how to handle this ajax request
	                    order: itemList.sortable('toArray').toString() // Passes ID's of list items in  1,3,2 format
	                },
	                success: function(response) {
	                    jQuery('#loading-animation').hide(); // Hide the loading animation
	                    var testst = itemList.sortable('toArray');
	                   for (var key in testst){
	                   	alert(key + ': ' + testst[key]);
	                   	jQuery("input[id='" + testst[key] + "']").val(key); 
	                   }
	                    return; 
	                },
	                error: function(xhr,textStatus,e) {  // This can be expanded to provide more information
	                    alert(e);
	                    // alert('There was an error saving the updates');
	                    jQuery('#loading-animation').hide(); // Hide the loading animation
	                    return; 
	                }
	            };
	            
	            jQuery.ajax(opts);
	        }
	    }); 
	});
	</script>

	<style>
		.accordion_sidebar{
			float:right;
		}
		.accordion_fields{
			margin-right: 300px;
		}
		
	</style>
	
	<div class="wrap">
		
		<?php screen_icon('themes') ?>
		<h2>CGT - General Settings</h2>
	      
		<div id="post-body">
			<div id="post-body-content">            
				<?php cgt_settings_page(); ?>
			</div>
		</div>
	
	</div>
<?php
}

/**
 * Create the option settings page
 *
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta
 */
function cgt_settings_page() {
    global $bp, $cgt;
    
	// Check that the user is allowed to update options
	if (!current_user_can('manage_options')) {
	    wp_die('You do not have sufficient permissions to access this page.');
	}	
	
	if (isset($_POST['submit'])) {
		$cgt_options = $_POST["cgt_options"];
		update_option("cgt_options", $cgt_options);
		?><div id="message" class="updated"><p>CGT Settings Saved :-)</p></div><?php
	}
	
	// Get all needed values
	$cgt_options = get_option('cgt_options');
		
	// Get all post types
    $args=array(
		'_builtin' => false
    ); 
    $output = 'names'; // names or objects, note names is the default
    $operator = 'and'; // 'and' or 'or'
    $post_types=get_post_types($args,$output,$operator); 
     
    foreach($post_types as $key => $value) {
		if(array_key_exists($key, (array)$cgt->new_post_type_slugs)) {
    		unset($post_types[$key]);
		}
    }
	
	// Form starts
	$form = new Form("cgt_form");
	$form->configure(array(
		"prevent" => array("bootstrap", "jQuery"),
		"action" => $_SERVER['REQUEST_URI'],
	));
	
	wp_enqueue_script('bootstrapjs', plugins_url('PFBC/Resources/bootstrap/js/bootstrap.min.js', __FILE__), array('jquery') );
    wp_enqueue_style('bootstrapcss', plugins_url('PFBC/Resources/bootstrap/css/bootstrap.min.css', __FILE__));
    wp_enqueue_script('jQuery');
    wp_enqueue_script('jquery-ui-sortable'); 
	    
	$form->addElement(new Element_Hidden("submit", "submit"));
	$form->addElement(new Element_Button('submit','submit',array('id' => 'submit', 'name' => 'action','value' => 'submit')));
	
	$form->addElement(new Element_HTML('<div class="tabbable tabs-top"><ul class="nav nav-tabs"><label for="cgt_form-element-1"></label>
		<li class="active"><a href="#general-settings" data-toggle="tab">General Settings</a></li>'));
	if(is_array($cgt_options['existing_post_types'])){
		foreach( $cgt_options['existing_post_types'] as $key => $existing_post_types) {
			$form->addElement(new Element_HTML('<li class=""><a href="#'.$existing_post_types.'" data-toggle="tab">'.$existing_post_types.'</a></li>'));
		}
	}	
	$form->addElement(new Element_HTML('</ul></div>
		<div class="tab-content"><div class="subcontainer tab-pane fade in active" id="general-settings">'));
	$form->addElement(new Element_Checkbox("Use existing post types as custom group type::", "cgt_options[existing_post_types][]", $post_types, array('value' => $cgt_options['existing_post_types'])));
	$form->addElement(new Element_HTML('</div>'));
	
	if(is_array($cgt_options['existing_post_types'])){
		foreach( $cgt_options['existing_post_types'] as $key => $existing_post_types) {
			
	    	$form->addElement(new Element_HTML('<div class="subcontainer tab-pane fade in" id="'.$existing_post_types.'">'));
				
			$form->addElement(new Element_HTML('
			<div class="accordion_sidebar" id="accordion_'.$existing_post_types.'">
				<div class="accordion-group">
					<div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_'.$existing_post_types.'" href="#accordion_'.$existing_post_types.'_content">'.$existing_post_types.' Label</a></div>
					<div id="accordion_'.$existing_post_types.'_content" class="accordion-body collapse in">
						<div class="accordion-inner">')); 
							$form->addElement(new Element_Textbox("Name:", "cgt_options[new_group_types][".$existing_post_types."][name]", array('value' => $cgt_options['new_group_types'][$existing_post_types]['name'])));
							$form->addElement(new Element_Textbox("Singular Name:", "cgt_options[new_group_types][".$existing_post_types."][singular_name]", array('value' => $cgt_options['new_group_types'][$existing_post_types]['singular_name'])));
							$form->addElement(new Element_Textbox("Overwrite slug if needed *:", "cgt_options[new_group_types][".$existing_post_types."][slug]", array('value' => $cgt_options['new_group_types'][$existing_post_types]['slug'])));
							
							$form->addElement(new Element_HTML('
						</div>
			    	</div>
				</div>
		 		<div class="accordion-group">
					<div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_'.$existing_post_types.'" href="#accordion_'.$existing_post_types.'_status">'.$existing_post_types.' Status</a></div>
				    <div id="accordion_'.$existing_post_types.'_status" class="accordion-body collapse in">
						<div class="accordion-inner">')); 
							$form->addElement(new Element_Select("Status:", "cgt_options[new_group_types][".$existing_post_types."][status]", array('publish','pending','draft'),array('value' => $cgt_options['new_group_types'][$existing_post_types]['status'])));
						
							$form->addElement(new Element_HTML('
						</div>
					</div>
				</div>		  
				<div class="accordion-group">
					<div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_'.$existing_post_types.'" href="#accordion_'.$existing_post_types.'_fields">'.$existing_post_types.' Form Fields</a></div>
				    <div id="accordion_'.$existing_post_types.'_fields" class="accordion-body collapse in">
						<div class="accordion-inner">
							<div id="#idkommtnoch">
								<p><a href="Text/'.$existing_post_types.'" class="action">Text</a></p>
								<p><a href="Textarea/'.$existing_post_types.'" class="action">Textarea</a></p>
								<p><a href="Link/'.$existing_post_types.'" class="action">Link</a></p>
								<p><a href="Mail/'.$existing_post_types.'" class="action">Mail</a></p>
								<p><a href="Dropdown/'.$existing_post_types.'" class="action">Dropdown</a></p>
								<p><a href="Radiobutton/'.$existing_post_types.'" class="action">Radiobutton</a></p>
								<p><a href="Checkbox/'.$existing_post_types.'" class="action">Checkbox</a></p>
								<p><a href="Taxonomy/'.$existing_post_types.'" class="action">Taxonomy</a></p>
								<p><a href="Hidden/'.$existing_post_types.'" class="action">Hidden</a></p>
								<p><a href="AttachGroupType/'.$existing_post_types.'" class="action">AttachGroupType</a></p>
							</div>
						</div>
					</div>
				</div>		  
			</div>
			<div id="cgt_forms_builder_'.$existing_post_types.'" class="cgt_forms_builder">
			<h3>Hier kommt der form builder angerollt ;-)</h3>'));
		
				echo '<pre>';
				print_r($cgt_options);
				echo '</pre>';			
	
			//incision_sort($cgt_options['new_group_types'][$existing_post_types]['form_fields'],$cgt_options['new_group_types'][$existing_post_types]['order']);
				    
			$sortArray = array(); 
		    foreach($cgt_options['new_group_types'][$existing_post_types]['form_fields'] as $key => $array) { 
		        $sortArray[$key] = $array['order']; 
		    } 

    		array_multisort($sortArray, SORT_ASC, SORT_NUMERIC, $cgt_options['new_group_types'][$existing_post_types]['form_fields']); 
    
				echo '<pre>';
				print_r($cgt_options['new_group_types'][$existing_post_types]['form_fields']);
				echo '</pre>';			
	
			$form->addElement(new Element_HTML('
			<ul id="sortable">'));
			if(is_array($cgt_options['new_group_types'][$existing_post_types]['form_fields'])){
				
				foreach($cgt_options['new_group_types'][$existing_post_types]['form_fields'] as $field_id => $sad) {
					
					$field_position = $cgt_options['new_group_types'][$existing_post_types]['form_fields'][$field_id]['order'];
					
					//$field_value = $cgt_options['new_group_types'][$existing_post_types]['form_fields'][$field_id];
					
					$args = Array('field_position' => $field_position, 'field_id' => $field_id, 'field_value' => $field_value,'post_type' => $existing_post_types, 'field_type' => $cgt_options['new_group_types'][$existing_post_types]['form_fields'][$field_id][type]);
					
					$form->addElement(new Element_HTML(view_form_fields($args)));
				}
			}
			$form->addElement(new Element_HTML('</ul></div></div>'));
	    
		}	
	}
	
       
	$form->addElement(new Element_HTML('</div>'));			
		
	$form->render();
}

// $arr = array to sort. 
// $col = column to sort by. 
function incision_sort($arr, $col){ 
   for($k = 0; $k < sizeof($arr)-1; $k++){ 
       // $arr[$k+1] is possibly in the wrong place. Take it out. 
       $t = $arr[$k+1]; 
       $i = $k;    
       
       // Push $arr[i] to the right until we find the right place for $t. 
       while($i >= 0 && $arr[$i][$col] > $t[$col]){ 
           $arr[$i+1] = $arr[$i]; 
           $i--; 
       } 
       
       // Insert $t into the right place. 
       $arr[$i+1] = $t;                            
   }// End sort 
       return $arr;        
   } 

?>