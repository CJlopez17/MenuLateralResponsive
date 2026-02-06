<?php
/**
 * Clase para la activación y desactivación del plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MLR_Activator {

    /**
     * Ejecuta acciones al activar el plugin.
     */
    public static function activate() {
        $default_options = array(
            'position'        => 'left',
            'width'           => '300',
            'bg_color'        => '#1a1a2e',
            'text_color'      => '#ffffff',
            'hover_color'     => '#16213e',
            'accent_color'    => '#0f3460',
            'overlay_color'   => 'rgba(0,0,0,0.5)',
            'close_on_overlay' => true,
            'show_logo'       => false,
            'logo_url'        => '',
            'menu_location'   => 'mlr_sidebar_menu',
        );

        if ( false === get_option( 'mlr_options' ) ) {
            add_option( 'mlr_options', $default_options );
        }

        flush_rewrite_rules();
    }

    /**
     * Ejecuta acciones al desactivar el plugin.
     */
    public static function deactivate() {
        flush_rewrite_rules();
    }
}
