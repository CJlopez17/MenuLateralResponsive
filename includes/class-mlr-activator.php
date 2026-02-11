<?php
/**
 * Clase para la activación y desactivación del plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MLR_Activator {

    public static function activate() {
        // Opciones de apariencia
        $default_options = array(
            'header_color'       => '#7B2D8E',
            'header_text'        => '#ffffff',
            'card_border'        => '#7B2D8E',
            'card_icon_color'    => '#7B2D8E',
            'card_text_color'    => '#333333',
            'card_bg_hover'      => '#f5f0f7',
            'card_active_indicator' => '#7B2D8E',
            'submenu_cat_color'  => '#7B2D8E',
            'submenu_link_color' => '#555555',
            'submenu_link_hover' => '#7B2D8E',
            'overlay_opacity'    => '0.6',
            'menu_title'         => 'Menú',
            'panel_width'        => '280',
            'submenu_width'      => '520',
        );

        if ( false === get_option( 'mlr_options' ) ) {
            add_option( 'mlr_options', $default_options );
        }

        // Datos del menú - vacío por defecto, se configura desde el admin
        $default_menu_data = array(
            'top_links' => array(),
            'cards'     => array(),
        );

        if ( false === get_option( 'mlr_menu_data' ) ) {
            add_option( 'mlr_menu_data', $default_menu_data );
        }

        flush_rewrite_rules();
    }

    public static function deactivate() {
        flush_rewrite_rules();
    }
}
