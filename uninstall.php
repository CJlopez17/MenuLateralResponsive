<?php
/**
 * Limpieza al desinstalar el plugin.
 *
 * Se ejecuta cuando el plugin es eliminado desde el admin de WordPress.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Eliminar las opciones del plugin de la base de datos
delete_option( 'mlr_options' );

// Si es multisite, eliminar opciones de cada sitio
if ( is_multisite() ) {
    $sites = get_sites();
    foreach ( $sites as $site ) {
        switch_to_blog( $site->blog_id );
        delete_option( 'mlr_options' );
        restore_current_blog();
    }
}
