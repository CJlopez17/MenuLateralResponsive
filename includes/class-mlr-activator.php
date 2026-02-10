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

        // Datos del menú
        $default_menu_data = array(
            'top_links' => array(
                array( 'title' => 'Seguridad', 'url' => '#' ),
                array( 'title' => 'Blog', 'url' => '#' ),
                array( 'title' => 'Contáctanos', 'url' => '#' ),
            ),
            'cards' => array(
                array(
                    'title'      => 'Productos',
                    'icon_type'  => 'builtin',
                    'icon_name'  => 'grid',
                    'icon_url'   => '',
                    'categories' => array(
                        array(
                            'title' => 'Ahorros',
                            'color' => '#7B2D8E',
                            'links' => array(
                                array( 'title' => 'Cuenta de ahorros', 'url' => '#' ),
                                array( 'title' => 'Cuenta inteligente (Con chequera)', 'url' => '#' ),
                                array( 'title' => 'Ahorro propósito', 'url' => '#' ),
                            ),
                        ),
                        array(
                            'title' => 'Inversiones',
                            'color' => '#7B2D8E',
                            'links' => array(
                                array( 'title' => 'Depósito a plazo fijo (Póliza)', 'url' => '#' ),
                            ),
                        ),
                        array(
                            'title' => 'Crédito',
                            'color' => '#7B2D8E',
                            'links' => array(
                                array( 'title' => 'Crédito para tu negocio', 'url' => '#' ),
                                array( 'title' => 'Crédito para tus gastos', 'url' => '#' ),
                            ),
                        ),
                        array(
                            'title' => 'Tarjeta de crédito',
                            'color' => '#7B2D8E',
                            'links' => array(
                                array( 'title' => 'Tarjeta de crédito Mastercard', 'url' => '#' ),
                            ),
                        ),
                    ),
                ),
                array(
                    'title'      => 'Canales electrónicos',
                    'icon_type'  => 'builtin',
                    'icon_name'  => 'screen',
                    'icon_url'   => '',
                    'categories' => array(),
                ),
                array(
                    'title'      => 'Beneficios',
                    'icon_type'  => 'builtin',
                    'icon_name'  => 'heart',
                    'icon_url'   => '',
                    'categories' => array(),
                ),
                array(
                    'title'      => 'Institución',
                    'icon_type'  => 'builtin',
                    'icon_name'  => 'building',
                    'icon_url'   => '',
                    'categories' => array(),
                ),
            ),
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
