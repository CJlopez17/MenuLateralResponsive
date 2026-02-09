<?php
/**
 * Clase de administración del plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MLR_Admin {

    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
    }

    public function add_admin_menu() {
        add_menu_page(
            esc_html__( 'Menu Lateral Responsive', 'menu-lateral-responsive' ),
            esc_html__( 'Menu Lateral', 'menu-lateral-responsive' ),
            'manage_options',
            'mlr-settings',
            array( $this, 'render_settings_page' ),
            'dashicons-menu-alt3',
            80
        );
    }

    public function register_settings() {
        register_setting( 'mlr_settings_group', 'mlr_options', array( $this, 'sanitize_options' ) );

        // Sección: General
        add_settings_section(
            'mlr_general_section',
            esc_html__( 'General', 'menu-lateral-responsive' ),
            function () {
                echo '<p>' . esc_html__( 'Configuración general del panel lateral.', 'menu-lateral-responsive' ) . '</p>';
            },
            'mlr-settings'
        );

        add_settings_field( 'mlr_menu_title', esc_html__( 'Título del menú', 'menu-lateral-responsive' ), array( $this, 'render_text_field' ), 'mlr-settings', 'mlr_general_section', array( 'field' => 'menu_title', 'default' => 'Menú' ) );
        add_settings_field( 'mlr_panel_width', esc_html__( 'Ancho del panel (px)', 'menu-lateral-responsive' ), array( $this, 'render_number_field' ), 'mlr-settings', 'mlr_general_section', array( 'field' => 'panel_width', 'default' => '280', 'min' => 220, 'max' => 500 ) );

        // Sección: Header
        add_settings_section(
            'mlr_header_section',
            esc_html__( 'Header (zona púrpura)', 'menu-lateral-responsive' ),
            function () {
                echo '<p>' . esc_html__( 'Colores del header que contiene el título y los links superiores.', 'menu-lateral-responsive' ) . '</p>';
            },
            'mlr-settings'
        );

        add_settings_field( 'mlr_header_color', esc_html__( 'Color de fondo del header', 'menu-lateral-responsive' ), array( $this, 'render_color_field' ), 'mlr-settings', 'mlr_header_section', array( 'field' => 'header_color', 'default' => '#7B2D8E' ) );
        add_settings_field( 'mlr_header_text', esc_html__( 'Color del texto del header', 'menu-lateral-responsive' ), array( $this, 'render_color_field' ), 'mlr-settings', 'mlr_header_section', array( 'field' => 'header_text', 'default' => '#ffffff' ) );

        // Sección: Tarjetas
        add_settings_section(
            'mlr_cards_section',
            esc_html__( 'Tarjetas', 'menu-lateral-responsive' ),
            function () {
                echo '<p>' . esc_html__( 'Colores de las tarjetas de navegación principal.', 'menu-lateral-responsive' ) . '</p>';
            },
            'mlr-settings'
        );

        add_settings_field( 'mlr_card_border', esc_html__( 'Color del borde', 'menu-lateral-responsive' ), array( $this, 'render_color_field' ), 'mlr-settings', 'mlr_cards_section', array( 'field' => 'card_border', 'default' => '#7B2D8E' ) );
        add_settings_field( 'mlr_card_icon_color', esc_html__( 'Color de los iconos', 'menu-lateral-responsive' ), array( $this, 'render_color_field' ), 'mlr-settings', 'mlr_cards_section', array( 'field' => 'card_icon_color', 'default' => '#7B2D8E' ) );
        add_settings_field( 'mlr_card_text_color', esc_html__( 'Color del texto', 'menu-lateral-responsive' ), array( $this, 'render_color_field' ), 'mlr-settings', 'mlr_cards_section', array( 'field' => 'card_text_color', 'default' => '#333333' ) );
        add_settings_field( 'mlr_card_bg_hover', esc_html__( 'Color fondo hover', 'menu-lateral-responsive' ), array( $this, 'render_color_field' ), 'mlr-settings', 'mlr_cards_section', array( 'field' => 'card_bg_hover', 'default' => '#f5f0f7' ) );

        // Sección: Overlay
        add_settings_section(
            'mlr_overlay_section',
            esc_html__( 'Overlay', 'menu-lateral-responsive' ),
            function () {
                echo '<p>' . esc_html__( 'El overlay oscurece el fondo y bloquea toda interacción fuera del menú.', 'menu-lateral-responsive' ) . '</p>';
            },
            'mlr-settings'
        );

        add_settings_field( 'mlr_overlay_opacity', esc_html__( 'Opacidad del overlay', 'menu-lateral-responsive' ), array( $this, 'render_range_field' ), 'mlr-settings', 'mlr_overlay_section', array( 'field' => 'overlay_opacity', 'default' => '0.6', 'min' => 0.1, 'max' => 0.9, 'step' => 0.1 ) );
    }

    public function sanitize_options( $input ) {
        $s = array();
        $s['menu_title']      = sanitize_text_field( $input['menu_title'] );
        $s['panel_width']     = max( 220, min( 500, absint( $input['panel_width'] ) ) );
        $s['header_color']    = sanitize_hex_color( $input['header_color'] );
        $s['header_text']     = sanitize_hex_color( $input['header_text'] );
        $s['card_border']     = sanitize_hex_color( $input['card_border'] );
        $s['card_icon_color'] = sanitize_hex_color( $input['card_icon_color'] );
        $s['card_text_color'] = sanitize_hex_color( $input['card_text_color'] );
        $s['card_bg_hover']   = sanitize_hex_color( $input['card_bg_hover'] );
        $s['overlay_opacity'] = max( 0.1, min( 0.9, floatval( $input['overlay_opacity'] ) ) );
        return $s;
    }

    public function enqueue_admin_assets( $hook ) {
        if ( 'toplevel_page_mlr-settings' !== $hook ) {
            return;
        }
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_style( 'mlr-admin-styles', MLR_PLUGIN_URL . 'admin/css/mlr-admin.css', array(), MLR_VERSION );
        wp_enqueue_script( 'mlr-admin-scripts', MLR_PLUGIN_URL . 'admin/js/mlr-admin.js', array( 'wp-color-picker', 'jquery' ), MLR_VERSION, true );
    }

    public function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ?>
        <div class="wrap mlr-admin-wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

            <div class="mlr-admin-header">
                <p><?php esc_html_e( 'Configura la apariencia del menú lateral. Los items del menú se configuran en Apariencia > Menús.', 'menu-lateral-responsive' ); ?></p>

                <div class="mlr-shortcode-info">
                    <strong><?php esc_html_e( 'Shortcode:', 'menu-lateral-responsive' ); ?></strong>
                    <code>[menu_lateral]</code>
                </div>

                <div class="mlr-shortcode-info" style="margin-top:8px;">
                    <strong><?php esc_html_e( 'Ubicaciones de menú:', 'menu-lateral-responsive' ); ?></strong><br>
                    <code>Menu Lateral - Links superiores</code> → <?php esc_html_e( 'Links del header púrpura (Seguridad, Blog, etc.)', 'menu-lateral-responsive' ); ?><br>
                    <code>Menu Lateral - Tarjetas principales</code> → <?php esc_html_e( 'Tarjetas con icono (Productos, Canales, etc.)', 'menu-lateral-responsive' ); ?>
                </div>

                <div class="mlr-shortcode-info" style="margin-top:8px;">
                    <strong><?php esc_html_e( 'Iconos para tarjetas:', 'menu-lateral-responsive' ); ?></strong><br>
                    <?php esc_html_e( 'En Apariencia > Menús, agrega una Clase CSS al item:', 'menu-lateral-responsive' ); ?>
                    <code>mlr-icon-grid</code> <code>mlr-icon-screen</code> <code>mlr-icon-heart</code> <code>mlr-icon-building</code>
                    <code>mlr-icon-money</code> <code>mlr-icon-card</code> <code>mlr-icon-phone</code> <code>mlr-icon-mail</code>
                    <code>mlr-icon-user</code> <code>mlr-icon-settings</code> <code>mlr-icon-chart</code> <code>mlr-icon-shield</code>
                </div>
            </div>

            <form action="options.php" method="post">
                <?php
                settings_fields( 'mlr_settings_group' );
                do_settings_sections( 'mlr-settings' );
                submit_button( esc_html__( 'Guardar cambios', 'menu-lateral-responsive' ) );
                ?>
            </form>
        </div>
        <?php
    }

    // --- Render helpers ---

    public function render_text_field( $args ) {
        $options = get_option( 'mlr_options', array() );
        $value   = isset( $options[ $args['field'] ] ) ? $options[ $args['field'] ] : $args['default'];
        echo '<input type="text" name="mlr_options[' . esc_attr( $args['field'] ) . ']" value="' . esc_attr( $value ) . '" class="regular-text">';
    }

    public function render_number_field( $args ) {
        $options = get_option( 'mlr_options', array() );
        $value   = isset( $options[ $args['field'] ] ) ? $options[ $args['field'] ] : $args['default'];
        echo '<input type="number" name="mlr_options[' . esc_attr( $args['field'] ) . ']" value="' . esc_attr( $value ) . '" min="' . esc_attr( $args['min'] ) . '" max="' . esc_attr( $args['max'] ) . '" step="10">';
        echo '<p class="description">' . esc_html( $args['min'] . 'px - ' . $args['max'] . 'px' ) . '</p>';
    }

    public function render_color_field( $args ) {
        $options = get_option( 'mlr_options', array() );
        $value   = isset( $options[ $args['field'] ] ) ? $options[ $args['field'] ] : $args['default'];
        echo '<input type="text" name="mlr_options[' . esc_attr( $args['field'] ) . ']" value="' . esc_attr( $value ) . '" class="mlr-color-picker">';
    }

    public function render_range_field( $args ) {
        $options = get_option( 'mlr_options', array() );
        $value   = isset( $options[ $args['field'] ] ) ? $options[ $args['field'] ] : $args['default'];
        echo '<input type="range" name="mlr_options[' . esc_attr( $args['field'] ) . ']" value="' . esc_attr( $value ) . '" min="' . esc_attr( $args['min'] ) . '" max="' . esc_attr( $args['max'] ) . '" step="' . esc_attr( $args['step'] ) . '" oninput="this.nextElementSibling.textContent=this.value">';
        echo '<span style="margin-left:10px;font-weight:bold;">' . esc_html( $value ) . '</span>';
    }
}

MLR_Admin::get_instance();
