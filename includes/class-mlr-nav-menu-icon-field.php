<?php
/**
 * Agrega un campo personalizado de icono (upload SVG/PNG) a cada item del menú
 * en la pantalla Apariencia > Menús del admin de WordPress.
 *
 * El usuario puede subir cualquier imagen y el plugin la muestra al tamaño
 * correcto (40x40px) sin importar el tamaño original.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MLR_Nav_Menu_Icon_Field {

    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Mostrar el campo de upload en cada item del menú
        add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'render_icon_field' ), 10, 5 );

        // Guardar el valor al salvar el menú
        add_action( 'wp_update_nav_menu_item', array( $this, 'save_icon_field' ), 10, 3 );

        // Encolar media uploader en la página de menús
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_menu_scripts' ) );
    }

    /**
     * Renderiza el campo de upload de icono dentro de cada item del menú.
     *
     * @param int      $item_id  ID del item del menú.
     * @param WP_Post  $menu_item Objeto del item.
     * @param int      $depth    Profundidad.
     * @param stdClass $args     Argumentos.
     * @param int      $current_object_id ID del objeto actual.
     */
    public function render_icon_field( $item_id, $menu_item, $depth, $args, $current_object_id = 0 ) {
        $icon_url = get_post_meta( $item_id, '_mlr_icon_url', true );
        ?>
        <p class="mlr-menu-icon-field description description-wide">
            <label for="mlr-icon-<?php echo esc_attr( $item_id ); ?>">
                <?php esc_html_e( 'Icono del Menú Lateral (SVG/PNG)', 'menu-lateral-responsive' ); ?>
            </label>
            <span class="mlr-icon-upload-wrap">
                <input
                    type="text"
                    id="mlr-icon-<?php echo esc_attr( $item_id ); ?>"
                    name="mlr_icon_url[<?php echo esc_attr( $item_id ); ?>]"
                    value="<?php echo esc_url( $icon_url ); ?>"
                    class="widefat mlr-icon-url-input"
                    placeholder="<?php esc_attr_e( 'URL del icono o sube uno...', 'menu-lateral-responsive' ); ?>"
                >
                <button type="button" class="button mlr-icon-upload-btn" data-target="mlr-icon-<?php echo esc_attr( $item_id ); ?>">
                    <?php esc_html_e( 'Subir icono', 'menu-lateral-responsive' ); ?>
                </button>
                <button type="button" class="button mlr-icon-remove-btn" data-target="mlr-icon-<?php echo esc_attr( $item_id ); ?>" <?php echo empty( $icon_url ) ? 'style="display:none;"' : ''; ?>>
                    <?php esc_html_e( 'Quitar', 'menu-lateral-responsive' ); ?>
                </button>
            </span>
            <?php if ( $icon_url ) : ?>
                <span class="mlr-icon-preview" id="mlr-icon-preview-<?php echo esc_attr( $item_id ); ?>">
                    <img src="<?php echo esc_url( $icon_url ); ?>" alt="" style="max-width:40px;max-height:40px;margin-top:6px;border:1px solid #ddd;border-radius:4px;padding:3px;background:#f9f9f9;">
                </span>
            <?php else : ?>
                <span class="mlr-icon-preview" id="mlr-icon-preview-<?php echo esc_attr( $item_id ); ?>"></span>
            <?php endif; ?>
        </p>
        <?php
    }

    /**
     * Guarda la URL del icono al actualizar un item del menú.
     *
     * @param int   $menu_id         ID del menú.
     * @param int   $menu_item_db_id ID del item en la base de datos.
     * @param array $args            Argumentos del item.
     */
    public function save_icon_field( $menu_id, $menu_item_db_id, $args ) {
        if ( ! isset( $_POST['mlr_icon_url'] ) ) {
            return;
        }

        $icon_urls = $_POST['mlr_icon_url'];

        if ( isset( $icon_urls[ $menu_item_db_id ] ) ) {
            $url = esc_url_raw( $icon_urls[ $menu_item_db_id ] );
            if ( ! empty( $url ) ) {
                update_post_meta( $menu_item_db_id, '_mlr_icon_url', $url );
            } else {
                delete_post_meta( $menu_item_db_id, '_mlr_icon_url' );
            }
        }
    }

    /**
     * Encola el media uploader y el script del campo de icono en la página de menús.
     *
     * @param string $hook Hook de la página actual.
     */
    public function enqueue_menu_scripts( $hook ) {
        if ( 'nav-menus.php' !== $hook ) {
            return;
        }

        wp_enqueue_media();

        wp_enqueue_script(
            'mlr-nav-menu-icon',
            MLR_PLUGIN_URL . 'admin/js/mlr-nav-menu-icon.js',
            array( 'jquery' ),
            MLR_VERSION,
            true
        );

        wp_localize_script( 'mlr-nav-menu-icon', 'mlrMenuIcon', array(
            'title'  => esc_html__( 'Seleccionar icono', 'menu-lateral-responsive' ),
            'button' => esc_html__( 'Usar este icono', 'menu-lateral-responsive' ),
        ) );

        wp_enqueue_style(
            'mlr-nav-menu-icon-css',
            MLR_PLUGIN_URL . 'admin/css/mlr-nav-menu-icon.css',
            array(),
            MLR_VERSION
        );
    }
}

MLR_Nav_Menu_Icon_Field::get_instance();
