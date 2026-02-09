<?php
/**
 * Integración con Elementor.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MLR_Elementor {

    /**
     * Instancia única.
     *
     * @var MLR_Elementor|null
     */
    private static $instance = null;

    /**
     * Retorna la instancia única.
     *
     * @return MLR_Elementor
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
    private function __construct() {
        add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
        add_action( 'elementor/elements/categories_registered', array( $this, 'add_widget_category' ) );
    }

    /**
     * Registra la categoría del widget en Elementor.
     *
     * @param \Elementor\Elements_Manager $elements_manager Manager de elementos.
     */
    public function add_widget_category( $elements_manager ) {
        $elements_manager->add_category(
            'mlr-category',
            array(
                'title' => esc_html__( 'Menu Lateral Responsive', 'menu-lateral-responsive' ),
                'icon'  => 'fa fa-bars',
            )
        );
    }

    /**
     * Registra los widgets de Elementor.
     *
     * @param \Elementor\Widgets_Manager $widgets_manager Manager de widgets.
     */
    public function register_widgets( $widgets_manager ) {
        require_once MLR_PLUGIN_DIR . 'elementor/class-mlr-elementor-widget.php';
        $widgets_manager->register( new MLR_Elementor_Widget() );
    }
}
