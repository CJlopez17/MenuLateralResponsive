<?php
/**
 * Clase para la activación y desactivación del plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MLR_Activator {

    public static function activate() {
        $default_options = array(
            'header_color'    => '#7B2D8E',
            'header_text'     => '#ffffff',
            'card_border'     => '#7B2D8E',
            'card_icon_color' => '#7B2D8E',
            'card_text_color' => '#333333',
            'card_bg_hover'   => '#f5f0f7',
            'overlay_opacity' => '0.6',
            'menu_title'      => 'Menú',
            'panel_width'     => '280',
        );

        if ( false === get_option( 'mlr_options' ) ) {
            add_option( 'mlr_options', $default_options );
        }

        flush_rewrite_rules();
    }

    public static function deactivate() {
        flush_rewrite_rules();
    }
}
