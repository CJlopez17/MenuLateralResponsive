<?php
/**
 * Clase de administración del plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MLR_Admin {

    /**
     * Instancia única.
     *
     * @var MLR_Admin|null
     */
    private static $instance = null;

    /**
     * Retorna la instancia única.
     *
     * @return MLR_Admin
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
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
    }

    /**
     * Agrega la página del menú de administración.
     */
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

    /**
     * Registra los ajustes del plugin.
     */
    public function register_settings() {
        register_setting( 'mlr_settings_group', 'mlr_options', array( $this, 'sanitize_options' ) );

        // Sección: Apariencia
        add_settings_section(
            'mlr_appearance_section',
            esc_html__( 'Apariencia', 'menu-lateral-responsive' ),
            array( $this, 'render_appearance_section' ),
            'mlr-settings'
        );

        add_settings_field(
            'mlr_position',
            esc_html__( 'Posición del menú', 'menu-lateral-responsive' ),
            array( $this, 'render_position_field' ),
            'mlr-settings',
            'mlr_appearance_section'
        );

        add_settings_field(
            'mlr_width',
            esc_html__( 'Ancho del menú (px)', 'menu-lateral-responsive' ),
            array( $this, 'render_width_field' ),
            'mlr-settings',
            'mlr_appearance_section'
        );

        add_settings_field(
            'mlr_bg_color',
            esc_html__( 'Color de fondo', 'menu-lateral-responsive' ),
            array( $this, 'render_bg_color_field' ),
            'mlr-settings',
            'mlr_appearance_section'
        );

        add_settings_field(
            'mlr_text_color',
            esc_html__( 'Color del texto', 'menu-lateral-responsive' ),
            array( $this, 'render_text_color_field' ),
            'mlr-settings',
            'mlr_appearance_section'
        );

        add_settings_field(
            'mlr_hover_color',
            esc_html__( 'Color hover', 'menu-lateral-responsive' ),
            array( $this, 'render_hover_color_field' ),
            'mlr-settings',
            'mlr_appearance_section'
        );

        add_settings_field(
            'mlr_accent_color',
            esc_html__( 'Color de acento', 'menu-lateral-responsive' ),
            array( $this, 'render_accent_color_field' ),
            'mlr-settings',
            'mlr_appearance_section'
        );

        // Sección: Comportamiento
        add_settings_section(
            'mlr_behavior_section',
            esc_html__( 'Comportamiento', 'menu-lateral-responsive' ),
            array( $this, 'render_behavior_section' ),
            'mlr-settings'
        );

        add_settings_field(
            'mlr_overlay_color',
            esc_html__( 'Color del overlay', 'menu-lateral-responsive' ),
            array( $this, 'render_overlay_color_field' ),
            'mlr-settings',
            'mlr_behavior_section'
        );

        add_settings_field(
            'mlr_close_on_overlay',
            esc_html__( 'Cerrar al hacer click en overlay', 'menu-lateral-responsive' ),
            array( $this, 'render_close_on_overlay_field' ),
            'mlr-settings',
            'mlr_behavior_section'
        );

        // Sección: Logo
        add_settings_section(
            'mlr_logo_section',
            esc_html__( 'Logo', 'menu-lateral-responsive' ),
            array( $this, 'render_logo_section' ),
            'mlr-settings'
        );

        add_settings_field(
            'mlr_show_logo',
            esc_html__( 'Mostrar logo', 'menu-lateral-responsive' ),
            array( $this, 'render_show_logo_field' ),
            'mlr-settings',
            'mlr_logo_section'
        );

        add_settings_field(
            'mlr_logo_url',
            esc_html__( 'URL del logo', 'menu-lateral-responsive' ),
            array( $this, 'render_logo_url_field' ),
            'mlr-settings',
            'mlr_logo_section'
        );
    }

    /**
     * Sanitiza las opciones antes de guardar.
     *
     * @param array $input Opciones enviadas.
     * @return array Opciones sanitizadas.
     */
    public function sanitize_options( $input ) {
        $sanitized = array();

        $sanitized['position']        = in_array( $input['position'], array( 'left', 'right' ), true ) ? $input['position'] : 'left';
        $sanitized['width']           = absint( $input['width'] );
        $sanitized['bg_color']        = sanitize_hex_color( $input['bg_color'] );
        $sanitized['text_color']      = sanitize_hex_color( $input['text_color'] );
        $sanitized['hover_color']     = sanitize_hex_color( $input['hover_color'] );
        $sanitized['accent_color']    = sanitize_hex_color( $input['accent_color'] );
        $sanitized['overlay_color']   = sanitize_text_field( $input['overlay_color'] );
        $sanitized['close_on_overlay'] = ! empty( $input['close_on_overlay'] );
        $sanitized['show_logo']       = ! empty( $input['show_logo'] );
        $sanitized['logo_url']        = esc_url_raw( $input['logo_url'] );

        if ( $sanitized['width'] < 200 ) {
            $sanitized['width'] = 200;
        }
        if ( $sanitized['width'] > 600 ) {
            $sanitized['width'] = 600;
        }

        return $sanitized;
    }

    /**
     * Encola los assets de la página de administración.
     *
     * @param string $hook Hook de la página actual.
     */
    public function enqueue_admin_assets( $hook ) {
        if ( 'toplevel_page_mlr-settings' !== $hook ) {
            return;
        }

        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_media();

        wp_enqueue_style(
            'mlr-admin-styles',
            MLR_PLUGIN_URL . 'admin/css/mlr-admin.css',
            array(),
            MLR_VERSION
        );

        wp_enqueue_script(
            'mlr-admin-scripts',
            MLR_PLUGIN_URL . 'admin/js/mlr-admin.js',
            array( 'wp-color-picker', 'jquery' ),
            MLR_VERSION,
            true
        );
    }

    /**
     * Renderiza la página de ajustes.
     */
    public function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ?>
        <div class="wrap mlr-admin-wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

            <div class="mlr-admin-header">
                <p><?php esc_html_e( 'Configura tu menú lateral responsive. Usa el shortcode [menu_lateral] para insertarlo en cualquier página o el widget de Elementor.', 'menu-lateral-responsive' ); ?></p>
                <div class="mlr-shortcode-info">
                    <strong><?php esc_html_e( 'Shortcode:', 'menu-lateral-responsive' ); ?></strong>
                    <code>[menu_lateral]</code>
                    <br>
                    <strong><?php esc_html_e( 'Con parámetros:', 'menu-lateral-responsive' ); ?></strong>
                    <code>[menu_lateral position="left" width="300" theme="dark"]</code>
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

    // --- Callbacks de secciones ---

    public function render_appearance_section() {
        echo '<p>' . esc_html__( 'Personaliza la apariencia visual del menú lateral.', 'menu-lateral-responsive' ) . '</p>';
    }

    public function render_behavior_section() {
        echo '<p>' . esc_html__( 'Configura el comportamiento del menú.', 'menu-lateral-responsive' ) . '</p>';
    }

    public function render_logo_section() {
        echo '<p>' . esc_html__( 'Agrega un logo en la parte superior del menú.', 'menu-lateral-responsive' ) . '</p>';
    }

    // --- Callbacks de campos ---

    public function render_position_field() {
        $options  = get_option( 'mlr_options', array() );
        $position = isset( $options['position'] ) ? $options['position'] : 'left';
        ?>
        <select name="mlr_options[position]" id="mlr_position">
            <option value="left" <?php selected( $position, 'left' ); ?>><?php esc_html_e( 'Izquierda', 'menu-lateral-responsive' ); ?></option>
            <option value="right" <?php selected( $position, 'right' ); ?>><?php esc_html_e( 'Derecha', 'menu-lateral-responsive' ); ?></option>
        </select>
        <?php
    }

    public function render_width_field() {
        $options = get_option( 'mlr_options', array() );
        $width   = isset( $options['width'] ) ? $options['width'] : '300';
        ?>
        <input type="number" name="mlr_options[width]" id="mlr_width" value="<?php echo esc_attr( $width ); ?>" min="200" max="600" step="10">
        <p class="description"><?php esc_html_e( 'Mínimo 200px, máximo 600px.', 'menu-lateral-responsive' ); ?></p>
        <?php
    }

    public function render_bg_color_field() {
        $options  = get_option( 'mlr_options', array() );
        $bg_color = isset( $options['bg_color'] ) ? $options['bg_color'] : '#1a1a2e';
        ?>
        <input type="text" name="mlr_options[bg_color]" id="mlr_bg_color" value="<?php echo esc_attr( $bg_color ); ?>" class="mlr-color-picker">
        <?php
    }

    public function render_text_color_field() {
        $options    = get_option( 'mlr_options', array() );
        $text_color = isset( $options['text_color'] ) ? $options['text_color'] : '#ffffff';
        ?>
        <input type="text" name="mlr_options[text_color]" id="mlr_text_color" value="<?php echo esc_attr( $text_color ); ?>" class="mlr-color-picker">
        <?php
    }

    public function render_hover_color_field() {
        $options     = get_option( 'mlr_options', array() );
        $hover_color = isset( $options['hover_color'] ) ? $options['hover_color'] : '#16213e';
        ?>
        <input type="text" name="mlr_options[hover_color]" id="mlr_hover_color" value="<?php echo esc_attr( $hover_color ); ?>" class="mlr-color-picker">
        <?php
    }

    public function render_accent_color_field() {
        $options      = get_option( 'mlr_options', array() );
        $accent_color = isset( $options['accent_color'] ) ? $options['accent_color'] : '#0f3460';
        ?>
        <input type="text" name="mlr_options[accent_color]" id="mlr_accent_color" value="<?php echo esc_attr( $accent_color ); ?>" class="mlr-color-picker">
        <?php
    }

    public function render_overlay_color_field() {
        $options       = get_option( 'mlr_options', array() );
        $overlay_color = isset( $options['overlay_color'] ) ? $options['overlay_color'] : 'rgba(0,0,0,0.5)';
        ?>
        <input type="text" name="mlr_options[overlay_color]" id="mlr_overlay_color" value="<?php echo esc_attr( $overlay_color ); ?>">
        <p class="description"><?php esc_html_e( 'Usa formato rgba, ej: rgba(0,0,0,0.5)', 'menu-lateral-responsive' ); ?></p>
        <?php
    }

    public function render_close_on_overlay_field() {
        $options          = get_option( 'mlr_options', array() );
        $close_on_overlay = isset( $options['close_on_overlay'] ) ? (bool) $options['close_on_overlay'] : true;
        ?>
        <label>
            <input type="checkbox" name="mlr_options[close_on_overlay]" id="mlr_close_on_overlay" value="1" <?php checked( $close_on_overlay ); ?>>
            <?php esc_html_e( 'Cerrar el menú cuando se hace click fuera de él.', 'menu-lateral-responsive' ); ?>
        </label>
        <?php
    }

    public function render_show_logo_field() {
        $options   = get_option( 'mlr_options', array() );
        $show_logo = isset( $options['show_logo'] ) ? (bool) $options['show_logo'] : false;
        ?>
        <label>
            <input type="checkbox" name="mlr_options[show_logo]" id="mlr_show_logo" value="1" <?php checked( $show_logo ); ?>>
            <?php esc_html_e( 'Mostrar un logo en la parte superior del menú.', 'menu-lateral-responsive' ); ?>
        </label>
        <?php
    }

    public function render_logo_url_field() {
        $options  = get_option( 'mlr_options', array() );
        $logo_url = isset( $options['logo_url'] ) ? $options['logo_url'] : '';
        ?>
        <input type="text" name="mlr_options[logo_url]" id="mlr_logo_url" value="<?php echo esc_url( $logo_url ); ?>" class="regular-text">
        <button type="button" class="button mlr-upload-logo"><?php esc_html_e( 'Seleccionar imagen', 'menu-lateral-responsive' ); ?></button>
        <p class="description"><?php esc_html_e( 'Selecciona o sube una imagen para el logo.', 'menu-lateral-responsive' ); ?></p>
        <?php if ( $logo_url ) : ?>
            <div class="mlr-logo-preview">
                <img src="<?php echo esc_url( $logo_url ); ?>" alt="Logo preview" style="max-width:150px;margin-top:10px;">
            </div>
        <?php endif; ?>
        <?php
    }
}

MLR_Admin::get_instance();
