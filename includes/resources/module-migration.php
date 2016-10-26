<?php
if ( ! defined( 'TK__EDD_STORE_URL' ) ) {
	// This should point to your EDD install.
	define( 'TK__EDD_STORE_URL', 'https://themekraft.com' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file
}

// The EDD download ID of your product.
define( 'TK__EDD_DOWNLOAD_ID', '12867' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

/**
 * The license migration script.
 *
 * IMPORTANT:
 *  You should use your own function name, and be sure to replace it throughout this file.
 *
 * @author   Vova Feldman (@svovaf)
 * @since    1.0.0
 *
 * @param int    $edd_download_id The context EDD download ID (from your store).
 * @param string $edd_license_key The current site's EDD license key.
 * @param string $edd_store_url   Your EDD store URL.
 * @param bool   $redirect
 *
 * @return bool
 */
function do_buddyforms_edd2fs_license_migration(
	$edd_download_id,
	$edd_license_key,
	$edd_store_url,
	$redirect = false
) {
	/**
	 * @var \Freemius $fs
	 */
	$fs = buddyforms_core_fs();

	$install_details = $fs->get_opt_in_params();

	// Override is_premium flat because it's a paid license migration.
	$install_details['is_premium'] = true;
	// The plugin is active for sure and not uninstalled.
	$install_details['is_active']      = true;
	$install_details['is_uninstalled'] = false;

	// Clean unnecessary arguments.
	unset( $install_details['return_url'] );
	unset( $install_details['account_url'] );


	// Call the custom license and account migration endpoint.
	$transient_key = 'fs_license_migration_' . $edd_download_id . '_' . md5( $edd_license_key );
	$response      = get_transient( $transient_key );

	if ( false === $response ) {
		$response = wp_remote_post(
			$edd_store_url . '/fs-api/edd/migrate-license.json',
			array_merge( $install_details, array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => json_encode( array_merge( $install_details, array(
					'module_id'   => $edd_download_id,
					'license_key' => $edd_license_key,
					'url'         => home_url()
				) ) ),
			) )
		);

		// Cache result (5-min).
		set_transient( $transient_key, $response, 5 * MINUTE_IN_SECONDS );
	}

	// make sure the response came back okay
	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		$error_message = $response->get_error_message();

		return ( is_wp_error( $response ) && ! empty( $error_message ) ) ?
			$error_message :
			__( 'An error occurred, please try again.' );

	} else {
		$response = json_decode( wp_remote_retrieve_body( $response ) );

		if ( ! is_object( $response ) ||
		     ! isset( $response->success ) ||
		     true !== $response->success
		) {
			if ( isset( $response->error ) ) {
				switch ( $response->error->code ) {
					case 'invalid_license_key':
						// Invalid license key.
						break;
					case 'invalid_download_id':
						// Invalid download ID.
						break;
					default:
						// Unexpected error.
						break;
				}
			} else {
				// Unexpected error.
			}

			// Failed to pull account information.
			return false;
		}

		// Delete transient on successful migration.
		delete_transient( $transient_key );

		$fs->setup_account(
			new FS_User( $response->data->user ),
			new FS_Site( $response->data->install ),
			$redirect
		);

		return true;
	}
}

/**
 * Initiate a non-blocking HTTP POST request to the same URL
 * as the current page, with the addition of "fsm_edd_{$edd_download_id}"
 * param in the query string that is set to a unique migration
 * request identifier, making sure only one request will make
 * the migration.
 *
 * @todo     Test 2 threads in parallel and make sure that `fs_add_transient()` works as expected.
 *
 * @author   Vova Feldman (@svovaf)
 * @since    1.0.0
 *
 * @param int $edd_download_id The context EDD download ID (from your store).
 *
 * @return bool Is successfully spawned the migration request.
 */
function spawn_buddyforms_edd2fs_license_migration( $edd_download_id ) {
	global $wp;

	#region Make sure only one request handles the migration (prevent race condition)

	// Generate unique md5.
	$migration_uid = md5( rand() . microtime() );

	$loaded_migration_uid = false;

	/**
	 * Use `fs_add_transient()` instead of `set_transient()` because
	 * we only want that one request will succeed writing this
	 * option to the storage.
	 */
	if ( fs_add_transient( 'fsm_edd_' . $edd_download_id, $migration_uid ) ) {
		$loaded_migration_uid = fs_get_transient( 'fsm_edd_' . $edd_download_id );
	}

	if ( $migration_uid !== $loaded_migration_uid ) {
		return false;
	}

	#endregion

	$current_url   = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
	$migration_url = add_query_arg(
		'fsm_edd_' . $edd_download_id,
		$migration_uid,
		$current_url
	);

	wp_remote_post(
		$migration_url,
		array(
			'timeout'   => 0.01,
			'blocking'  => false,
			'sslverify' => false,
		)
	);

	return true;
}

