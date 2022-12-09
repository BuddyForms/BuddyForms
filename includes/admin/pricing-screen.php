<?php
//
// Add the Bundle Page to the BuddyForms Menu
//
add_action( 'admin_enqueue_scripts', 'buddyforms_freemius_checkout_script' );
function buddyforms_freemius_checkout_script() {
    wp_register_script( 'freemius-checkout', 'https://checkout.freemius.com/checkout.min.js', array(), false );
    wp_enqueue_script( 'freemius-checkout' );
}


function buddyforms_bundle_screen_menu() {
	add_submenu_page( 'edit.php?post_type=buddyforms', __( 'Bundle', 'buddyforms' ), __( 'Go Pro!', 'buddyforms' ), 'manage_options', 'buddyforms_bundle_screen', 'buddyforms_bundle_screen_content', 99 );

    global $submenu;
    $submenu[ 'edit.php?post_type=buddyforms' ][5][4] = "bf-go-pro";

}

add_action( 'admin_menu', 'buddyforms_bundle_screen_menu', 9999 );

function buddyforms_bundle_screen_content(){
    ?>
        <div class="container-pricing">
                <h2>CHOOSE THE BEST PLAN FOR YOU</h2>
                <p class="bundle-picture"><img src="<?php echo esc_url( BUDDYFORMS_ASSETS ); ?>admin/img/choose-bundle.png" alt="Bundle Choose"></p>
                <h3>Upgrade your free version or join our premium membership community of online business owners who build, grow and scale together, with our bundles.</h3>
                <div class="price-row">
                <div class="price-col tk-bundle-1">
                        <p class="bundle-type">Pro Version</p>
                        <h4><span id='savings-price'>$59.99</span> <span>/year</span></h4>
                        <h5>BLOCKED WITH THE FREE VERSION? UNLOCK ALL PRO FEATURES.</h5>
                        <h3><span class="fs-bundle-currency">$</span><span class="fs-bundle-price-1">39.99</span> /year</h3>
                        <ul>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/frontend-post-form-buddyforms/" class="tk-bundle-link" target="_blank">BuddyForms</a></li>
                        </ul>
                        <select id="licenses-1">
                            <option value="1" selected="selected">Single Site License</option>
                            <option value="5">5-Site License</option>
                            <option value="unlimited">Unlimited Sites License</option>
                        </select>
                        <button id="purchase">GET NOW</button>
                    </div>
                    <div class="price-col tk-bundle-2">
                        <p class="bundle-type">BuddyForms Bundle</p>
                        <h4><span id='savings-price-2'>$342.84</span> <span>/year</span></h4>
                        <h5>GET ALL BUDDYFORMS PRODUCTS FOR THE PRICE OF ONE.</h5>
                        <h3><span class="fs-bundle-currency">$</span><span class="fs-bundle-price-2">89.99</span> /year</h3>
                        <ul>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/frontend-post-form-buddyforms/" class="tk-bundle-link" target="_blank">BuddyForms</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddyforms-advanced-custom-fields/" class="tk-bundle-link" target="_blank">BuddyForms ACF</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddyforms-anonymous-author/" class="tk-bundle-link" target="_blank">BuddyForms Anonymous Author</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddypress-group-post/" class="tk-bundle-link" target="_blank">BuddyForms Attach Post with Group</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/wordpress-custom-login-page/" class="tk-bundle-link" target="_blank">BuddyForms Custom Login</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddyforms-geo-my-wp/" class="tk-bundle-link" target="_blank">BuddyForms Geo My WP</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/display-submissions-data/" class="tk-bundle-link" target="_blank">BuddyForms Hook Fields</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddyforms-hierarchical-posts/" class="tk-bundle-link" target="_blank">BuddyForms Hierarchical Posts</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddyforms-mailpoet/" class="tk-bundle-link" target="_blank">BuddyForms MailPoet</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddyforms-buddypress-members/" class="tk-bundle-link" target="_blank">BuddyForms Members</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddyforms-content-moderation/" class="tk-bundle-link" target="_blank">BuddyForms Moderation</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddyforms-pay-for-submissions/" class="tk-bundle-link" target="_blank">BuddyForms Pay for Submission</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/wordpress-pods-forms/" class="tk-bundle-link" target="_blank">BuddyForms Pods</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddyforms-buddypress-post-in-groups/" class="tk-bundle-link" target="_blank">BuddyForms Poost in Groups</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddypress-profile-image/" class="tk-bundle-link" target="_blank">BuddyForms Profile Image</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddyforms-remote-embed-forms/" class="tk-bundle-link" target="_blank">BuddyForms Remote</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddyforms-ultimate-member/" class="tk-bundle-link" target="_blank">BuddyForms Ultimate Member</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddyforms-woocommerce-bookings/" class="tk-bundle-link" target="_blank">BuddyForms WooCoomerce Bookings</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/woocommerce-form-elements/" class="tk-bundle-link" target="_blank">BuddyForms WooCoomerce Form Elements</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddyforms-woocommerce-simple-auction/" class="tk-bundle-link" target="_blank">BuddyForms WooCoomerce Simple Auctions</a></li>
                        </ul>
                        <select id="licenses-2">
                            <option value="1" selected="selected">Single Site License</option>
                            <option value="5">5-Site License</option>
                            <option value="unlimited">Unlimited Sites License</option>
                        </select>
                        <button id="purchase-2">GET NOW</button>
                    </div>
                    <div class="price-col tk-bundle-3">
                        <p class="bundle-type">TK Membership</p>
                        <h4><span id='savings-price-3'>$602.75</span> <span>/year</span></h4>
                        <h5>PREMIUM PACK WITH ALL OUR PRODUCTS INCLUDED.</h5>
                        <h3><span class="fs-bundle-currency">$</span><span class="fs-bundle-price-3">99.99</span> /year</h3>
                        <ul>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/all-in-one-invite-codes/" class="tk-bundle-link" target="_blank">All in One Invite Codes</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/restrict-forms-invite-codes/" class="tk-bundle-link" target="_blank">All in One Invite Codes BuddyForms</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/invite-codes-buddypress/" class="tk-bundle-link" target="_blank">All in One Invite Codes BuddyPress</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/frontend-post-form-buddyforms/" class="tk-bundle-link" target="_blank">BuddyForms</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddyforms-advanced-custom-fields/" class="tk-bundle-link" target="_blank">BuddyForms ACF</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddyforms-anonymous-author/" class="tk-bundle-link" target="_blank">BuddyForms Anonymous Author</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddypress-group-post/" class="tk-bundle-link" target="_blank">BuddyForms Attach Post with Group</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/wordpress-custom-login-page/" class="tk-bundle-link" target="_blank">BuddyForms Custom Login</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddyforms-geo-my-wp/" class="tk-bundle-link" target="_blank">BuddyForms Geo My WP</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/display-submissions-data/" class="tk-bundle-link" target="_blank">BuddyForms Hook Fields</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddyforms-hierarchical-posts/" class="tk-bundle-link" target="_blank">BuddyForms Hierarchical Posts</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddyforms-mailpoet/" class="tk-bundle-link" target="_blank">BuddyForms MailPoet</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddyforms-buddypress-members/" class="tk-bundle-link" target="_blank">BuddyForms Members</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddyforms-content-moderation/" class="tk-bundle-link" target="_blank">BuddyForms Moderation</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddyforms-pay-for-submissions/" class="tk-bundle-link" target="_blank">BuddyForms Pay for Submission</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/wordpress-pods-forms/" class="tk-bundle-link" target="_blank">BuddyForms Pods</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddyforms-buddypress-post-in-groups/" class="tk-bundle-link" target="_blank">BuddyForms Poost in Groups</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddypress-profile-image/" class="tk-bundle-link" target="_blank">BuddyForms Profile Image</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddyforms-remote-embed-forms/" class="tk-bundle-link" target="_blank">BuddyForms Remote</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddyforms-ultimate-member/" class="tk-bundle-link" target="_blank">BuddyForms Ultimate Member</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddyforms-woocommerce-bookings/" class="tk-bundle-link" target="_blank">BuddyForms WooCoomerce Bookings</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/woocommerce-form-elements/" class="tk-bundle-link" target="_blank">BuddyForms WooCoomerce Form Elements</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/buddyforms-woocommerce-simple-auction/" class="tk-bundle-link" target="_blank">BuddyForms WooCoomerce Simple Auctions</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/bp-wc-vendors/" class="tk-bundle-link" target="_blank">BuddyPress WooCommerce Vendors</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/selling-lessons-with-learndash/" class="tk-bundle-link" target="_blank">LearnDash Pay for Lessons</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/larry-wordpress-buddyboss-theme/" class="tk-bundle-link" target="_blank">Larry Theme</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/gdpr-compliant-google-fonts/" class="tk-bundle-link" target="_blank">TK Google Fonts</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/woocommerce-buddypress-integration/" class="tk-bundle-link" target="_blank">WooBuddy</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/woocommerce-buddypress-groups/" class="tk-bundle-link" target="_blank">WooBuddy Groups</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/woocommerce-subscriptions-buddypress/" class="tk-bundle-link" target="_blank">WooBuddy Subscriptions</a></li>
                            <li class="tk-bundle-product"><a href="https://themekraft.com/wordpress-products/woobuddy-checkout-manager/" class="tk-bundle-link" target="_blank">WooBuddy xProfile Checkout Manager</a></li>
                        </ul>
                        <select id="licenses-3">
                            <option value="1" selected="selected">Single Site License</option>
                            <option value="5">5-Site License</option>
                            <option value="unlimited">Unlimited Sites License</option>
                        </select>
                        <button id="purchase-3">GET NOW</button>
                    </div>
                </div>
        </div>
    <?php
}

