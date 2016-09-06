<?php
/**
 * Created by PhpStorm.
 * User: svenl77
 * Date: 25.03.14
 * Time: 14:44
 */

/**
 * Create "BuddyForms Options" nav menu
 *
 * @package buddyforms
 * @since 0.1-beta
 */
function buddyforms_create_addons_menu() {

	add_submenu_page( 'edit.php?post_type=buddyforms', __( 'Add-ons', 'buddyforms' ), __( 'Add-ons', 'buddyforms' ), 'manage_options', 'buddyforms-addons', 'bf_add_ons_screen' );

}

add_action( 'admin_menu', 'buddyforms_create_addons_menu', 99999999 );

function bf_add_ons_screen() {

	// Check that the user is allowed to update options
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'buddyforms' ) );
	} ?>

	<div id="bf_admin_wrap" class="wrap">

		<?php

		include( 'bf-admin-header.php' );

		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';


		$call_api = plugins_api( 'query_plugins',
			array(
				'author'   => 'svenl77',
				'per_page' => '99',
				'fields'   => array(
					'downloaded'        => true,
					'active_installs'   => true,
					'icons'             => true,
					'rating'            => true,
					'num_ratings'       => true,
					'description'       => false,
					'short_description' => true,
					'donate_link'       => false,
					'tags'              => false,
					'sections'          => false,
					'homepage'          => true,
					'added'             => false,
					'last_updated'      => true,
					'compatibility'     => true,
					//'tested'            => true,
					'requires'          => true,
					'downloadlink'      => true,
				)
			)
		);


		add_thickbox();

		// buddyforms_get_addons( $call_api );
		buddyforms_get_addons_freemius();
		?>


	</div>

	<?php
}


function buddyforms_get_addons_freemius() { ?>
		<style>
			li.fs-cta a {
				display: none !important;
			}
		</style>
	<?php
}



















function buddyforms_get_addons( $call_api ) {


	$wp_plugins = array();
	foreach($call_api->plugins as $plugin){
		$wp_plugins[$plugin->slug] = $plugin;
	}



$slug = 'buddyforms';
/**
 * @var Freemius
 */
$fs = freemius( $slug );

$open_addon_slug = fs_request_get( 'slug' );

$open_addon = false;

/**
 * @var FS_Plugin[]
 */
$addons = $fs->get_addons();

$has_addons = ( is_array( $addons ) && 0 < count( $addons ) );
?>
<style>
	h4 {
		font-size: 18px;
		line-height: 20px;
	}
	h4 a {
		text-decoration: none;
	}
	div#fs_addons {
		display: none;
	}
