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

/**
 * Display the settings page
 *
 * @package BuddyPress Custom Group Types
 * @since 0.2-beta
 */
function cgt_options_content() {
	echo tk_form( 'cgt-config', 'cgt-config', cgt_settings_page() );
}

/**
 * Create the option settings page
 *
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta
 */
function cgt_settings_page() {
    global $bp, $cgt;
    
    // Create the Array for the jQuery Taps 
    $TapArray = array();
    
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
      
     	$new_group_types = $cgt->post_types;

           
        $tab1 = '<table class="global-settings-table">';

            $tab1 .= '<tr valign="top">';   
            $tab1 .= '<th scope="row">Use existing post type as custom group type</th>';
            $tab1 .= '<th scope="row">Create new post type to use as custom group type</th>';
            $tab1 .= '</tr>';
            
            $tab1 .= '<tr><td style="vertical-align: top;">';
            
            foreach ($post_types  as $post_type ) {
                    
                $checked = false;
				
				$pt = isset( $cgt->existing_post_types[$post_type] ) ? $cgt->existing_post_types[$post_type] : '';

                if( $post_type == $pt )
                    $checked = true;
                 
                  $cgt_existing = array(
                    'id' => '',
                    'name' => 'cgt-config_values[existing_post_types]['.$post_type.']',
                    'value' => $post_type,
                    'checked' => $checked,
                    'extra' => '',
                    'before_element' => '',
                    'after_element' => ''
                );
        
                $tab1 .=  tk_checkbox($cgt_existing) . ' ' . $post_type . '<br>' ;
        
            }

            $tab1 .= '</td><td>';
          
            $new_cgt_form = '<table>';
            
            $new_cgt_form .= '<div class="hidden_fields" style="display: none;">';

            foreach( (array) $cgt->new_post_type_slugs as $key => $new_post_type_slug){
                if($new_post_type_slug)
                    $new_post_type_slugs_hidden[$new_post_type_slug] = tk_textfield(array('id' => 'new_post_type_slugs','name' => 'cgt-config_values[new_post_type_slugs]['.$key.']', 'value' => $new_post_type_slug)) ;
            $new_cgt_form .= $new_post_type_slugs_hidden[$new_post_type_slug] ;
            }
            
            $new_cgt_form .= '</div>';
            
            $new_cgt_form .= '<tr>';
            $new_cgt_form .= ' <td class="label">Slug: </td>';
            $new_cgt_form .= ' <td> ' . tk_textfield(array('multi_index' => 0, 'id' => 'new_post_type_slugs','name' => 'cgt-config_values[new_post_type_slugs][]')). ' </td>';
            $new_cgt_form .= '</tr>';
            $new_cgt_form .= '<tr><td class="create_cgt">' . tk_button('Create new CGT', 'cgt_create_new_form_submit') . '</td><tr>';
            $new_cgt_form .= '</table>';
   
            $tab1 .= $new_cgt_form;
            
            $tab1 .= '</td></tr>';
        
        $tab1 .= '</table>';
       
        $TapArray[] = array(
            'id' => 1,
            'title' => 'General Settings',
            'content' => $tab1  
        );
         
        if(!empty($new_group_types)){
            foreach($new_group_types as $new_group_type ) :
                if($new_group_type != '') {
                    
                $new_group_type_id = str_replace(' ', '', $new_group_type);
                
                $accordion_lable  = '<div>';
                $accordion_lable .= '<div>Lable for ' .$new_group_type. ' display name: </div>';
                $accordion_lable .= '<table>';
                $accordion_lable .= '<tr>';
                $accordion_lable .= ' <td>Name: </td>';
                $accordion_lable .= ' <td> ' . tk_textfield(Array('id' => 'new_group_types_name','name' => 'cgt-config_values[new_group_types]['.$new_group_type.'][name]', 'value' => $cgt->new_group_types[$new_group_type]['name'])) . ' </td>';
                $accordion_lable .= '</tr>';
                $accordion_lable .= '<tr>';
                $accordion_lable .= ' <td>Singular Name: </td>';
                $accordion_lable .= ' <td> ' . tk_textfield(Array('id' => 'new_group_types_singular_name','name' => 'cgt-config_values[new_group_types]['.$new_group_type.'][singular_name]', 'value' => $cgt->new_group_types[$new_group_type]['singular_name'])) . ' </td>';
                $accordion_lable .= '</tr>';
                $accordion_lable .= '<tr>';
                $accordion_lable .= ' <td>Status: </td>';
                
                      
                $new_field_type = new tk_form_select( array( 'id' => 'custom_field_type_status', 'name' => 'cgt-config_values[new_group_types]['.$new_group_type.'][status]', 'value' => isset( $cgt->new_group_types[$new_group_type]['status'] ) ? $cgt->new_group_types[$new_group_type]['status'] : '' ));
                $new_field_type->add_option('publish');
                $new_field_type->add_option('pending');
                $new_field_type->add_option('draft');
              
                
              $accordion_lable .= '<td>' . $new_field_type->get_html().'</td>';
                $accordion_lable .= '</tr>';
                $accordion_lable .= '<tr>';
                if(isset($new_post_type_slugs_hidden[$new_group_type])){
                    $accordion_lable .= ' <td>Slug: </td>';
                    $accordion_lable .= ' <td> ' . $new_post_type_slugs_hidden[$new_group_type] . ' </td>';
                    $accordion_lable .= '</tr>';
                } else {
                    $accordion_lable .= ' <td>Overwrite slug if needed * : </td>';
                    $accordion_lable .= ' <td> ' . tk_textfield(Array('id' => 'existing_post_type_slugs','name' => 'cgt-config_values[existing_post_type_slugs]['.$new_group_type.']', 'value' => $cgt->existing_post_type_slugs[$new_group_type])) . ' </td>';
                    $accordion_lable .= '</tr>';   
                }
                $accordion_lable .= '</table>';
                $accordion_lable .= '</div>';
                
                $accordion_custom_fields  = '<table id="table-5" class="cgt_fields" border="0" width="100%" cellpadding="0" cellspacing="0">';
                $accordion_custom_fields .= '<tbody>';
                $accordion_custom_fields .= '<tr class="nodrop nodrag">';
                $accordion_custom_fields .= '<th>Position</th>';
                $accordion_custom_fields .= '<th>Field Type</th>';
                $accordion_custom_fields .= '<th>Field meta</th>';
                $accordion_custom_fields .= '</tr>';
    
                if(!empty($cgt->custom_field_slug[$new_group_type])){
                            
                  foreach($cgt->custom_field_slug[$new_group_type] as $key => $custom_field ) {
                    if($custom_field == ''){
                        unset($cgt->custom_field_slug[$new_group_type][$key] );
                    }
                   }    
                   
                    foreach($cgt->custom_field_slug[$new_group_type] as $key => $custom_field ) {
                        
                          $accordion_custom_fields .= '<tr id="table5-row-'.$key.'">';
                          $accordion_custom_fields .= '<td class="dragHandle">'.$key.'</td><td>';
                          
                          $new_field_type = new TK_Form_select( array(
                          	'value' => isset($cgt->custom_field_type[$new_group_type][$key]) ? $cgt->custom_field_type[$new_group_type][$key] : '',
                          	'name' 	=> 'cgt-config_values[custom_field_type]['.$new_group_type.']['.$key.']', 
                          	'id' 	=> 'custom_field_type'
						  ) );
						  
                          $new_field_type->add_option('-');
                          $new_field_type->add_option('Mail');
                          $new_field_type->add_option('Link');
                          $new_field_type->add_option('Radiobutton');
                          $new_field_type->add_option('Checkbox');
                          $new_field_type->add_option('Dropdown');
                          $new_field_type->add_option('Textarea');
                          $new_field_type->add_option('Text');
                          $new_field_type->add_option('Taxonomy');
                          $new_field_type->add_option('Hidden');
                          $new_field_type->add_option('AttachGroupType');
               
              
                           $accordion_custom_fields .= '<div><div class="label">Type: </div>' . $new_field_type->get_html().'</div>';
                           $accordion_custom_fields .= '<div><div class="label">Name: </div>' . tk_textfield(Array('id' => 'custom_field_name','name' => 'cgt-config_values[custom_field_name]['.$new_group_type.']['.$key.']', 'value' => $cgt->custom_field_name[$new_group_type][$key])) . ' </div>';
                           $accordion_custom_fields .= '<div><div class="label">Discription: </div>' . tk_textfield(Array('id' => 'custom_field_discription','name' => 'cgt-config_values[custom_field_discription]['.$new_group_type.']['.$key.']', 'value' => $cgt->custom_field_discription[$new_group_type][$key])) . ' </div>';
                           $accordion_custom_fields .= '<div><div class="label">Slug: </div>' . tk_textfield(Array('id' => 'custom_field_slug','name' => 'cgt-config_values[custom_field_slug]['.$new_group_type.']['.$key.']', 'value' => $cgt->custom_field_slug[$new_group_type][$key])) . ' </div>';
                          $custom_fields_meta = '';
						  
						  $cft = isset( $cgt->custom_field_type[$new_group_type][$key] ) ? $cgt->custom_field_type[$new_group_type][$key] : '';
						  
                           switch ($cft) {
                               case 'AttachGroupType':
                               $new_field_type2 = new tk_form_select( array('value' => $cgt->custom_field_attach_group[$new_group_type][$key] , 'name' => 'cgt-config_values[custom_field_attach_group]['.$new_group_type.']['.$key.']', 'id' => 'custom_field_attach_group'));
                                  foreach ($cgt->post_types as $post_type ) {
                                   $new_field_type2->add_option($post_type);
                                  }
                                $accordion_custom_fields .=  '<div><div class="label">Taxonomie: </div>'.$new_field_type2->get_html().'</div>';
                                
                               break;
                            case 'Link':
                         
                                 $accordion_custom_fields .= '<div><div class="label">Options: </div>' . tk_textfield(Array('id' => 'custom_field_option','name' => 'cgt-config_values[custom_field_option]['.$new_group_type.']['.$key.']', 'value' => $cgt->custom_field_option[$new_group_type][$key])) . ' </div>';
                                
                               break;
                               case 'Dropdown':
                         
                                 $accordion_custom_fields .= '<div><div class="label">Value: </div>' . tk_textfield(Array('id' => 'custom_field_select','name' => 'cgt-config_values[custom_field_select]['.$new_group_type.']['.$key.']', 'value' => $cgt->custom_field_select[$new_group_type][$key])) . ' </div>';
                                
                                 if($cgt->custom_field_m_select[$new_group_type][$key] == 'on') { $checked = true; } else {  $checked = false; };
                                 $custom_fields_meta = '<div>multi select ' . tk_checkbox(array('checked' => $checked, 'value' => $cgt->custom_field_m_select[$new_group_type][$key] , 'name' => 'cgt-config_values[custom_field_m_select]['.$new_group_type.']['.$key.']', 'id' => 'custom_field_m_select')) . '</div>';
                         
                               break;
                               case 'Hidden':
                         
                                 $accordion_custom_fields .= '<div><div class="label">Value: </div>' . tk_textfield(Array('id' => 'custom_field_hidden_val','name' => 'cgt-config_values[custom_field_hidden_val]['.$new_group_type.']['.$key.']', 'value' => $cgt->custom_field_hidden_val[$new_group_type][$key])) . ' </div>';
                            
                               break;
                               case 'Taxonomy':
                                   
                                $args=array(
                                 'public'   => true,
                                  '_builtin' => false
                                  
                                ); 
                                $output = 'names'; // or objects
                                $operator = 'and'; // 'and' or 'or'
                                $taxonomies=get_taxonomies($args,$output,$operator); 
                                $new_field_type = new tk_form_select( array('value' => $cgt->custom_field_taxonomy[$new_group_type][$key] , 'name' => 'cgt-config_values[custom_field_taxonomy]['.$new_group_type.']['.$key.']', 'id' => 'custom_field_taxonomy'));
                                if  ($taxonomies) {
                                  foreach ($taxonomies as $taxonomy ) {
                                   $new_field_type->add_option($taxonomy);
                                  }
                                }
                                $accordion_custom_fields .=  '<div><div class="label">Taxonomie: </div>'.$new_field_type->get_html().'</div>';
                                
                                if(isset($cgt->custom_field_m_select[$new_group_type][$key]) && $cgt->custom_field_m_select[$new_group_type][$key] == 'on') { $checked = true; } else {  $checked = false; };
                                $custom_fields_meta = '<div>multi select ' . tk_checkbox(array('checked' => $checked, 'name' => 'cgt-config_values[custom_field_m_select]['.$new_group_type.']['.$key.']', 'id' => 'custom_field_m_select')) . '</div>';
                         
                               break;
                               }
                          $accordion_custom_fields .= '</td><td>';
                          
                          if(isset($cgt->custom_field_display[$new_group_type][$key]) && $cgt->custom_field_display[$new_group_type][$key] == 'on') { $checked = true; } else {  $checked = false; };
                          $accordion_custom_fields .= '<div>display ' . tk_checkbox(array('checked' => $checked, 'value' => isset($cgt->custom_field_display[$new_group_type][$key]) ? $cgt->custom_field_display[$new_group_type][$key] : '', 'name' => 'cgt-config_values[custom_field_display]['.$new_group_type.']['.$key.']', 'id' => 'custom_field_display')) . '</div>';
                          
                          $accordion_custom_fields .= $custom_fields_meta;
                          
                          if(isset($cgt->custom_field_required[$new_group_type][$key]) && $cgt->custom_field_required[$new_group_type][$key] == 'on') { $checked = true; } else {  $checked = false; };
                          $accordion_custom_fields .= '<div>required ' . tk_checkbox(array('checked' => $checked, 'name' => 'cgt-config_values[custom_field_required]['.$new_group_type.']['.$key.']', 'id' => 'custom_field_required')) . '</div>';
                          
                          $accordion_custom_fields .= '</td></tr>';
                    }
                }
                $accordion_custom_fields .= ' </tbody></table>';    
               
               $new_field_type = new tk_form_select( array('name' => 'cgt-config_values[custom_field_type]['.$new_group_type.'][]', 'id' => 'custom_field_type'));
               $new_field_type->add_option('-');
               $new_field_type->add_option('Mail');
               $new_field_type->add_option('Link');
               $new_field_type->add_option('Radiobutton');
               $new_field_type->add_option('Checkbox');
               $new_field_type->add_option('Dropdown');
               $new_field_type->add_option('Textarea');
               $new_field_type->add_option('Text');
               $new_field_type->add_option('Taxonomy');
               $new_field_type->add_option('Hidden');
               $new_field_type->add_option('AttachGroupType');
               
               $accordion_custom_fields .=  $new_field_type->get_html();
               $accordion_custom_fields .= ' <td> ' . tk_textfield(Array('id' => 'custom_field_slug','name' => 'cgt-config_values[custom_field_slug]['.$new_group_type.'][]' )). ' </td>';
               
               $accordion_custom_fields .= '<tr><td>' . tk_button('Add one more', 'cgt_add_form_element_submit') . '</td><tr>';
                       
                $accordion_Array[] = array(
                        'id' => 'accordion_lable_'.$new_group_type_id,
                        'title' => $new_group_type.' Lable',
                        'content' => $accordion_lable
                        );
                   
                $tabs = tk_accordion('cgt_accordion_'.$new_group_type_id , $accordion_Array, 'html' );
                  
               $TapArray[] = array(
                    'id' => $new_group_type_id,
                    'title' => $new_group_type,
                    'content' => $tabs.'<br>'.$accordion_custom_fields
                );
            
                $tabs = '';
                $accordion_Array = '';
                $accordion_lable = '';
                $accordion_custom_fields = '';
                $accordion_taxonomies = '';
                $cgt_post_type_taxonomies_options = '';

                }
           endforeach;     
        }
        ob_start(); 
    ?>
    <div class="wrap">
        
        <div class="admin_right_box">
            <p>Custom Group Types is proudly brought to you by 
                <a href="http://themekraft.com/" target="_blank" class="">
                    <img class="admin_tk_logo" />
                </a>
            </p>
            <a href="https://twitter.com/themekraft" class="twitter-follow-button" data-show-count="false" data-lang="en">Follow</a>
            <div class="fb-like" data-href="http://themekraft.com/" data-send="false" data-layout="button_count" data-width="80" data-show-faces="false" data-font="lucida grande"></div> 
        </div>
        
        <div class="headerwrap">    
            <div id="icon-buddypress" class="icon32"></div>    
            <h2>Custom Group Types Setup</h2>
            
            <script type="text/javascript">
            jQuery(document).ready(function() {
                // Initialise the table
                jQuery("#table-5").tableDnD({
                    onDrop: function(table, row) {
                        //alert(jQuery.tableDnD.serialize());
                    },
                    dragHandle: ".dragHandle" 
                });
                
                jQuery("#table-5 tr").hover(function() {
                      jQuery(this.cells[0]).addClass('showDragHandle');
                }, function() {
                      jQuery(this.cells[0]).removeClass('showDragHandle');
                });
            
            });
            </script>
            
                <div id="fb-root"></div>
        
                <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
        
                <script>(function(d, s, id) {
                  var js, fjs = d.getElementsByTagName(s)[0];
                  if (d.getElementById(id)) return;
                  js = d.createElement(s); js.id = id;
                  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1";
                  fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));
                
                jQuery.noConflict();
                jQuery(document).ready(function(){          
                    jQuery('textarea').elastic();
                    jQuery('textarea').trigger('update').linedtextarea({selectedLine: 1}).elastic();
                }); 
                </script>
        
                <style>
                    /* CGT Settings page jQuery CSS overwrite */ 
                                            
                    .ui-accordion .ui-accordion-header a { color: #888888; }
                    
                    .ui-accordion .ui-accordion-header.ui-state-hover a,
                    .ui-accordion .ui-accordion-header.ui-state-active a { 
                        color: #212121; 
                    }
                    
                    div.ui-accordion h3.ui-accordion-header:hover,
                    div.ui-accordion h3.ui-accordion-header:focus {
                        border: 1px solid #D3D3D3;
                    }
                    
                    div.ui-tabs div.ui-tabs-panel {
                        padding: 1.5em 0;
                    }
                                
        
        
                    /* CGT Settings buttons */ 
        
                    div.theme_settings_form input[id="Export settings"],
                    div.theme_settings_form input[name="cc-config_values[import_settings]"] {
                        background-color: #F5F5F5;
                        background-image: -moz-linear-gradient(center top , #FFFFFF, #F5F5F5);
                        border: 1px solid #999999;
                        border-radius: 11px 11px 11px 11px;
                        font-size: 12px;
                        font-family: sans-serif;
                        color: #555555;
                        text-shadow: -1px 1px 0 #FFFFFF;
                        min-width: 50px;
                        padding: 2px 6px;
                        font-weight: normal;
                        cursor: pointer;
                    }
                    
                    div.theme_settings_form input[id="Export settings"]:hover,
                    div.theme_settings_form input[name="cc-config_values[import_settings]"]:hover {
                        border: 1px solid #222222;
                        color: #222222;
                    }
                    
                    /* CC Import Export Styles */
                    
                    div.cc_export_import {
                        width: auto; 
                        overflow: auto;
                        border-top: 1px solid #dddddd;
                    }
                    
                    div.cc_export_import_header h2 {
                        padding-top: 0;
                    }
                    
                    div.cc_export_import div.tk_field_row {
                        padding: 14px 24px;
                    }
                    
                    div.cc_export_import div.tk_field_label {
                        width: 80px;
                    }
                    
                    div.cc_export_import div.tk_field {
                        float: left;
                        margin-left: 1%;
                        width: 70%;
                    }
                    
                    div.cc_export_import a {
                        margin-left: 6px;
                    }
                    
                                
                    /* other Theme Settings page specific CSS */
                    
                    span.fb_edge_comment_widget {
                        display: none;
                    }
                    div.wrap div.title { 
                        float: left; 
                        width: auto; 
                        height: auto; 
                    }
                    div.headerwrap {
                        margin-right: 278px;
                    }
                    
                    div.admin_right_box {
                        width: 258px; 
                        float: right;
                        height: auto;
                        overflow: auto;
                        padding: 8px;
                    }
                    
                    div.admin_right_box p {
                        font-size: 11.7px; 
                        text-align: right;
                        margin-top: 0;
                        float: right;
                    }
                    div.admin_main_box {
                        padding: 0;
                        clear: left;
                        width: 450px;
                        height: auto;
                        margin-top: 30px;
                        overflow: auto;
                    }
                    div.theme_settings_form {
                        float: none;
                        margin-top: 20px;
                    }
                    div.admin_right_box img.admin_tk_logo {
                        height: 36px;
                        margin-bottom: 6px;
                        margin-top: 18px;
                        margin-right: 0;
                        padding-bottom: 6px;
                        border-bottom: 1px solid transparent;
                        width: 256px;
                        float: right;
                        background: url("<?php echo plugins_url(); ?>/BP-Custom-Group-Types/includes/images/themekraft-logo-s.png") no-repeat scroll 0 0 transparent;
                    }            
                    div#icon-buddypress {
                        background: url("<?php echo plugins_url(); ?>/BP-Custom-Group-Types/includes/images/icons32.png") no-repeat scroll -4px 0 transparent;
                    }
                    .icon32 {
                        float: left;
                        height: 34px;
                        margin: 7px 8px 0 0;
                        width: 36px;
                    }            
                    div.fb-like {
                        float: left; 
                    }
                    iframe.twitter-follow-button {
                        float: right;
                    }
                    div.headerlink {
                        width: auto; 
                        float: left;
                        padding: 4px 32px 4px 4px; 
                    }
                    div.headerlink p {
                        font-size: 15px; 
                        font-weight: bold; 
                        color: #888888; 
                        margin-bottom: 8px;
                    }
                    div.headerlink a {
                        font-size: 15px; 
                    }
                    div#publishing-action.cc_settings_save {
                        height: 34px;
                    }
                    div.save_it {
                        color: #888888;
                        margin-top: 4px;
                        float: left;
                    }
                    div#submitpost {
                        height: 44px;
                    }
                    div.theme_settings_form div#side-sortables {
                        padding-top: 0.5em;
                    }
                    table.global-settings-table th {
					    font-weight: bold;
					    padding-bottom: 20px;
					    padding-right: 25px;
					    text-align: left;
					}
					table.global-settings-table td.label {
						width: 90px;
					}
					td.create_cgt {
						padding-top: 20px;
					}
					.ui-accordion .ui-accordion-content div, .ui-accordion .ui-accordion-content td {
					    padding: 5px !important;
					}
					table.cgt_fields {
						background: #F5F5F5;
						margin-bottom: 20px;
						border: 1px solid #ddd; 
					}
					table.cgt_fields td, table.cgt_fields th {
					    border-bottom: 1px solid #ddd !important;
					    border-left: medium none;
					    border-right: medium none;
					    border-top: 1px solid #FFFFFF !important;
					    padding: 10px !important;
					    text-align: left;
					}
					table.cgt_fields input[type="checkbox"] {
					    margin: 2px 5px 0 0;
					    float: left;
					}
					table.cgt_fields div.label {
					    float: left;
					    min-width: 90px;
					}
					table.cgt_fields input[type="text"], 
					table.cgt_fields select {
					    width: 150px;
					}
					.cgt_tabs table input[type="text"], 
					.cgt_tabs table select {
					    width: 150px;
					}
        
                </style>
                
                        
                <div class="admin_main_box">          
                    <div class="getting_started headerlink">
                        <a href="#" target="_blank">Getting started</a>
                    </div>
        
                    <div class="documentation headerlink">
                        <a href="#" target="_blank">Documentation</a>
                    </div>
        
                    <div class="support headerlink"> 
                        <a href="http://themekraft.com/shop/premium-support/" title="Get Premium Support" target="_blank">Premium support</a>
                    </div>
                </div>
            </div>
        
        
        
       <div class="theme_settings_form">
       
       <div id="poststuff" class="metabox-holder has-right-sidebar">           
            
            <div id="side-info-column" class="inner-sidebar">
                

                
                <div id="side-sortables" class="meta-box-sortables ui-sortable">
                
                    <div id="submitdiv" class="postbox">
                        <div title="Click to toggle" class="handlediv"><br></br></div><h3 class="hndle"><span>Save</span></h3>
                        <div class="inside">
                            <div id="submitpost" class="submitbox">
                                
                                <div id="major-publishing-actions">
                                    <div class="save_it">Save CGT settings</div>
                                    <div id="publishing-action" class="cc_settings_save">
                                        <input type="submit" id="Save" value="Save" class="button-primary" />
                                    </div>
                                </div>

                            </div>  
                        </div>
                    </div>
            
   
                 <div id="cc_admin_navigation" class="postbox">
                        <div title="Click to toggle" class="handlediv"><br></br></div><h3 class="hndle"><span>Standard Fields</span></h3>
                        
                        <div class="inside">
                            <div id="cc_admin_navigation_box" class="submitbox">

                                <ul></ul>
  
                            </div>  
                        </div>
                        
                    </div>    
   
                   <div id="cc_admin_navigation" class="postbox">
                        <div title="Click to toggle" class="handlediv"><br></br></div><h3 class="hndle"><span>Post Fields</span></h3>
                        
                        <div class="inside">
                            <div id="cc_admin_navigation_box" class="submitbox">

                                <ul>
                               <li>etwas</li>
                       
                                </ul>
                       
  
                            </div>  
                        </div>
                        
                    </div>    
                   
            
                    <div id="cc_admin_navigation" class="postbox">
                        <div title="Click to toggle" class="handlediv"><br></br></div><h3 class="hndle"><span>Navigator</span></h3>
                        
                        <div class="inside">
                            <div id="cc_admin_navigation_box" class="submitbox">
                                
                                <b>CGT Settings</b>
                                <ul>
                                    <li>
                                        <a href="#">CGT Settings Home</a>
                                    </li>
                                </ul>

                                
                                <b>Help</b>
                                <ul>
                                    <li>
                                        <a href="#">Getting Started</a>
                                    </li>

                                    <li>
                                        <a href="#">Knowledge Base</a>
                                    </li>
                                    <li>
                                        <a href="#">Get Premium Support</a>
                                    </li>

                                </ul>
  
                            </div>  
                        </div>
                        
                    </div>                  
                </div>
            </div> 

            <div id="post-body">
                <div id="post-body-content">            
                <?php
                // Creating the tabs
                tk_tabs( 'cgt_tabs', $TapArray, 'echo' );
                ?>    
              </div>
           </div>
       </div>
    </div>  
    </div>
<?php

    $inhalte = ob_get_contents();
    ob_end_clean();
    return $inhalte;
 } ?>