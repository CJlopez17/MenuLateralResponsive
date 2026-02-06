<?php
/**
 * Plugin Name: Menu Lateral Responsive
 * Plugin URI: https://github.com/CJlopez17/MenuLateralResponsive
 * Description: Plugin de menú lateral responsive compatible con Elementor. Disponible como shortcode [menu_lateral] y como widget de Elementor.
 * Version: 1.0.0
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

define( 'MLR_VERSION', '1.0.0' );
define( 'MLR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MLR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MLR_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Clase principal del plugin Menu Lateral Responsive.
 */
final class Menu_Lateral_Responsive {

    /**
     * Instancia única del plugin.
     *
     * @var Menu_Lateral_Responsive|null
     */
    private static $instance = null;

    /**
     * Retorna la instancia única del plugin (Singleton).
     *
     * @return Menu_Lateral_Responsive
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor privado.
     */
    private function __construct() {
        $this->load_dependencies();
        $this->set_hooks();
    }

    /**
     * Carga los archivos necesarios del plugin.
     */
    private function load_dependencies() {
        require_once MLR_PLUGIN_DIR . 'includes/class-mlr-activator.php';
        require_once MLR_PLUGIN_DIR . 'includes/class-mlr-shortcode.php';
        require_once MLR_PLUGIN_DIR . 'includes/class-mlr-walker-nav-menu.php';
        require_once MLR_PLUGIN_DIR . 'admin/class-mlr-admin.php';
    }

    /**
     * Registra los hooks principales del plugin.
     */
    private function set_hooks() {
        register_activation_hook( __FILE__, array( 'MLR_Activator', 'activate' ) );
        register_deactivation_hook( __FILE__, array( 'MLR_Activator', 'deactivate' ) );

        add_action( 'init', array( $this, 'register_menu_location' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
        add_action( 'plugins_loaded', array( $this, 'init_elementor_widget' ) );
    }

    /**
     * Carga el textdomain para traducciones.
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'menu-lateral-responsive',
            false,
            dirname( MLR_PLUGIN_BASENAME ) . '/languages'
        );
    }

    /**
     * Registra la ubicación del menú en WordPress.
     */
    public function register_menu_location() {
        register_nav_menus( array(
            'mlr_sidebar_menu' => esc_html__( 'Menú Lateral Responsive', 'menu-lateral-responsive' ),
        ) );
    }

    /**
     * Encola los assets del frontend.
     */
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
            'menuPosition'  => isset( $options['position'] ) ? $options['position'] : 'left',
            'overlayColor'  => isset( $options['overlay_color'] ) ? $options['overlay_color'] : 'rgba(0,0,0,0.5)',
            'closeOnOverlay' => isset( $options['close_on_overlay'] ) ? (bool) $options['close_on_overlay'] : true,
        ) );
    }

    /**
     * Inicializa el widget de Elementor si Elementor está activo.
     */
    public function init_elementor_widget() {
        if ( did_action( 'elementor/loaded' ) ) {
            require_once MLR_PLUGIN_DIR . 'elementor/class-mlr-elementor.php';
            MLR_Elementor::get_instance();
        }
    }
}

/**
 * Inicializa el plugin.
 *
 * @return Menu_Lateral_Responsive
 */
function mlr_init() {
    return Menu_Lateral_Responsive::get_instance();
}

mlr_init();
