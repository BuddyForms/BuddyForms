<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function buddyforms_marketing_meet_condition() {
	$meet = false;


	return $meet;
}

add_action( 'admin_init', 'buddyforms_marketing_init' );

function buddyforms_marketing_init() {
//	add_action( 'admin_enqueue_scripts', 'buddyforms_marketing_assets' );
	add_action( 'admin_enqueue_scripts', 'buddyforms_marketing_offer_bundle', 10, 1 );
	add_action( 'admin_enqueue_scripts', 'buddyforms_marketing_form_list_coupon_for_free', 10, 1 );
	add_action( 'wp_ajax_buddyforms_marketing_form_list_coupon_for_free_close', 'buddyforms_marketing_form_list_coupon_for_free_close' );
	add_action( 'wp_ajax_buddyforms_marketing_reset_permissions', 'buddyforms_marketing_reset_permissions' );
}

function buddyforms_marketing_offer_bundle( $hook ) {
	if ( $hook !== 'buddyforms_page_buddyforms-addons' ) {
		return;
	}
	$user_id = get_current_user_id();
	if ( empty( $user_id ) || ! is_user_logged_in() ) {
		return;
	}
	if ( ! current_user_can( 'administrator' ) ) {
		return;
	}
	$base_content = "<p class=\"corner-head\">By ALL by once</p><p class=\"corner-text\">%content</p><div class=\"bf-marketing-action-container\"><a target='_blank' href=\"https://checkout.freemius.com/mode/dialog/bundle/2046/plan/4316?utm=buddyform-plugin\" class=\"bf-marketing-btn corner-btn-close\">%cta</a></div>";
	$content      = array( '%content' => 'Get the result that you expect to provide to your final customers earning all these Add-ons with the ThemeKraft Bundle.', '%cta' => 'Get the OFFER' );
	buddyforms_marketing_include_assets( $content, $base_content );
}

