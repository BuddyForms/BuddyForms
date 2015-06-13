<?php
/**
 * Created by PhpStorm.
 * User: svenl77
 * Date: 25.03.14
 * Time: 14:44
 */

function bf_add_ons_screen(){

    // Check that the user is allowed to update options
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.','buddyforms'));
    } ?>

    <div id="bf_admin_wrap" class="wrap">

    <?php

    include('admin-credits.php');

    include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

    $call_api = plugins_api( 'query_plugins',
        array(
            'search' => 'buddyforms',
            'page' => '1',
            'per_page' => '-1',
            'fields' => array(
                'downloaded' => true,
                'active_installs' => true,
                'icons' => true,
                'rating' => true,
                'num_ratings' => true,
                'description' => false,
                'short_description' => true,
                'donate_link' => false,
                'tags' => false,
                'sections' => false,
                'homepage' => false,
                'added' => false,
                'last_updated' => true,
                'compatibility' => true,
                'tested' => true,
                'requires' => true,
                'downloadlink' => true,
            )
        )
    );
    add_thickbox();
    buddyforms_get_addons($call_api); ?>


    </div>

<?php
}

function buddyforms_get_addons($call_api){ ?>

    <div class="col-12" itemscope="" itemtype="http://schema.org/SoftwareApplication">


        <p style="text-align: right"><strong style="float: left"> Showing <?php echo count($call_api->plugins)+1 ?> Extensions </strong>
        <br class="clear"></p>

        <div class="plugin-group">
            <?php foreach($call_api->plugins as $plugin) :

                $plugin = (array) $plugin;

                $date_format = __( 'M j, Y @ H:i' );
                $last_updated_timestamp = strtotime( $plugin['last_updated'] );

                $details_link   = self_admin_url('plugin-install.php?tab=plugin-information&plugin=' . $plugin['slug'] . '&TB_iframe=true&width=772&height=600');

                if ( !empty( $plugin['icons']['svg'] ) ) {
                    $plugin_icon_url = $plugin['icons']['svg'];
                } elseif ( !empty( $plugin['icons']['2x'] ) ) {
                    $plugin_icon_url = $plugin['icons']['2x'];
                } elseif ( !empty( $plugin['icons']['1x'] ) ) {
                    $plugin_icon_url = $plugin['icons']['1x'];
                } else {
                    $plugin_icon_url = $plugin['icons']['default'];
                }
                $action_links = array();

                // Remove any HTML from the description.
                $description = strip_tags( $plugin['short_description'] );
                $version = wp_kses( $plugin['version'], $plugins_allowedtags );
                $title = wp_kses( $plugin['name'], $plugins_allowedtags );
                $name = strip_tags( $title . ' ' . $version );

                $author = wp_kses( $plugin['author'], $plugins_allowedtags );
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

                ?>
                <div class="plugin-card plugin-card-<?php echo sanitize_html_class( $plugin['slug'] ); ?>">
                    <div class="plugin-card-top">
                        <a href="<?php echo esc_url( $details_link ); ?>" class="thickbox plugin-icon"><img src="<?php echo esc_attr( $plugin_icon_url ) ?>" /></a>
                        <div class="name column-name">
                            <h4><a href="<?php echo esc_url( $details_link ); ?>" class="thickbox"><?php echo $plugin['name']; ?></a></h4>
                        </div>
                        <div class="action-links">
                            <?php
                            if ( $action_links ) {
                                echo '<ul class="plugin-action-buttons"><li>' . implode( '</li><li>', $action_links ) . '</li></ul>';
                            }
                            ?>
                        </div>
                        <div class="desc column-description">
                            <p><?php echo $description; ?></p>
                            <p class="authors"><?php echo $author; ?></p>
                        </div>
                    </div>
                    <div class="plugin-card-bottom">
                        <div class="vers column-rating">
                            <?php wp_star_rating( array( 'rating' => $plugin['rating'], 'type' => 'percent', 'number' => $plugin['num_ratings'] ) ); ?>
                            <span class="num-ratings">(<?php echo number_format_i18n( $plugin['num_ratings'] ); ?>)</span>
                        </div>
                        <div class="column-updated">
                            <strong><?php _e( 'Last Updated:' ); ?></strong> <span title="<?php echo esc_attr( date_i18n( $date_format, $last_updated_timestamp ) ); ?>">
						<?php printf( __( '%s ago' ), human_time_diff( $last_updated_timestamp ) ); ?>
					</span>
                        </div>
                        <div class="column-downloaded">
                            <?php
                            if ( $plugin['active_installs'] >= 1000000 ) {
                                $active_installs_text = _x( '1+ Million', 'Active plugin installs' );
                            } else {
                                $active_installs_text = number_format_i18n( $plugin['active_installs'] ) . '+';
                            }
                            printf( __( '%s Active Installs' ), $active_installs_text );
                            ?>
                        </div>
                        <div class="column-compatibility">
                            <?php
                            if ( ! empty( $plugin['tested'] ) && version_compare( substr( $GLOBALS['wp_version'], 0, strlen( $plugin['tested'] ) ), $plugin['tested'], '>' ) ) {
                                echo '<span class="compatibility-untested">' . __( 'Untested with your version of WordPress' ) . '</span>';
                            } elseif ( ! empty( $plugin['requires'] ) && version_compare( substr( $GLOBALS['wp_version'], 0, strlen( $plugin['requires'] ) ), $plugin['requires'], '<' ) ) {
                                echo '<span class="compatibility-incompatible">' . __( '<strong>Incompatible</strong> with your version of WordPress' ) . '</span>';
                            } else {
                                echo '<span class="compatibility-compatible">' . __( '<strong>Compatible</strong> with your version of WordPress' ) . '</span>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php
}