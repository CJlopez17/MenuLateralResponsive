<?php
/**
 * Plugin Name: Menu Lateral Responsive
 * Plugin URI: https://github.com/CJlopez17/MenuLateralResponsive
 * Description: Plugin de menú lateral responsive compatible con Elementor. Disponible como shortcode [menu_lateral] y como widget de Elementor.
 * Version: 2.0.0
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

define( 'MLR_VERSION', '2.0.0' );
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
        require_once MLR_PLUGIN_DIR . 'includes/class-mlr-walker-nav-menu.php';
        require_once MLR_PLUGIN_DIR . 'includes/class-mlr-nav-menu-icon-field.php';
        require_once MLR_PLUGIN_DIR . 'admin/class-mlr-admin.php';
    }

    private function set_hooks() {
        register_activation_hook( __FILE__, array( 'MLR_Activator', 'activate' ) );
        register_deactivation_hook( __FILE__, array( 'MLR_Activator', 'deactivate' ) );

        add_action( 'init', array( $this, 'register_menu_locations' ) );
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

    /**
     * Registra dos ubicaciones de menú:
     * 1. Links superiores (header púrpura): "Seguridad", "Blog", "Contáctanos"
     * 2. Tarjetas principales (body blanco): "Productos", "Canales", etc.
     */
    public function register_menu_locations() {
        register_nav_menus( array(
            'mlr_top_links'  => esc_html__( 'Menu Lateral - Links superiores', 'menu-lateral-responsive' ),
            'mlr_card_items' => esc_html__( 'Menu Lateral - Tarjetas principales', 'menu-lateral-responsive' ),
        ) );
    }

    public function enqueue_frontend_assets() {
        wp_enqueue_style(
            'mlr-styles',
            MLR_PLUGIN_URL . 'assets/css/mlr-styles.css',
            array(),
            MLR_VERSION
        );

        wp_enqueue_script(
            'mlr-scripts',
            MLR_PLUGIN_URL . 'assets/js/mlr-scripts.js',
            array(),
            MLR_VERSION,
            true
        );

        $options = get_option( 'mlr_options', array() );

        wp_localize_script( 'mlr-scripts', 'mlrConfig', array(
            'animationSpeed' => 400,
        ) );
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
