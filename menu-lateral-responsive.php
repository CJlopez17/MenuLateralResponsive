<?php
/**
 * Plugin Name: Menu Lateral Responsive
 * Plugin URI: https://github.com/CJlopez17/MenuLateralResponsive
 * Description: Plugin de menú lateral responsive con submenús desplegables. Compatible con Elementor. Disponible como shortcode [menu_lateral] y como widget de Elementor. Todo se configura desde el panel de administración del plugin.
 * Version: 3.0.0
 * Author: CJlopez17
 * Author URI: https://github.com/CJlopez17
 * License: GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: menu-lateral-responsive
 * Domain Path: /languages
 * Elementor tested up to: 3.18
 * Requires at least: 5.6
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'MLR_VERSION', '3.0.0' );
define( 'MLR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MLR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MLR_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

final class Menu_Lateral_Responsive {

    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->load_dependencies();
        $this->set_hooks();
    }

    private function load_dependencies() {
        require_once MLR_PLUGIN_DIR . 'includes/class-mlr-activator.php';
        require_once MLR_PLUGIN_DIR . 'includes/class-mlr-shortcode.php';
        require_once MLR_PLUGIN_DIR . 'admin/class-mlr-admin.php';
    }

    private function set_hooks() {
        register_activation_hook( __FILE__, array( 'MLR_Activator', 'activate' ) );
        register_deactivation_hook( __FILE__, array( 'MLR_Activator', 'deactivate' ) );

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
        add_action( 'plugins_loaded', array( $this, 'init_elementor_widget' ) );
    }

    public function load_textdomain() {
        load_plugin_textdomain(
            'menu-lateral-responsive',
            false,
            dirname( MLR_PLUGIN_BASENAME ) . '/languages'
        );
    }

    public function enqueue_frontend_assets() {
        wp_enqueue_style(
            'mlr-styles',
            MLR_PLUGIN_URL . 'assets/css/mlr-styles.css',
            array(),
            MLR_VERSION
        );

        // Inyectar CSS dinámico con alta especificidad para proteger colores
        // contra sobreescrituras de temas WordPress, Elementor, etc.
        wp_add_inline_style( 'mlr-styles', $this->get_color_protection_css() );

        wp_enqueue_script(
            'mlr-scripts',
            MLR_PLUGIN_URL . 'assets/js/mlr-scripts.js',
            array(),
            MLR_VERSION,
            true
        );

        wp_localize_script( 'mlr-scripts', 'mlrConfig', array(
            'animationSpeed' => 400,
        ) );
    }

    /**
     * Genera CSS dinámico con alta especificidad para proteger los colores
     * configurados por el admin contra sobreescrituras de temas/plugins.
     */
    private function get_color_protection_css() {
        $options = get_option( 'mlr_options', array() );

        $header_color   = isset( $options['header_color'] ) ? sanitize_hex_color( $options['header_color'] ) : '#7B2D8E';
        $header_text    = isset( $options['header_text'] ) ? sanitize_hex_color( $options['header_text'] ) : '#ffffff';
        $card_border    = isset( $options['card_border'] ) ? sanitize_hex_color( $options['card_border'] ) : '#7B2D8E';
        $card_icon      = isset( $options['card_icon_color'] ) ? sanitize_hex_color( $options['card_icon_color'] ) : '#7B2D8E';
        $card_text      = isset( $options['card_text_color'] ) ? sanitize_hex_color( $options['card_text_color'] ) : '#333333';
        $card_hover     = isset( $options['card_bg_hover'] ) ? sanitize_hex_color( $options['card_bg_hover'] ) : '#f5f0f7';
        $card_indicator = isset( $options['card_active_indicator'] ) ? sanitize_hex_color( $options['card_active_indicator'] ) : '#7B2D8E';
        $sub_cat_color  = isset( $options['submenu_cat_color'] ) ? sanitize_hex_color( $options['submenu_cat_color'] ) : '#7B2D8E';
        $sub_link_color = isset( $options['submenu_link_color'] ) ? sanitize_hex_color( $options['submenu_link_color'] ) : '#555555';
        $sub_link_hover = isset( $options['submenu_link_hover'] ) ? sanitize_hex_color( $options['submenu_link_hover'] ) : '#7B2D8E';

        $css = "
            /* MLR: Protección dinámica de colores - Alta especificidad */

            /* Header */
            #mlr-panel .mlr-panel-header,
            #mlr-panel .mlr-panel-header * {
                color: {$header_text} !important;
            }
            #mlr-panel .mlr-panel-header {
                background-color: {$header_color} !important;
            }

            /* Top links - todos los estados */
            #mlr-panel .mlr-top-links a,
            #mlr-panel .mlr-top-links a:link,
            #mlr-panel .mlr-top-links a:visited,
            #mlr-panel .mlr-top-links a:active,
            #mlr-panel .mlr-top-links a:focus,
            #mlr-panel .mlr-top-links a:hover,
            #mlr-panel a.mlr-top-link,
            #mlr-panel a.mlr-top-link:link,
            #mlr-panel a.mlr-top-link:visited,
            #mlr-panel a.mlr-top-link:active,
            #mlr-panel a.mlr-top-link:focus,
            #mlr-panel a.mlr-top-link:hover {
                color: {$header_text} !important;
            }

            /* Close button */
            #mlr-panel .mlr-close-btn,
            #mlr-panel .mlr-close-btn:hover,
            #mlr-panel .mlr-close-btn:active,
            #mlr-panel .mlr-close-btn:focus {
                color: {$header_text} !important;
            }

            /* Cards - icon */
            #mlr-panel .mlr-card-icon,
            #mlr-panel .mlr-card:hover .mlr-card-icon,
            #mlr-panel .mlr-card:active .mlr-card-icon,
            #mlr-panel .mlr-card:focus .mlr-card-icon,
            #mlr-panel a.mlr-card:visited .mlr-card-icon {
                color: {$card_icon} !important;
            }

            /* Cards - label */
            #mlr-panel .mlr-card-label,
            #mlr-panel .mlr-card:hover .mlr-card-label,
            #mlr-panel .mlr-card:active .mlr-card-label,
            #mlr-panel .mlr-card:focus .mlr-card-label,
            #mlr-panel a.mlr-card:visited .mlr-card-label {
                color: {$card_text} !important;
            }

            /* Cards - border */
            #mlr-panel .mlr-card,
            #mlr-panel .mlr-card:hover,
            #mlr-panel .mlr-card:active,
            #mlr-panel .mlr-card:focus,
            #mlr-panel a.mlr-card:visited {
                border-color: {$card_border} !important;
            }

            /* Cards link - no color de texto del tema */
            #mlr-panel a.mlr-card,
            #mlr-panel a.mlr-card:link,
            #mlr-panel a.mlr-card:visited,
            #mlr-panel a.mlr-card:active,
            #mlr-panel a.mlr-card:focus,
            #mlr-panel a.mlr-card:hover {
                text-decoration: none !important;
            }

            /* Submenu category titles */
            #mlr-panel .mlr-submenu-cat-title {
                color: {$sub_cat_color} !important;
            }

            /* Submenu links - todos los estados */
            #mlr-panel .mlr-submenu-links a,
            #mlr-panel .mlr-submenu-links a:link,
            #mlr-panel .mlr-submenu-links a:visited,
            #mlr-panel .mlr-submenu-links a:active,
            #mlr-panel .mlr-submenu-links a:focus {
                color: {$sub_link_color} !important;
                text-decoration: none !important;
            }
            #mlr-panel .mlr-submenu-links a:hover {
                color: {$sub_link_hover} !important;
            }

            /* Active indicator */
            #mlr-panel .mlr-submenu-content.mlr-submenu-visible {
                border-color: {$card_indicator} !important;
            }

            /* Back button */
            #mlr-panel .mlr-submenu-back,
            #mlr-panel .mlr-submenu-back:hover,
            #mlr-panel .mlr-submenu-back:active,
            #mlr-panel .mlr-submenu-back:focus {
                color: {$sub_cat_color} !important;
            }
        ";

        return $css;
    }

    public function init_elementor_widget() {
        if ( did_action( 'elementor/loaded' ) ) {
            require_once MLR_PLUGIN_DIR . 'elementor/class-mlr-elementor.php';
            MLR_Elementor::get_instance();
        }
    }
}

function mlr_init() {
    return Menu_Lateral_Responsive::get_instance();
}

mlr_init();
