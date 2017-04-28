WordPress TinyMCE Placeholder Plugin
====================================

 - Last Updated: March 23, 2016
 - Author: Myles McNamara
 - Original Author: mohan999
 - Original Source: https://github.com/mohan999/tinymce-placeholder

### Preview ###
![Preview](https://github.com/tripflex/wp-tinymce-placeholder/raw/master/placeholder.gif)

This plugin brings HTML5 placeholder attribute functionality for the TinyMCE editor in WordPress.  The standard placeholder in the textarea will show when viewing in text mode, this plugin adds a `<label>` html element that is only shown when viewing the Visual editor.

# Usage

 - Add mce.placeholder.js to your site
 - Add to list of WordPress TinyMCE Plugins through filter
 - Set placeholder value of textarea through filter
 - Profit!

## Add mce.placeholder.js
Download the mce.placeholder.js file and place it in your theme/plugin directory, or somewhere you know the full URL to.

## Add placeholder plugin through filter


```php
add_filter( 'mce_external_plugins', 'add_mce_placeholder_plugin' );

function add_mce_placeholder_plugin( $plugins ){

	// Optional, check for specific post type to add this
	// if( 'my_custom_post_type' !== get_post_type() ) return $plugins;

	$plugins[ 'placeholder' ] = '//domain.com/full/path/to/mce.placeholder.js';

	return $plugins;
}
```

## Set placeholder value in textarea


```php
add_filter( 'the_editor', array( $this, 'set_my_mce_editor_placeholder' ) );

function set_my_mce_editor_placeholder( $textarea_html ){

	// Optional, check for specific post type to add this (remove // to uncomment and use)
	// if( 'my_custom_post_type' !== get_post_type() ) return $plugins;

	$placeholder = __( 'Check it out...this is my custom placeholder!' );

	$textarea_html = preg_replace( '/<textarea/', "<textarea placeholder=\"{$placeholder}\"", $textarea_html );

	return $textarea_html;
}
```

# Profit!
