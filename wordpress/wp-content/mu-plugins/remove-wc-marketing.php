<?php

/**
 * Plugin Name: Remove WooCommerce Marketing Menu
 * Description: Entfernt den nervigen Marketing-Menüpunkt
 * Author: Christian Eichert<c@zp1.net>
 * Author URI:        https://github.com/ecxod
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Version: 1.0
 */
add_filter( 'woocommerce_admin_features', function( $features ) {
    $key = array_search( 'marketing', $features );
    if ( false !== $key ) {
        unset( $features[ $key ] );
    }
    return array_values( $features );
});

add_action( 'admin_menu', function() {
    remove_menu_page( 'woocommerce-marketing' );
    // Falls noch Submenüpunkte übrig bleiben (meist nicht mehr nötig)
    remove_submenu_page( 'woocommerce-marketing', 'admin.php?page=wc-admin&path=/marketing' );
}, 9999 );