</style>

	<h2><?php printf( __fs( 'add-ons-for-x', $slug ), $fs->get_plugin_name() ) ?></h2>

	<div id="">
		<?php if ( ! $has_addons ) : ?>
			<h3><?php printf(
					'%s... %s',
					__fs( 'oops', $slug ),
					__fs( 'add-ons-missing', $slug )
				) ?></h3>
		<?php endif ?>
		<div class="col-12" itemscope="" itemtype="http://schema.org/SoftwareApplication">
			<?php if ( $has_addons ) : ?>
			<div class="plugin-group">
				<?php foreach ( $addons as $addon ) : ?>
					<?php
					$open_addon = ( $open_addon || ( $open_addon_slug === $addon->slug ) );

					$price        = 0;
					$plan         = null;
					$plans_result = $fs->get_api_site_or_plugin_scope()->get( "/addons/{$addon->id}/plans.json" );
					if ( ! isset( $plans_result->error ) ) {
						$plans = $plans_result->plans;
						if ( is_array( $plans ) && 0 < count( $plans ) ) {
							$plan           = new FS_Plugin_Plan( $plans[0] );
							$pricing_result = $fs->get_api_site_or_plugin_scope()->get( "/addons/{$addon->id}/plans/{$plan->id}/pricing.json" );
							if ( ! isset( $pricing_result->error ) ) {
								// Update plan's pricing.
								$plan->pricing = $pricing_result->pricing;

								if ( is_array( $plan->pricing ) && 0 < count( $plan->pricing ) ) {
									$min_price = 999999;
									foreach ( $plan->pricing as $pricing ) {
										if ( ! is_null( $pricing->annual_price ) && $pricing->annual_price > 0 ) {
											$min_price = min( $min_price, $pricing->annual_price );
										} else if ( ! is_null( $pricing->monthly_price ) && $pricing->monthly_price > 0 ) {
											$min_price = min( $min_price, 12 * $pricing->monthly_price );
										}
									}

									if ( $min_price < 999999 ) {
										$price = $min_price;
									}
								}
							}
						}
					}

					echo sprintf( '<a href="%s" class="thickbox fs-overlay" aria-label="%s" data-title="%s"></a>',
						esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&parent_plugin_id=' . $fs->get_id() . '&plugin=' . $addon->slug .
						                            '&TB_iframe=true&width=600&height=550' ) ),
						esc_attr( sprintf( __fs( 'more-information-about-x', $slug ), $addon->title ) ),
						esc_attr( $addon->title )
					);

					if ( is_null( $addon->info ) ) {
						$addon->info = new stdClass();
					}

					if ( ! isset( $addon->info->card_banner_url ) ) {
						$addon->info->card_banner_url = '//dashboard.freemius.com/assets/img/marketing/blueprint-300x100.jpg';
					}


					if ( ! isset( $addon->info->short_description ) ) {
						$addon->info->short_description = 'What\'s the one thing your add-on does really, really well?';
					}

					$date_format            = __( 'M j, Y @ H:i' );
					$last_updated_timestamp = strtotime( $addon->updated );

					$status = Array(
						'status' => 'pro',
					);




					$action_links = array();
					$action_links[] = sprintf( '<a href="%s" class="button thickbox fs-overlay" aria-label="%s" data-title="%s">Purchase</a>',
						esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&parent_plugin_id=' . $fs->get_id() . '&plugin=' . $addon->slug .
						                            '&TB_iframe=true&width=600&height=550' ) ),
						esc_attr( sprintf( __fs( 'more-information-about-x', $slug ), $addon->title ) ),
						esc_attr( 'Purchase' )
					);

					if ( 0 == $price && isset( $wp_plugins[$addon->slug] ) ){
						$plugin = (array) $wp_plugins[$addon->slug];

						$date_format            = __( 'M j, Y @ H:i' );
						$last_updated_timestamp = strtotime( $plugin['last_updated'] );

						$details_link = $plugin['homepage'];

						if ( ! empty( $plugin['icons']['svg'] ) ) {
							$plugin_icon_url = $plugin['icons']['svg'];
						} elseif ( ! empty( $plugin['icons']['2x'] ) ) {
							$plugin_icon_url = $plugin['icons']['2x'];
						} elseif ( ! empty( $plugin['icons']['1x'] ) ) {
							$plugin_icon_url = $plugin['icons']['1x'];
						} else {
							$plugin_icon_url = $plugin['icons']['default'];
						}

						$addon->info->card_banner_url = $plugin_icon_url;
						$addon->info->short_description = strip_tags( $plugin['short_description'] );


						$action_links = array();

						// Remove any HTML from the description.
						$description = strip_tags( $plugin['short_description'] );
						$version     = $plugin['version'];
						$title       = $plugin['name'];
						$name        = strip_tags( $title . ' ' . $version );

						$author = $plugin['author'];

						if ( ! empty( $author ) ) {
							$author = ' <cite>' . sprintf( __( 'By %s' ), $author ) . '</cite>';
						}


						if ( current_user_can( 'install_plugins' ) || current_user_can( 'update_plugins' ) ) {
							$status = install_plugin_install_status( $plugin );




							//echo $status['status'];
							//echo wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . sanitize_html_class( $plugin['slug'] )), 'install-plugin_' . sanitize_html_class( $plugin['slug'] ));



							switch ( $status['status'] ) {
								case 'install':
									if ( $status['url'] ) {
										/* translators: 1: Plugin name and version. */
										$action_links[] = '<a class="install-now button" data-slug="' . esc_attr( $plugin['slug'] ) . '" href="' . esc_url( $status['url'] ) . '" aria-label="' . esc_attr( sprintf( __( 'Install %s now' ), $name ) ) . '" data-name="' . esc_attr( $name ) . '">' . __( 'Install Now' ) . '</a>';
									}

									break;
								case 'update_available':
									if ( $status['url'] ) {
										/* translators: 1: Plugin name and version */
										$action_links[] = '<a class="update-now button" data-plugin="' . esc_attr( $status['file'] ) . '" data-slug="' . esc_attr( $plugin['slug'] ) . '" href="' . esc_url( $status['url'] ) . '" aria-label="' . esc_attr( sprintf( __( 'Update %s now' ), $name ) ) . '" data-name="' . esc_attr( $name ) . '">' . __( 'Update Now' ) . '</a>';
									}

									break;
								case 'latest_installed':
								case 'newer_installed':
									//$action_links[] = '<a class="activate-now button" data-plugin="' . esc_attr( $status['file'] ) . '" data-slug="' . esc_attr( $plugin['slug'] ) . '" href="' . wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . sanitize_html_class( $plugin['slug'] )), 'install-plugin_' . sanitize_html_class( $plugin['slug'] )) . '" aria-label="' . esc_attr( sprintf( __( 'Update %s now' ), $name ) ) . '" data-name="' . esc_attr( $name ) . '">' . __( 'Update Now' ) . '</a>';

									$action_links[] = '<span class="button button-disabled" title="' . esc_attr__( 'This plugin is already installed and is up to date' ) . ' ">' . _x( 'Installed', 'plugin' ) . '</span>';
									break;
							}
						}
					}

					?>

					<div data-slug="<?php echo $addon->slug ?>" class="plugin-card plugin-card-<?php echo $addon->slug ?>">
						<?php
						$status = '';
						$status = install_plugin_install_status( $addon );