function buddyforms_marketing_form_list_coupon_for_free_close() {
	try {
		if ( ! ( is_array( $_POST ) && defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			die();
		}
		if ( ! isset( $_POST['action'] ) || ! isset( $_POST['nonce'] ) ) {
			die();
		}
		if ( ! wp_verify_nonce( $_POST['nonce'], 'fac_drop' ) ) {
			die();
		}

		update_option( 'buddyforms_marketing_form_list_coupon_for_free_close', true );

		wp_send_json( '' );
	} catch ( Exception $ex ) {
		BuddyFormsContactAuthor::error_log( $ex->getMessage() );
	}
	die();
}

function buddyforms_marketing_reset_permissions() {
	try {
		if ( ! ( is_array( $_POST ) && defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			die();
		}
		if ( ! isset( $_POST['action'] ) || ! isset( $_POST['nonce'] ) ) {
			die();
		}
		if ( ! wp_verify_nonce( $_POST['nonce'], 'fac_drop' ) ) {
			die();
		}

		$result = delete_option( 'buddyforms_marketing_form_list_coupon_for_free_close' );

		wp_send_json( $result );
	} catch ( Exception $ex ) {
		BuddyFormsContactAuthor::error_log( $ex->getMessage() );
	}
	die();
}

function buddyforms_marketing_form_list_coupon_for_free( $hook ) {
	if ( $hook !== 'edit.php' ) {
		return;
	}
	if ( ! current_user_can( 'administrator' ) ) {
		return;
	}
	$freeemius = buddyforms_core_fs();
	if ( empty( $freeemius ) ) {
		return;
	}
	$is_able_to_open = get_option( 'buddyforms_marketing_form_list_coupon_for_free_close' );
	$is_free         = $freeemius->is_free_plan();
	$is_trial        = $freeemius->is_trial();
	$current_screen  = get_current_screen();
	if ( ! empty( $current_screen ) && $current_screen->id === 'edit-buddyforms' && ( $is_free || $is_trial ) && empty( $is_able_to_open ) ) {
		$base_content = "<p class=\"corner-head\">30% off .:. 50% off</p><p class=\"corner-text\">%content</p><div class=\"bf-marketing-action-container\"><a target='_blank' href=\"https://themekraft.com/price-off-in-5-minutos?utm=buddyform-plugin\" class=\"bf-marketing-btn corner-btn-close\">%cta</a></div>";
		$content      = array( '%content' => 'UNLOCK the complete POWER of this tools to provide better solutions to your clients.', '%cta' => 'Let\'s do it' );
		buddyforms_marketing_include_assets( $content, $base_content );
	}

}

function buddyforms_marketing_include_assets( $content, $base_content ) {
	wp_enqueue_style( 'buddyforms-marketing-popup', BUDDYFORMS_ASSETS . 'resources/corner-popup/css/corner-popup.min.css', array(), BUDDYFORMS_VERSION );
	wp_enqueue_script( 'buddyforms-marketing-popup', BUDDYFORMS_ASSETS . 'resources/corner-popup/js/corner-popup.min.js', array( 'jquery' ), BUDDYFORMS_VERSION );
	wp_enqueue_script( 'buddyforms-marketing-popup-handler', BUDDYFORMS_ASSETS . 'admin/js/admin-marketing.js', array( 'jquery' ), BUDDYFORMS_VERSION );
	wp_localize_script( 'buddyforms-marketing-popup-handler', 'buddyformsMarketingHandler', array(
		'content' => str_replace( array_keys( $content ), array_values( $content ), $base_content ),
	) );
}

function buddyforms_marketing_assets() {
	try {
		$user_id = get_current_user_id();
		if ( empty( $user_id ) || ! is_user_logged_in() ) {
			return;
		}
		if ( ! current_user_can( 'administrator' ) ) {
			return;
		}

		$base_content = "<p class=\"corner-head\">This is only for you</p><p class=\"corner-text\">%content</p><div class=\"bf-marketing-action-container\"><a href=\"#\" class=\"bf-marketing-btn corner-btn-close\">%no</a><a href=\"#\" class=\"bf-marketing-btn corner-btn-close\">%yes</a></div>";
		$content      = buddyforms_marketing_content_interest();
		$is_free      = false;
		$is_pro       = false;
		$is_trial     = false;

		$first_question = get_user_meta( $user_id, '_buddyforms_marketing_question1', true );
		$start_date     = get_user_meta( $user_id, '_buddyforms_marketing_start_date', true );

		$freeemius = buddyforms_core_fs();
		if ( empty( $freeemius ) ) {
			return;
		}

		$is_free     = $freeemius->is_free_plan();
		$is_pro      = $freeemius->is_premium();
		$is_trial    = $freeemius->is_trial();
		$upgrade_url = $freeemius->get_upgrade_url();
		$plugin      = $freeemius->get_plugin();

		if ( $freeemius->has_active_valid_license() ) {
			$license = $freeemius->_get_license();
			if ( ! empty( $license ) ) {
				$created_date = new DateTime( $license->created );
				//Add an interval to define when to show the modal
				//Create something generic to handle this generic process like condition meet and then do X
				//Like a class system where we can override generic behavior using heritage
			}
		}

		if ( $is_free ) {
			$content = buddyforms_marketing_content_pro_coupon();
		} else if ( $is_trial ) {
			$content = buddyforms_marketing_content_pro_coupon();
		} else if ( $is_pro ) {

		}


		wp_enqueue_style( 'buddyforms-marketing-popup', BUDDYFORMS_ASSETS . 'resources/corner-popup/css/corner-popup.min.css', array(), BUDDYFORMS_VERSION );
		wp_enqueue_script( 'buddyforms-marketing-popup', BUDDYFORMS_ASSETS . 'resources/corner-popup/js/corner-popup.min.js', array( 'jquery' ), BUDDYFORMS_VERSION );
		wp_enqueue_script( 'buddyforms-marketing-popup-handler', BUDDYFORMS_ASSETS . 'admin/js/admin-marketing.js', array( 'jquery' ), BUDDYFORMS_VERSION );
		wp_localize_script( 'buddyforms-marketing-popup-handler', 'buddyformsMarketingHandler', array(
			'content' => str_replace( array_keys( $content ), array_values( $content ), $base_content ),
		) );
	} catch ( Exception $ex ) {

	}
}

/**
 * Popup content asking to receive the offer
 */
function buddyforms_marketing_content_interest() {
	return array( '%content' => 'Are you interest in get a personal offer', '%no' => 'No Thanks', '%yes' => 'Yes' );
}

/**
 * Popup content to offer a discount coupon
 */
function buddyforms_marketing_content_pro_coupon() {
	return array( '%content' => 'Get your discount coupon', '%no' => 'No thanks', '%yes' => 'Yes please' );
}

/**
 * Popup content to upgrade from month to yearly
 */
function buddyforms_marketing_content_yearly_coupon() {
	return array( '%content' => 'Upgrade to yearly with a special copuon', '%no' => 'No thanks', '%yes' => 'Go for it' );
}

/**
 * Popup content to increase site quote
 */
function buddyforms_marketing_content_site_quote() {
	return array( '%content' => 'Be ready to install this amazing tool in more sites', '%no' => 'No thanks', '%yes' => 'Yes please' );
}