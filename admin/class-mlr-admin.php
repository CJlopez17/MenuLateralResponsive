<?php
/**
 * Clase de administración del plugin.
 * Gestiona la configuración de apariencia y los items del menú.
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
        add_action( 'wp_ajax_mlr_save_menu_data', array( $this, 'ajax_save_menu_data' ) );
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
        add_settings_field( 'mlr_panel_width', esc_html__( 'Ancho del panel (px)', 'menu-lateral-responsive' ), array( $this, 'render_number_field' ), 'mlr-settings', 'mlr_general_section', array( 'field' => 'panel_width', 'default' => '280', 'min' => 220, 'max' => 600 ) );
        add_settings_field( 'mlr_submenu_width', esc_html__( 'Ancho con submenú abierto (px)', 'menu-lateral-responsive' ), array( $this, 'render_number_field' ), 'mlr-settings', 'mlr_general_section', array( 'field' => 'submenu_width', 'default' => '520', 'min' => 400, 'max' => 800 ) );

        // Sección: Header
        add_settings_section(
            'mlr_header_section',
            esc_html__( 'Header (zona superior)', 'menu-lateral-responsive' ),
            function () {
                echo '<p>' . esc_html__( 'Colores del header que contiene el título y los links superiores.', 'menu-lateral-responsive' ) . '</p>';
            },
            'mlr-settings'
        );

        add_settings_field( 'mlr_header_color', esc_html__( 'Color de fondo del header', 'menu-lateral-responsive' ), array( $this, 'render_color_field' ), 'mlr-settings', 'mlr_header_section', array( 'field' => 'header_color', 'default' => '#BA00F7' ) );
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

        add_settings_field( 'mlr_card_border', esc_html__( 'Color del borde', 'menu-lateral-responsive' ), array( $this, 'render_color_field' ), 'mlr-settings', 'mlr_cards_section', array( 'field' => 'card_border', 'default' => '#BCA4FD' ) );
        add_settings_field( 'mlr_card_icon_color', esc_html__( 'Color de los iconos', 'menu-lateral-responsive' ), array( $this, 'render_color_field' ), 'mlr-settings', 'mlr_cards_section', array( 'field' => 'card_icon_color', 'default' => '#D200FF' ) );
        add_settings_field( 'mlr_card_text_color', esc_html__( 'Color del texto', 'menu-lateral-responsive' ), array( $this, 'render_color_field' ), 'mlr-settings', 'mlr_cards_section', array( 'field' => 'card_text_color', 'default' => '#2D1361' ) );
        add_settings_field( 'mlr_card_bg_hover', esc_html__( 'Color fondo hover', 'menu-lateral-responsive' ), array( $this, 'render_color_field' ), 'mlr-settings', 'mlr_cards_section', array( 'field' => 'card_bg_hover', 'default' => '#FFFFFF' ) );

        // Sección: Submenú
        add_settings_section(
            'mlr_submenu_section',
            esc_html__( 'Submenú', 'menu-lateral-responsive' ),
            function () {
                echo '<p>' . esc_html__( 'Colores del panel de submenú que se despliega al hacer clic en una tarjeta.', 'menu-lateral-responsive' ) . '</p>';
            },
            'mlr-settings'
        );

        add_settings_field( 'mlr_submenu_cat_color', esc_html__( 'Color títulos de categoría', 'menu-lateral-responsive' ), array( $this, 'render_color_field' ), 'mlr-settings', 'mlr_submenu_section', array( 'field' => 'submenu_cat_color', 'default' => '#5F17DC' ) );
        add_settings_field( 'mlr_submenu_link_color', esc_html__( 'Color de links', 'menu-lateral-responsive' ), array( $this, 'render_color_field' ), 'mlr-settings', 'mlr_submenu_section', array( 'field' => 'submenu_link_color', 'default' => '#656565' ) );
        add_settings_field( 'mlr_submenu_link_hover', esc_html__( 'Color de links (hover)', 'menu-lateral-responsive' ), array( $this, 'render_color_field' ), 'mlr-settings', 'mlr_submenu_section', array( 'field' => 'submenu_link_hover', 'default' => '#BA00F7' ) );

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
        $s['menu_title']         = sanitize_text_field( $input['menu_title'] );
        $s['panel_width']        = max( 220, min( 600, absint( $input['panel_width'] ) ) );
        $s['submenu_width']      = max( 400, min( 800, absint( $input['submenu_width'] ) ) );
        $s['header_color']       = sanitize_hex_color( $input['header_color'] );
        $s['header_text']        = sanitize_hex_color( $input['header_text'] );
        $s['card_border']        = sanitize_hex_color( $input['card_border'] );
        $s['card_icon_color']    = sanitize_hex_color( $input['card_icon_color'] );
        $s['card_text_color']    = sanitize_hex_color( $input['card_text_color'] );
        $s['card_bg_hover']      = sanitize_hex_color( $input['card_bg_hover'] );
        $s['card_active_indicator'] = sanitize_hex_color( $input['card_active_indicator'] );
        $s['submenu_cat_color']  = sanitize_hex_color( $input['submenu_cat_color'] );
        $s['submenu_link_color'] = sanitize_hex_color( $input['submenu_link_color'] );
        $s['submenu_link_hover'] = sanitize_hex_color( $input['submenu_link_hover'] );
        $s['overlay_opacity']    = max( 0.1, min( 0.9, floatval( $input['overlay_opacity'] ) ) );
        return $s;
    }

    public function enqueue_admin_assets( $hook ) {
        if ( 'toplevel_page_mlr-settings' !== $hook ) {
            return;
        }
        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_style( 'mlr-admin-styles', MLR_PLUGIN_URL . 'admin/css/mlr-admin.css', array(), MLR_VERSION );
        wp_enqueue_script( 'mlr-admin-scripts', MLR_PLUGIN_URL . 'admin/js/mlr-admin.js', array( 'wp-color-picker', 'jquery' ), MLR_VERSION, true );

        $menu_data = get_option( 'mlr_menu_data', array( 'top_links' => array(), 'cards' => array() ) );
        $icons_list = array( 'grid', 'screen', 'heart', 'building', 'money', 'card', 'phone', 'mail', 'user', 'settings', 'chart', 'shield' );

        wp_localize_script( 'mlr-admin-scripts', 'mlrAdminData', array(
            'menuData'  => $menu_data,
            'icons'     => $icons_list,
            'nonce'     => wp_create_nonce( 'mlr_save_menu_data' ),
            'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
            'i18n'      => array(
                'saveSuccess'     => esc_html__( 'Menú guardado correctamente.', 'menu-lateral-responsive' ),
                'saveError'       => esc_html__( 'Error al guardar el menú.', 'menu-lateral-responsive' ),
                'confirmDelete'   => esc_html__( '¿Estás seguro de eliminar este elemento?', 'menu-lateral-responsive' ),
                'addTopLink'      => esc_html__( 'Agregar link', 'menu-lateral-responsive' ),
                'addCard'         => esc_html__( 'Agregar tarjeta', 'menu-lateral-responsive' ),
                'addCategory'     => esc_html__( 'Agregar categoría', 'menu-lateral-responsive' ),
                'addLink'         => esc_html__( 'Agregar link', 'menu-lateral-responsive' ),
                'title'           => esc_html__( 'Título', 'menu-lateral-responsive' ),
                'url'             => esc_html__( 'URL', 'menu-lateral-responsive' ),
                'icon'            => esc_html__( 'Icono', 'menu-lateral-responsive' ),
                'uploadIcon'      => esc_html__( 'Subir imagen', 'menu-lateral-responsive' ),
                'removeIcon'      => esc_html__( 'Quitar', 'menu-lateral-responsive' ),
                'remove'          => esc_html__( 'Eliminar', 'menu-lateral-responsive' ),
                'categories'      => esc_html__( 'Categorías', 'menu-lateral-responsive' ),
                'links'           => esc_html__( 'Links', 'menu-lateral-responsive' ),
                'color'           => esc_html__( 'Color', 'menu-lateral-responsive' ),
                'builtinIcon'     => esc_html__( 'Icono integrado', 'menu-lateral-responsive' ),
                'customImage'     => esc_html__( 'Imagen personalizada', 'menu-lateral-responsive' ),
                'noIcon'          => esc_html__( 'Sin icono personalizado', 'menu-lateral-responsive' ),
                'selectImage'     => esc_html__( 'Seleccionar imagen', 'menu-lateral-responsive' ),
                'useImage'        => esc_html__( 'Usar esta imagen', 'menu-lateral-responsive' ),
                'saving'          => esc_html__( 'Guardando...', 'menu-lateral-responsive' ),
                'save'            => esc_html__( 'Guardar menú', 'menu-lateral-responsive' ),
                'noCards'         => esc_html__( 'No hay tarjetas configuradas. Agrega una para comenzar.', 'menu-lateral-responsive' ),
                'noTopLinks'      => esc_html__( 'No hay links configurados. Agrega uno para comenzar.', 'menu-lateral-responsive' ),
                'noCategories'    => esc_html__( 'No hay categorías. Agrega una para crear el submenú de esta tarjeta.', 'menu-lateral-responsive' ),
                'noLinks'         => esc_html__( 'No hay links en esta categoría.', 'menu-lateral-responsive' ),
                'moveUp'          => esc_html__( 'Subir', 'menu-lateral-responsive' ),
                'moveDown'        => esc_html__( 'Bajar', 'menu-lateral-responsive' ),
            ),
        ) );
    }

    /**
     * AJAX handler para guardar datos del menú.
     */
    public function ajax_save_menu_data() {
        check_ajax_referer( 'mlr_save_menu_data', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }

        $raw = isset( $_POST['menu_data'] ) ? $_POST['menu_data'] : '';
        $data = json_decode( wp_unslash( $raw ), true );

        if ( ! is_array( $data ) ) {
            wp_send_json_error( array( 'message' => 'Invalid data format' ) );
        }

        $sanitized = $this->sanitize_menu_data( $data );
        update_option( 'mlr_menu_data', $sanitized );

        wp_send_json_success( array( 'message' => 'Saved' ) );
    }

    /**
     * Sanitiza recursivamente los datos del menú.
     */
    private function sanitize_menu_data( $data ) {
        $clean = array(
            'top_links' => array(),
            'cards'     => array(),
        );

        // Top links
        if ( ! empty( $data['top_links'] ) && is_array( $data['top_links'] ) ) {
            foreach ( $data['top_links'] as $link ) {
                if ( empty( $link['title'] ) ) continue;
                $clean['top_links'][] = array(
                    'title' => sanitize_text_field( $link['title'] ),
                    'url'   => esc_url_raw( $link['url'] ),
                );
            }
        }

        // Cards
        if ( ! empty( $data['cards'] ) && is_array( $data['cards'] ) ) {
            foreach ( $data['cards'] as $card ) {
                if ( empty( $card['title'] ) ) continue;
                $clean_card = array(
                    'title'      => sanitize_text_field( $card['title'] ),
                    'url'        => esc_url_raw( isset( $card['url'] ) ? $card['url'] : '' ),
                    'icon_type'  => in_array( $card['icon_type'], array( 'builtin', 'custom' ), true ) ? $card['icon_type'] : 'builtin',
                    'icon_name'  => sanitize_text_field( isset( $card['icon_name'] ) ? $card['icon_name'] : 'grid' ),
                    'icon_url'   => esc_url_raw( isset( $card['icon_url'] ) ? $card['icon_url'] : '' ),
                    'categories' => array(),
                );

                if ( ! empty( $card['categories'] ) && is_array( $card['categories'] ) ) {
                    foreach ( $card['categories'] as $cat ) {
                        if ( empty( $cat['title'] ) ) continue;
                        $clean_cat = array(
                            'title' => sanitize_text_field( $cat['title'] ),
                            'color' => sanitize_hex_color( isset( $cat['color'] ) ? $cat['color'] : '#7B2D8E' ),
                            'links' => array(),
                        );

                        if ( ! empty( $cat['links'] ) && is_array( $cat['links'] ) ) {
                            foreach ( $cat['links'] as $lnk ) {
                                if ( empty( $lnk['title'] ) ) continue;
                                $clean_cat['links'][] = array(
                                    'title' => sanitize_text_field( $lnk['title'] ),
                                    'url'   => esc_url_raw( $lnk['url'] ),
                                );
                            }
                        }

                        $clean_card['categories'][] = $clean_cat;
                    }
                }

                $clean['cards'][] = $clean_card;
            }
        }

        return $clean;
    }

    public function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'appearance';
        ?>
        <div class="wrap mlr-admin-wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

            <div class="mlr-admin-header">
                <p><?php esc_html_e( 'Configura la apariencia y los items del menú lateral. Todo se gestiona desde aquí.', 'menu-lateral-responsive' ); ?></p>
                <div class="mlr-shortcode-info">
                    <strong><?php esc_html_e( 'Shortcode:', 'menu-lateral-responsive' ); ?></strong>
                    <code>[menu_lateral]</code>
                </div>
            </div>

            <nav class="nav-tab-wrapper mlr-tabs">
                <a href="?page=mlr-settings&tab=appearance" class="nav-tab <?php echo 'appearance' === $active_tab ? 'nav-tab-active' : ''; ?>">
                    <?php esc_html_e( 'Apariencia', 'menu-lateral-responsive' ); ?>
                </a>
                <a href="?page=mlr-settings&tab=top_links" class="nav-tab <?php echo 'top_links' === $active_tab ? 'nav-tab-active' : ''; ?>">
                    <?php esc_html_e( 'Links superiores', 'menu-lateral-responsive' ); ?>
                </a>
                <a href="?page=mlr-settings&tab=cards" class="nav-tab <?php echo 'cards' === $active_tab ? 'nav-tab-active' : ''; ?>">
                    <?php esc_html_e( 'Tarjetas y submenús', 'menu-lateral-responsive' ); ?>
                </a>
            </nav>

            <div class="mlr-tab-content">
                <?php
                switch ( $active_tab ) {
                    case 'top_links':
                        $this->render_top_links_tab();
                        break;
                    case 'cards':
                        $this->render_cards_tab();
                        break;
                    default:
                        $this->render_appearance_tab();
                        break;
                }
                ?>
            </div>
        </div>
        <?php
    }

    private function render_appearance_tab() {
        ?>
        <form action="options.php" method="post">
            <?php
            settings_fields( 'mlr_settings_group' );
            do_settings_sections( 'mlr-settings' );
            submit_button( esc_html__( 'Guardar cambios', 'menu-lateral-responsive' ) );
            ?>
        </form>
        <?php
    }

    private function render_top_links_tab() {
        ?>
        <div id="mlr-top-links-manager" class="mlr-manager-section">
            <p class="description"><?php esc_html_e( 'Administra los links que aparecen en el header del menú (zona superior). Estos son links simples de navegación.', 'menu-lateral-responsive' ); ?></p>
            <div id="mlr-top-links-list"></div>
            <div class="mlr-manager-actions">
                <button type="button" id="mlr-add-top-link" class="button button-secondary">
                    <span class="dashicons dashicons-plus-alt2"></span>
                    <?php esc_html_e( 'Agregar link', 'menu-lateral-responsive' ); ?>
                </button>
                <button type="button" id="mlr-save-top-links" class="button button-primary mlr-save-btn">
                    <?php esc_html_e( 'Guardar links', 'menu-lateral-responsive' ); ?>
                </button>
            </div>
            <div id="mlr-save-message" class="mlr-save-message"></div>
        </div>
        <?php
    }

    private function render_cards_tab() {
        ?>
        <div id="mlr-cards-manager" class="mlr-manager-section">
            <p class="description"><?php esc_html_e( 'Administra las tarjetas del menú y sus submenús. Cada tarjeta puede tener categorías con links que se despliegan al hacer clic.', 'menu-lateral-responsive' ); ?></p>
            <div id="mlr-cards-list"></div>
            <div class="mlr-manager-actions">
                <button type="button" id="mlr-add-card" class="button button-secondary">
                    <span class="dashicons dashicons-plus-alt2"></span>
                    <?php esc_html_e( 'Agregar tarjeta', 'menu-lateral-responsive' ); ?>
                </button>
                <button type="button" id="mlr-save-cards" class="button button-primary mlr-save-btn">
                    <?php esc_html_e( 'Guardar tarjetas', 'menu-lateral-responsive' ); ?>
                </button>
            </div>
            <div id="mlr-save-message-cards" class="mlr-save-message"></div>
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
