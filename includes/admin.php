<?php

/**
 * Create "CGT Options" sub nav menu under the Buddypress main admin nav
 *
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta
 */
add_action('admin_menu', 'cgt_create_menu');
function cgt_create_menu() {

    if ( !is_super_admin() )
        return false;
    
    $args = array(
            'id' => 'cgt_options',
            'menu_title' => 'CGT Options',
            'page_title' => 'CGT Options',
            'capability' => 'edit_posts',
            'parent_slug' => '',
            'menu_slug' => 'cgt_options_page',          
            'icon_url' => '',
            'position' => '',
            'object_menu' => TRUE   
        );
        
    $element[0] = array( 
            'id' => 'afsd',
            'menu_title' => 'Pagessss',
            'page_title' => 'page_title',
            'content' => tk_form( 'cgt-config', 'cgt-config', cgt_settings_page()),
        );
        
      
    tk_admin_pages($element, $args, false);  
}  

/**
 * Create the option settings page
 *
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta
 */
function cgt_settings_page() {
    global $bp, $cgt;
    $cgt = tk_get_values( 'cgt-config' );

    // Create the Array for the jQuery Taps 
    $TapArray = array();
    
   // Get all post types
    $args=array(
      '_builtin' => false
    ); 
    $output = 'names'; // names or objects, note names is the default
    $operator = 'and'; // 'and' or 'or'
    $post_types=get_post_types($args,$output,$operator); 
    
    
        $tab1 .= '<table class="global-settings-table">';

            $tab1 .= '<tr valign="top">';   
            $tab1 .= '<th scope="row">Select Existing Posts Type to use as Custom Group Typeps.</th>';
            $tab1 .= '<th scope="row">Create new post types to use as Custom Group Typeps.</th>';
            $tab1 .= '</tr>';
            
            $tab1 .= '<tr><td style="vertical-align: top;">';
            
            foreach ($post_types  as $post_type ) {
                    
                $checked = false;

                if( $post_type == $cgt->cgt_existing_types)
                    $checked = true;
                 
                  $cgt_existing = array(
                    'id' => '',
                    'name' => 'cgt-config_values[cgt_existing_types]',
                    'value' => $post_type,
                    'checked' => $checked,
                    'extra' => '',
                    'before_element' => '',
                    'after_element' => ''
                );
        
                $tab1 .=  tk_checkbox($cgt_existing) . ' ' . $post_type . '<br>' ;
        
            }

            $tab1 .= '</td><td>';
          
         foreach($cgt->cgt_create_field_name as $key => $value){
              $cgt_create_field_name[$value] = $key;
          }
          
            $md5id =  substr(md5( rand() ), 0, 15);
          
            $new_cgt_form .= '<table>';
            $new_cgt_form .= '<tr>';
               $new_cgt_form .= ' <td>Name: </td>';
               $new_cgt_form .= ' <td> ' . tk_textfield(Array('multi_index' => 0, 'id' => 'cgt_create_field_name','name' => 'cgt-config_values[cgt_create_field_name]['.$md5id.']')) . ' </td>';
             $new_cgt_form .= '</tr>';
             $new_cgt_form .= '<tr>';
               $new_cgt_form .= ' <td>Singular Name: </td>';
               $new_cgt_form .= ' <td> ' . tk_textfield(Array('multi_index' => 0, 'id' => 'cgt_create_field_singular_name','name' => 'cgt-config_values[cgt_create_field_singular_name]['.$md5id.']')). ' </td>';
             $new_cgt_form .= '</tr>';
             $new_cgt_form .= '<tr><td>' . tk_button('Create new CGT', 'cgt_create_new_form_submit') . '</td><tr>';
            $new_cgt_form .= '</table>';
   
            $tab1 .= $new_cgt_form;
            
            $tab1 .= '</td></tr>';
        
        $tab1 .= '</table>';
       
        $TapArray[] = array(
                'id' => 1,
                'title' => 'General Settings',
                'content' => $tab1  
                );
                
        if(!empty($cgt->cgt_create_field_name)){
            foreach($cgt->cgt_create_field_name as $new_group_type ) :
                if($new_group_type != '') {
                    
                $new_group_type_id = str_replace(' ', '', $new_group_type);
                
                $accordion_lable .= '<div>';
                $accordion_lable .= '<div>Lable for ' .$new_group_type. ' display name: </div>';
              $accordion_lable .= '<table>';
            $accordion_lable .= '<tr>';
               $accordion_lable .= ' <td>Name: </td>';
               $accordion_lable .= ' <td> ' . tk_textfield(Array('id' => 'cgt_create_field_name','name' => 'cgt-config_values[cgt_create_field_name]['.$cgt_create_field_name[$new_group_type].']', 'value' => $cgt->cgt_create_field_name[$cgt_create_field_name[$new_group_type]])) . ' </td>';
             $accordion_lable .= '</tr>';
             $accordion_lable .= '<tr>';
               $accordion_lable .= ' <td>Singular Name: </td>';
               $accordion_lable .= ' <td> ' . tk_textfield(Array('id' => 'cgt_create_field_singular_name','name' => 'cgt-config_values[cgt_create_field_singular_name]['.$cgt_create_field_name[$new_group_type].']', 'value' => $cgt->cgt_create_field_singular_name[$cgt_create_field_name[$new_group_type]])) . ' </td>';
             $accordion_lable .= '</tr>';
             $accordion_lable .= '</table>';
   
                $accordion_lable .= '</div>';
                
                $accordion_custom_fields .= '<table id="table-5" border="1" bordercolor="#ECECEC" style="background-color:#ECECEC" width="100%" cellpadding="0" cellspacing="0">';
                $accordion_custom_fields .= '<tbody>';
                $accordion_custom_fields .= '<tr class="nodrop nodrag">';
                    $accordion_custom_fields .= '<th>Position</th>';
                    $accordion_custom_fields .= '<th>Field Name</th>';
                    $accordion_custom_fields .= '<th>form</th>';
                    $accordion_custom_fields .= '<th>multi select</th>';
                    $accordion_custom_fields .= '<th>required</th>';
                    $accordion_custom_fields .= '<th>display</th>';
                $accordion_custom_fields .= '</tr>';
   

   
   
                $field_i = 0;
                if(!empty($cgt->cgt_custom_field[$cgt_create_field_name[$new_group_type]])){
                    foreach($cgt->cgt_custom_field[$cgt_create_field_name[$new_group_type]] as $custom_field ) {
                          $accordion_custom_fields .= '<tr id="table5-row-'.$field_i.'">';
                          $accordion_custom_fields .= '<td class="dragHandle">'.$field_i.'</td>';
                          
                          $new_field_type = new tk_form_select( array('value' => $custom_field, 'name' => 'cgt-config_values[cgt_custom_field]['.$cgt_create_field_name[$new_group_type].']['.$field_i.'] ', 'id' => 'cgt_custom_field'));
               
                          $new_field_type->add_option('-');
                          $new_field_type->add_option('Mail');
                          $new_field_type->add_option('Radiobutton');
                          $new_field_type->add_option('Checkbox');
                          $new_field_type->add_option('Dropdown');
                          $new_field_type->add_option('Textarea');
                          $new_field_type->add_option('Text');
                          $new_field_type->add_option('select');
               
                          
                          $accordion_custom_fields .= '<td>' . $new_field_type->get_html().'<input type="text" name="cgt_custom_fields['.$new_group_type.'][]" value="'.$custom_field.'" /></td>';
                         //$accordion_custom_fields .= ' <td> ' . $new_field_type->get_html() . tk_textfield(Array('id' => 'cgt_custom_fields_name','name' => 'cgt-config_values[cgt_custom_fields_name]['.$cgt_create_field_name[$new_group_type].']', 'value' => $cgt->cgt_create_field_singular_name[$cgt_create_field_name[$new_group_type]])) . ' </td>';
          
                          $accordion_custom_fields .= '<td>' . tk_checkbox($taxonomy_form) . '</td>';
                          $accordion_custom_fields .= '<td>' . tk_checkbox($taxonomy_multy_select) . '</td>';
                          $accordion_custom_fields .= ' <td>' . tk_checkbox($taxonomy_required) . '</td>';
                          $accordion_custom_fields .= ' <td>' . tk_checkbox($taxonomy_display) . '</td>';
                          $accordion_custom_fields .= '</tr>';
                          $field_i ++;
                    }
                }
                $accordion_custom_fields .= ' </tbody></table>';    
               
                $count2 = count($cgt->cgt_custom_field[$cgt_create_field_name[$new_group_type]]) + 1;
                

               $new_field_type = new tk_form_select( array('name' => 'cgt-config_values[cgt_custom_field]['.$cgt_create_field_name[$new_group_type].']['.$count2.'] ', 'id' => 'cgt_custom_field'));
               $new_field_type->add_option('-');
               $new_field_type->add_option('Mail');
               $new_field_type->add_option('Radiobutton');
               $new_field_type->add_option('Checkbox');
               $new_field_type->add_option('Dropdown');
               $new_field_type->add_option('Textarea');
               $new_field_type->add_option('Text');
               $new_field_type->add_option('select');
               $accordion_custom_fields .=  $new_field_type->get_html();
               
                
                $accordion_custom_fields .= '<tr><td>' . tk_button('Add one more', 'cgt_add_form_element_submit') . '</td><tr>';
                            
                
            
                
                  $accordion_taxonomies .= '<table border="1" bordercolor="#ECECEC" style="background-color:#ECECEC" width="100%" cellpadding="0" cellspacing="0">';
                  $accordion_taxonomies .= '<tbody>';
                   $accordion_taxonomies .= '<tr>';
                      $accordion_taxonomies .= '<th>Name</th>';
                      $accordion_taxonomies .= '<th>form</th>';
                      $accordion_taxonomies .= '<th>multi select</th>';
                      $accordion_taxonomies .= '<th>required</th>';
                      $accordion_taxonomies .= '<th>display</th>';
                   $accordion_taxonomies .= '</tr>';
                   
                   $args=array(
                      'object_type' => array($new_group_type),
                      'public'   => true,
                      '_builtin' => false
                      
                    ); 
                    $output = 'names'; // or objects
                    $operator = 'and'; // 'and' or 'or'
                    $taxonomies=get_taxonomies($args,$output,$operator); 
                              
                    if  ($taxonomies) {
                      foreach ($taxonomies  as $taxonomy ) {
                          
                        $accordion_taxonomies .= '<tr>';
                          $accordion_taxonomies .= '<td>' . $taxonomy . '</td>';
                          $accordion_taxonomies .= '<td>' . tk_checkbox($taxonomy_form) . '</td>';
                          $accordion_taxonomies .= '<td>' . tk_checkbox($taxonomy_multy_select) . '</td>';
                          $accordion_taxonomies .= '<td>' . tk_checkbox($taxonomy_required) . '</td>';
                          $accordion_taxonomies .= '<td>' . tk_checkbox($taxonomy_display) . '</td>';
                        $accordion_taxonomies .= '</tr>';
                       // $cgt_post_type_taxonomies_options[] = $taxonomy;
                      }
                    }
                    
                    $accordion_taxonomies .= '  </tbody></table>';
                        
                    //$cgt_post_type_taxonomies = tk_form_select( 'cgt_post_type_taxonomies_'.$new_group_type_id, $cgt_post_type_taxonomies_options, array(  'multiselect' => true ) );
                        
                    $accordion_Array[] = array(
                            'id' => 'accordion_lable_'.$new_group_type_id,
                            'title' => $new_group_type.' Lable',
                            'content' => $accordion_lable
                            );
                    // $accordion_Array[] = array(
                            // 'id' => 'accordion_taxonomies_'.$new_group_type_id,
                            // 'title' => $new_group_type. ' taxonomies',
                            // 'content' => $accordion_taxonomies
                            // );  
                     // $accordion_Array[] = array(
                            // 'id' => 'accordion_custom_fields_'.$new_group_type_id,
                            // 'title' => $new_group_type. ' custom Fields',
                            // 'content' => $accordion_custom_fields
                            // );
                            
     
                       
                    $tabs = tk_accordion('cgt_accordion_'.$new_group_type_id , $accordion_Array );
                      
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
                        padding: 4px 22px; 
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
                                <?php
                                $args=array(
                                 'public'   => true,
                                  '_builtin' => false
                                  
                                ); 
                                $output = 'names'; // or objects
                                $operator = 'and'; // 'and' or 'or'
                                $taxonomies=get_taxonomies($args,$output,$operator); 
                                          
                                if  ($taxonomies) {
                                  foreach ($taxonomies  as $taxonomy ) {
                                   //   echo '<li>' . $taxonomy . '</li>';
                                  }
                                }
                                ?>
                       
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
                
                $cgt_tabs = tk_tabs( 'cgt_tabs', $TapArray );
                
                //echo tk_form( 'cgt_create_new_form', 'cgt_create_new_form', $cgt_tabs );
                echo $cgt_tabs;
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