/**
 * Run non blocking migration if all of the following (AND condition):
 *  1. Has API connectivity to api.freemius.com
 *  2. User isn't yet identified with Freemius.
 *  3. Freemius is in "activation mode".
 *  4. It's a plugin version upgrade.
 *  5. It's the first installation of the context plugin that have Freemius integrated with.
 *
 * @author   Vova Feldman (@svovaf)
 * @since    1.0.0
 *
 * @param int    $edd_download_id The context EDD download ID (from your store).
 * @param string $edd_license_key The current site's EDD license key.
 * @param string $edd_store_url   Your EDD store URL.
 * @param bool   $is_blocking     Special argument for testing. When false, will initiate the migration in the same
 *                                HTTP request.
 *
 * @return string|bool
 */
function buddyforms_non_blocking_edd2fs_license_migration(
	$edd_download_id,
	$edd_license_key,
	$edd_store_url,
	$is_blocking = false
) {
	/**
	 * @var \Freemius $fs
	 */
	$fs = buddyforms_core_fs();

	if ( ! $fs->has_api_connectivity() ) {
		// No connectivity to Freemius API, it's up to you what to do.
		return 'no_connectivity';
	}

	if ( $fs->is_registered() ) {
		// User already identified by the API.
		return 'user_registered';
	}

	if ( ! $fs->is_activation_mode() ) {
		// Plugin isn't in Freemius activation mode.
		return 'not_in_activation';
	}
	if ( ! $fs->is_plugin_upgrade_mode() ) {
		// Plugin isn't in plugin upgrade mode.
		return 'not_in_upgrade';
	}

	if ( ! $fs->is_first_freemius_powered_version() ) {
		// It's not the 1st version of the plugin that runs with Freemius.
		return 'freemius_installed_before';
	}

	$migration_uid = fs_get_transient( 'fsm_edd_' . $edd_download_id );

	$in_migration = ( false !== $migration_uid );

	if ( ! $is_blocking && ! $in_migration ) {
		// Initiate license migration in a non-blocking request.
		return spawn_buddyforms_edd2fs_license_migration( $edd_download_id );
	} else {
		if ( $is_blocking ||
		     ( ! empty( $_REQUEST[ 'fsm_edd_' . $edd_download_id ] ) &&
		       $migration_uid === $_REQUEST[ 'fsm_edd_' . $edd_download_id ] &&
		       'POST' === $_SERVER['REQUEST_METHOD'] )
		) {
			$success = do_buddyforms_edd2fs_license_migration(
				$edd_download_id,
				$edd_license_key,
				$edd_store_url
			);

			if ( $success ) {
				$fs->set_plugin_upgrade_complete();

				return 'success';
			}

			return 'failed';
		}
	}
}

/**
 * Try to activate EDD license.
 *
 * @author   Vova Feldman (@svovaf)
 * @since    1.0.0
 *
 * @param string $license_key
 *
 * @return bool
 */
function buddyforms_edd_activate_license( $license_key ) {
	// Call the custom API.
	$response = wp_remote_post(
		TK__EDD_STORE_URL,
		array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => array(
				'edd_action' => 'activate_license',
				'license'    => $license_key,
				'item_id'    => TK__EDD_DOWNLOAD_ID,
				'url'        => home_url()
			)
		)
	);

	// Make sure the response came back okay.
	if ( is_wp_error( $response ) ) {
		return false;
	}

	// Decode the license data.
	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	if ( 'valid' === $license_data->license ) {
		// Store EDD license key.
		update_option( 'edd_sample_license_key', $license_key );
	} else {
		return false;
	}

	return true;
}

