<?php 
/*
Plugin Name: Themekraft framework
Plugin URI: http://themekraft.com/plugin/tk-framework
Description: Speed up your wordpress developement with our framework
Author: Sven Wagener, Sven Lehnert
Author URI: http://themekraft.com/
License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
Version: 0.1.1
Text Domain: tkframework
Site Wide Only: false
*/

// Schau mal was hier steht Ja was soll das?  SO! kjhg

function framework_init(){
 // Registering the form where the data have to be saved
 $args['forms'] = array( 'myform' );
 $args['text_domain'] = 'my_text_domain';
 
 require_once( 'loader.php' );
 
 tk_framework( $args );
}
add_action( 'plugins_loaded', 'framework_init' );
 
function init_backend(){
 /*
  * WML
  */
	
 /*
  * Hiding elemts by id 
  */
 tk_hide_element( 's1' );
 tk_hide_element( 'o3' );
 
 tk_autocomplete_add_value( 'city', 'Dusseldorf' );
 tk_autocomplete_delete_value( 'city', 'New York' );
 
 add_filter( 'tk_fileupload_tempfile', 'tkf_fileactions', 1, 2 );
 
 /*
  * Example with WML file
  */
 
 // Example for loading xml file
 tk_wml_parse_file( dirname( __FILE__ ) . '/example.xml' );
 
 
 /*
  * Getting back values from form fields
  */
 $values = tk_get_values( 'myform' );
 
}
add_action( 'admin_menu', 'init_backend' );

function tkf_test( $html ){
	$my_html = "<br /><b>Hallo</b>";
	
	return $html . $my_html;
}
add_filter( 'tk_form_before_content_myform', 'tkf_test' );

function tkf_fileactions( $file, $input ){
	/*
	echo '<pre>';
	print_r( $file );
	echo '</pre>';
	*/
	return $file;
	
}