//						print_r($status);

						?>
						<div class="plugin-card-top">
							<a target="_blank" class="thickbox plugin-icon"><img width="125px;" height="125px;"
							                                                     src="<?php echo esc_attr( $addon->info->card_banner_url ) ?>"/></a>
							<div class="name column-name">
								<h4>

									<?php

									echo sprintf( '<a href="%s" class="thickbox fs-overlay" aria-label="%s" data-title="%s">%s</a>',
										esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&parent_plugin_id=' . $fs->get_id() . '&plugin=' . $addon->slug .
										                            '&TB_iframe=true&width=600&height=550' ) ),
										esc_attr( sprintf( __fs( 'more-information-about-x', $slug ), $addon->title ) ),
										esc_attr( $addon->title ),
										$addon->title
									);?>

								</h4>
							</div>
							<div class="action-links">
								<?php
								if ( $action_links ) {
									echo '<ul class="plugin-action-buttons"><li>' . implode( '</li><li>', $action_links ) . '</li></ul>';
								}
								?>
							</div>
							<div class="desc column-description">
								<p><?php echo $addon->info->short_description; ?></p>
							</div>
						</div>
						<div class="plugin-card-bottom">
							<div class="vers column-rating">
								<li class="fs-offer">
									<span
										class="fs-price"><?php echo ( 0 == $price ) ? __fs( 'free', $slug ) : ('$' . number_format( $price, 2 ) . ($plan->has_trial() ? ' - ' . __fs('trial', $slug) : '')) ?></span>
								</li>
							</div>
							<div class="column-updated">
								<strong><?php _e( 'Last Updated:' ); ?></strong>
									<span
										title="<?php echo esc_attr( date_i18n( $date_format, $last_updated_timestamp ) ); ?>">
										<?php printf( __( '%s ago' ), human_time_diff( $last_updated_timestamp ) ); ?>
									</span>
							</div>
							<div class="column-downloaded">
								<?php echo sprintf( '<a href="%s" class="button thickbox fs-overlay" aria-label="%s" data-title="%s">View Details</a>',
									esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&parent_plugin_id=' . $fs->get_id() . '&plugin=' . $addon->slug .
									                            '&TB_iframe=true&width=600&height=550' ) ),
									esc_attr( sprintf( __fs( 'more-information-about-x', $slug ), $addon->title ) ),
									esc_attr( $addon->title )
								);?>
							</div>
							<div class="column-compatibility">
								<?php echo $addon->version; ?>
							</div>
						</div>
					</div>
				<?php endforeach ?>
				<?php endif ?>
			</div>
		</div>
	</div>
<script type="text/javascript">
	(function ($) {
		<?php if ( $open_addon ) : ?>

		var interval = setInterval(function () {
			// Open add-on information page.
			$('.fs-card[data-slug=<?php echo $open_addon_slug ?>] a').click();
			if ($('#TB_iframeContent').length > 0) {
				clearInterval(interval);
				interval = null;
			}
		}, 200);

		<?php else : ?>


		$('.fs-card.fs-addon').mouseover(function(){
			$(this).find('.fs-cta .button').addClass('button-primary');
		});

		$('.fs-card.fs-addon').mouseout(function(){
			$(this).find('.fs-cta .button').removeClass('button-primary');
		});

		<?php endif ?>
	})(jQuery);
</script>
<?php
}