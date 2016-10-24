<?php
	if ( ! defined( 'TK_EDD_STORE_URL' ) ) {
		// This should point to your EDD install.
		define( 'TK_EDD_STORE_URL', 'https://themekraft.com' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file
	}

	// The EDD download ID of your product.
	define( 'TK_EDD_DOWNLOAD_ID', '12867' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

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
	 *
	 * @return bool
	 */
	function do_buddyforms_edd2fs_license_migration(
		$edd_download_id,
		$edd_license_key,
		$edd_store_url
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
		$response = get_transient( 'fs_license_migration_' . $edd_download_id );

		if ( false === $response ) {
			$response = wp_remote_post(
				$edd_store_url . '/fs-api/edd/migrate-license.json',
				array_merge( $install_details, array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => array_merge( $install_details, array(
						'module_id'   => $edd_download_id,
						'license_key' => $edd_license_key,
						'url'         => home_url()
					) )
				) )
			);

			// Cache result (5-min).
			set_transient( 'fs_license_migration_' . $edd_download_id, $response, 5 * MINUTE_IN_SECONDS );
		}

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			$error_message = $response->get_error_message();

			$message = ( is_wp_error( $response ) && ! empty( $error_message ) ) ?
				$error_message :
				__( 'An error occurred, please try again.' );

		} else {
			if ( ! is_object( $response ) ||
			     isset( $response->success ) ||
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

			$fs->setup_account(
				new FS_User( $response->data->user ),
				new FS_Site( $response->data->install ),
				false
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
	 *
	 * @return string|bool
	 */
	function buddyforms_non_blocking_edd2fs_license_migration(
		$edd_download_id,
		$edd_license_key,
		$edd_store_url
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

		if ( ! $in_migration ) {
			// Initiate license migration in a non-blocking request.
			return spawn_buddyforms_edd2fs_license_migration( $edd_download_id );
		} else {
			if ( $migration_uid === get_query_var( 'fsm_edd_' . $edd_download_id, false ) &&
			     'POST' === $_SERVER['REQUEST_METHOD']
			) {
				$success = do_buddyforms_edd2fs_license_migration( $edd_download_id, $edd_license_key, $edd_store_url );
				if ( $success ) {
					$fs->set_plugin_upgrade_complete();

					return 'success';
				}

				return 'failed';
			}
		}
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

	if ( ! defined( 'DOING_AJAX' ) && ! defined( 'DOING_CRON' ) ) {
		// Pull license key from storage.
		$license_key = trim( get_option( 'buddyforms_edd_license_key' ) );

		if ( empty( $license_key ) ) {
			// No license key, therefore, no migration required.
		} else {
			buddyforms_non_blocking_edd2fs_license_migration(
				TK_EDD_DOWNLOAD_ID,
				$license_key,
				TK_EDD_STORE_URL
			);
		}
	}