/**
 * If installation failed due to license activation  on Freemius try to
 * activate the license on EDD first, and if successful, migrate the license
 * with a blocking request.
 *
 * This method will only be triggered upon failed module installation.
 *
 * @author   Vova Feldman (@svovaf)
 * @since    1.0.0
 *
 * @param object $response Freemius installation request result.
 * @param array  $args     Freemius installation request arguments.
 *
 * @return object|string
 */
function buddyforms_try_migrate_on_activation( $response, $args ) {
	if ( empty( $args['license_key'] ) || 32 !== strlen( $args['license_key'] ) ) {
		// No license key provided (or invalid length), ignore.
		return $response;
	}

	/**
	 * @var \Freemius $fs
	 */
	$fs = buddyforms_core_fs();

	if ( ! $fs->has_api_connectivity() ) {
		// No connectivity to Freemius API, it's up to you what to do.
		return $response;
	}

	$license_key = $args['license_key'];

	if ( ( is_object( $response->error ) && 'invalid_license_key' === $response->error->code ) ||
	     ( is_string( $response->error ) && false !== strpos( strtolower( $response->error ), 'license' ) )
	) {
		if ( buddyforms_edd_activate_license( $license_key ) ) {
			// Successfully activated license on EDD, try to migrate to Freemius.
			if ( do_buddyforms_edd2fs_license_migration(
				TK__EDD_DOWNLOAD_ID,
				$license_key,
				TK__EDD_STORE_URL,
				true
			) ) {
				/**
				 * If successfully migrated license and got to this point (no redirect),
				 * it means that it's an AJAX installation (opt-in), therefore,
				 * override the response with the after connect URL.
				 */
				return $fs->get_after_activation_url( 'after_connect_url' );
			}
		}
	}

	return $response;
}

#region Database Transient

if ( ! function_exists( 'fs_get_transient' ) ) {
	/**
	 * Very similar to the WP transient mechanism.
	 *
	 * @author   Vova Feldman (@svovaf)
	 * @since    1.0.0
	 *
	 * @param string $transient
	 *
	 * @return mixed
	 */
	function fs_get_transient( $transient ) {
		$transient_option  = '_fs_transient_' . $transient;
		$transient_timeout = '_fs_transient_timeout_' . $transient;

		$timeout = get_option( $transient_timeout );

		if ( false !== $timeout && $timeout < time() ) {
			delete_option( $transient_option );
			delete_option( $transient_timeout );
			$value = false;
		} else {
			$value = get_option( $transient_option );
		}

		return $value;
	}

	/**
	 * Not like `set_transient()`, this function will only ADD
	 * a transient if it's not yet exist.
	 *
	 * @author   Vova Feldman (@svovaf)
	 * @since    1.0.0
	 *
	 * @param string $transient
	 * @param mixed  $value
	 * @param int    $expiration
	 *
	 * @return bool TRUE if successfully added a transient.
	 */
	function fs_add_transient( $transient, $value, $expiration = 0 ) {
		$transient_option  = '_fs_transient_' . $transient;
		$transient_timeout = '_fs_transient_timeout_' . $transient;

		if ( false === get_option( $transient_option ) ) {
			$autoload = 'yes';
			if ( $expiration ) {
				$autoload = 'no';
				add_option( $transient_timeout, time() + $expiration, '', 'no' );
			}

			return add_option( $transient_option, $value, '', $autoload );
		}

		return false;
	}
}

#endregion

if ( ! defined( 'DOING_CRON' ) ) {
	// Pull EDD license key from storage.
	$license_key = trim( get_option( 'edd_sample_license_key' ) );

	if ( empty( $license_key ) ) {
		/**
		 * If no EDD license is set it might be one of the following:
		 *  1. User purchased module directly from Freemius.
		 *  2. User did purchase from EDD, but has never activated the license on this site.
		 *  3. User got access to the code without ever purchasing.
		 *
		 * In case it's reason #2, hook to Freemius `after_install_failure` event, and if
		 * the installation failure resulted due to an issue with the license, try to
		 * activate the license on EDD first, and if works, migrate to Freemius right after.
		 */
		buddyforms_core_fs()->add_filter( 'after_install_failure', 'buddyforms_try_migrate_on_activation', 10, 2 );
	} else {
		if ( ! defined( 'DOING_AJAX' ) ) {
			buddyforms_non_blocking_edd2fs_license_migration(
				TK__EDD_DOWNLOAD_ID,
				$license_key,
				TK__EDD_STORE_URL
			);
		}
	}
}