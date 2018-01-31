<?php

//
// This is a modified version of the pippinsplugins post change password form short code.
// Link: https://pippinsplugins.com/change-password-form-short-code/
//

function buddyforms_change_password_form($redirect_url = '') {
	global $post;

	if (is_singular()) :
		$current_url = get_permalink($post->ID);
	else :
		$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on") $pageURL .= "s";
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		else $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		$current_url = $pageURL;
	endif;
	$redirect = $current_url;

    if( ! empty( $redirect_url ) ){
	    $redirect = $redirect_url;
    }

    ob_start();

	//
	// show any error messages after form submission
    //

	buddyforms_show_error_messages(); ?>

	<?php if(isset($_GET['password-reset']) && $_GET['password-reset'] == 'true') { ?>
		<div class="buddyforms_message success">
			<span><?php _e('Password changed successfully', 'buddyforms'); ?></span>
		</div>
	<?php } ?>

	<form id="buddyforms_password_form" method="POST" action="<?php echo $current_url; ?>">
		<fieldset>
			<p>
				<label for="buddyforms_user_pass"><?php _e('New Password', 'buddyforms'); ?></label>
				<input name="buddyforms_user_pass" id="buddyforms_user_pass" class="required" type="password"/>
			</p>
			<p>
				<label for="buddyforms_user_pass_confirm"><?php _e('Password Confirm', 'buddyforms'); ?></label>
				<input name="buddyforms_user_pass_confirm" id="buddyforms_user_pass_confirm" class="required" type="password"/>
			</p>
			<p>
				<input type="hidden" name="buddyforms_action" value="reset-password"/>
				<input type="hidden" name="buddyforms_redirect" value="<?php echo $redirect; ?>"/>
				<input type="hidden" name="buddyforms_password_nonce" value="<?php echo wp_create_nonce('buddyforms-password-nonce'); ?>"/>



				<div><span id="password-strength"></span></div>
				<input id="buddyforms_password_submit" disabled="disabled" type="submit" value="<?php _e('Change Password', 'buddyforms'); ?>"/>

			</p>
		</fieldset>
	</form>
	<?php
	return ob_get_clean();
}

function buddyforms_reset_password() {
	// reset a users password
	if(isset($_POST['buddyforms_action']) && $_POST['buddyforms_action'] == 'reset-password') {

		global $user_ID;

		if(!is_user_logged_in())
			return;

		if(wp_verify_nonce($_POST['buddyforms_password_nonce'], 'buddyforms-password-nonce')) {

			if($_POST['buddyforms_user_pass'] == '' || $_POST['buddyforms_user_pass_confirm'] == '') {
				// password(s) field empty
				buddyforms_reset_password_errors()->add('password_empty', __('Please enter a password, and confirm it', 'buddyforms'));
			}
			if($_POST['buddyforms_user_pass'] != $_POST['buddyforms_user_pass_confirm']) {
				// passwords do not match
				buddyforms_reset_password_errors()->add('password_mismatch', __('Passwords do not match', 'buddyforms'));
			}

			// retrieve all error messages, if any
			$errors = buddyforms_reset_password_errors()->get_error_messages();

			if(empty($errors)) {
				// change the password here
				$user_data = array(
					'ID' => $user_ID,
					'user_pass' => $_POST['buddyforms_user_pass']
				);
				wp_update_user($user_data);
				// send password change email here (if WP doesn't)



                $redirect_url = apply_filters( 'buddyforms_reset_password_redirect', $_POST['buddyforms_redirect'] );

				$bf_pw_redirect_url = get_user_meta( $user_ID,'bf_pw_redirect_url', true );

				if($bf_pw_redirect_url){
					$redirect_url = $bf_pw_redirect_url;
					delete_user_meta( $user_ID, 'bf_pw_redirect_url' );
				}


				wp_redirect(add_query_arg('password-reset', 'true', $redirect_url));
				exit;
			}
		}
	}
}
add_action('init', 'buddyforms_reset_password');

if(!function_exists('buddyforms_show_error_messages')) {
	// displays error messages from form submissions
	function buddyforms_show_error_messages() {
		if($codes = buddyforms_reset_password_errors()->get_error_codes()) {
			echo '<div class="buddyforms_message error">';
			// Loop error codes and display errors
			foreach($codes as $code){
				$message = buddyforms_reset_password_errors()->get_error_message($code);
				echo '<span class="buddyforms_error"><strong>' . __('Error', 'buddyforms') . '</strong>: ' . $message . '</span><br/>';
			}
			echo '</div>';
		}
	}
}

if(!function_exists('buddyforms_reset_password_errors')) {
	// used for tracking error messages
	function buddyforms_reset_password_errors(){
		static $wp_error; // Will hold global variable safely
		return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
	}
}