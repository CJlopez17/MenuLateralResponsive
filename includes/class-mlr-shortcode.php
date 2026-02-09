<?php
/**
 * Clase para el registro y renderizado del shortcode.
 *
 * Estructura del panel (basado en diseño MegOnline):
 *  ┌─────────────────────┐
 *  │ ✕  Menú             │  ← Header púrpura
 *  │ Link1 Link2 Link3   │  ← Top links
 *  ├─────────────────────┤
 *  │  ┌──────────────┐   │
 *  │  │   [icono]     │   │  ← Tarjeta
 *  │  │   Texto       │   │
 *  │  └──────────────┘   │
 *  │  ┌──────────────┐   │
 *  │  │   [icono]     │   │
 *  │  │   Texto       │   │
 *  │  └──────────────┘   │
 *  │       ...            │
 *  └─────────────────────┘
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MLR_Shortcode {

    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_shortcode( 'menu_lateral', array( $this, 'render_shortcode' ) );
    }

    public function render_shortcode( $atts ) {
        $options = get_option( 'mlr_options', array() );

        $atts = shortcode_atts( array(
            'title'       => isset( $options['menu_title'] ) ? $options['menu_title'] : 'Menú',
            'width'       => isset( $options['panel_width'] ) ? $options['panel_width'] : '280',
            'top_menu'    => '',
            'card_menu'   => '',
        ), $atts, 'menu_lateral' );

        return self::render_menu( $atts, $options );
    }

    /**
     * Renderiza el HTML del menú lateral completo.
     */
    public static function render_menu( $atts, $options = array() ) {
        if ( empty( $options ) ) {
            $options = get_option( 'mlr_options', array() );
        }

        $title        = esc_html( $atts['title'] );
        $width        = absint( $atts['width'] );
        $header_color = isset( $options['header_color'] ) ? esc_attr( $options['header_color'] ) : '#7B2D8E';
        $header_text  = isset( $options['header_text'] ) ? esc_attr( $options['header_text'] ) : '#ffffff';
        $card_border  = isset( $options['card_border'] ) ? esc_attr( $options['card_border'] ) : '#7B2D8E';
        $card_icon    = isset( $options['card_icon_color'] ) ? esc_attr( $options['card_icon_color'] ) : '#7B2D8E';
        $card_text    = isset( $options['card_text_color'] ) ? esc_attr( $options['card_text_color'] ) : '#333333';
        $card_hover   = isset( $options['card_bg_hover'] ) ? esc_attr( $options['card_bg_hover'] ) : '#f5f0f7';
        $overlay_op   = isset( $options['overlay_opacity'] ) ? esc_attr( $options['overlay_opacity'] ) : '0.6';

        // Top links menu args
        $top_links_args = array(
            'theme_location'  => 'mlr_top_links',
            'container'       => false,
            'menu_class'      => 'mlr-top-links',
            'depth'           => 1,
            'echo'            => false,
            'walker'          => new MLR_Walker_Top_Links(),
            'fallback_cb'     => array( __CLASS__, 'fallback_top_links' ),
        );

        if ( ! empty( $atts['top_menu'] ) ) {
            $top_links_args['menu'] = sanitize_text_field( $atts['top_menu'] );
            unset( $top_links_args['theme_location'] );
        }

        // Card items menu args
        $card_args = array(
            'theme_location'  => 'mlr_card_items',
            'container'       => false,
            'menu_class'      => 'mlr-cards-grid',
            'depth'           => 1,
            'echo'            => false,
            'walker'          => new MLR_Walker_Cards(),
            'fallback_cb'     => array( __CLASS__, 'fallback_cards' ),
        );

        if ( ! empty( $atts['card_menu'] ) ) {
            $card_args['menu'] = sanitize_text_field( $atts['card_menu'] );
            unset( $card_args['theme_location'] );
        }

        ob_start();
        ?>
        <!-- MLR: Botón hamburger para abrir -->
        <button
            class="mlr-toggle-btn"
            aria-label="<?php esc_attr_e( 'Abrir menú', 'menu-lateral-responsive' ); ?>"
            aria-expanded="false"
            aria-controls="mlr-panel"
            style="--mlr-header-color: <?php echo $header_color; ?>; --mlr-header-text: <?php echo $header_text; ?>;"
        >
            <span class="mlr-hamburger">
                <span class="mlr-hamburger-line"></span>
                <span class="mlr-hamburger-line"></span>
                <span class="mlr-hamburger-line"></span>
            </span>
        </button>

        <!-- MLR: Overlay que bloquea toda interacción -->
        <div class="mlr-overlay" aria-hidden="true" style="--mlr-overlay-opacity: <?php echo $overlay_op; ?>;"></div>

        <!-- MLR: Panel lateral -->
        <div
            id="mlr-panel"
            class="mlr-panel"
            role="dialog"
            aria-modal="true"
            aria-label="<?php echo $title; ?>"
            style="
                --mlr-width: <?php echo $width; ?>px;
                --mlr-header-color: <?php echo $header_color; ?>;
                --mlr-header-text: <?php echo $header_text; ?>;
                --mlr-card-border: <?php echo $card_border; ?>;
                --mlr-card-icon: <?php echo $card_icon; ?>;
                --mlr-card-text: <?php echo $card_text; ?>;
                --mlr-card-hover: <?php echo $card_hover; ?>;
            "
        >
            <!-- Header púrpura -->
            <div class="mlr-panel-header">
                <div class="mlr-header-top">
                    <button class="mlr-close-btn" aria-label="<?php esc_attr_e( 'Cerrar menú', 'menu-lateral-responsive' ); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                    <span class="mlr-menu-title"><?php echo $title; ?></span>
                </div>
                <nav class="mlr-header-nav" aria-label="<?php esc_attr_e( 'Links de navegación', 'menu-lateral-responsive' ); ?>">
                    <?php echo wp_nav_menu( $top_links_args ); ?>
                </nav>
            </div>

            <!-- Body blanco con tarjetas -->
            <div class="mlr-panel-body">
                <?php echo wp_nav_menu( $card_args ); ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Fallback para top links si no hay menú asignado.
     */
    public static function fallback_top_links( $args ) {
        $output  = '<ul class="mlr-top-links">';
        $output .= '<li class="mlr-top-link-item"><a href="#">Seguridad</a></li>';
        $output .= '<li class="mlr-top-link-item"><a href="#">Blog</a></li>';
        $output .= '<li class="mlr-top-link-item"><a href="#">Contáctanos</a></li>';
        $output .= '</ul>';

        if ( ! empty( $args['echo'] ) ) {
            echo $output;
        }
        return $output;
    }

    /**
     * Fallback para cards si no hay menú asignado.
     */
    public static function fallback_cards( $args ) {
        $output  = '<ul class="mlr-cards-grid">';
        $output .= '<li class="mlr-card-item"><a href="#" class="mlr-card">';
        $output .= '<span class="mlr-card-icon">' . self::get_icon_svg( 'grid' ) . '</span>';
        $output .= '<span class="mlr-card-label">Productos</span>';
        $output .= '</a></li>';
        $output .= '<li class="mlr-card-item"><a href="#" class="mlr-card">';
        $output .= '<span class="mlr-card-icon">' . self::get_icon_svg( 'screen' ) . '</span>';
        $output .= '<span class="mlr-card-label">Canales electrónicos</span>';
        $output .= '</a></li>';
        $output .= '<li class="mlr-card-item"><a href="#" class="mlr-card">';
        $output .= '<span class="mlr-card-icon">' . self::get_icon_svg( 'heart' ) . '</span>';
        $output .= '<span class="mlr-card-label">Beneficios</span>';
        $output .= '</a></li>';
        $output .= '<li class="mlr-card-item"><a href="#" class="mlr-card">';
        $output .= '<span class="mlr-card-icon">' . self::get_icon_svg( 'building' ) . '</span>';
        $output .= '<span class="mlr-card-label">Institución</span>';
        $output .= '</a></li>';
        $output .= '</ul>';

        if ( ! empty( $args['echo'] ) ) {
            echo $output;
        }
        return $output;
    }

    /**
     * Retorna un SVG de icono por nombre.
     * Iconos disponibles: grid, screen, heart, building, money, card, phone, mail, user, settings, chart, shield
     *
     * Para usarlos: agrega la clase CSS "mlr-icon-NOMBRE" al item del menú de WordPress.
     * Ejemplo: mlr-icon-grid, mlr-icon-building
     */
    public static function get_icon_svg( $name ) {
        $icons = array(
            'grid' => '<svg viewBox="0 0 40 40" fill="currentColor" width="40" height="40"><circle cx="8" cy="8" r="3"/><circle cx="20" cy="8" r="3"/><circle cx="32" cy="8" r="3"/><circle cx="8" cy="20" r="3"/><circle cx="20" cy="20" r="3"/><circle cx="32" cy="20" r="3"/><circle cx="8" cy="32" r="3"/><circle cx="20" cy="32" r="3"/><circle cx="32" cy="32" r="3"/></svg>',

            'screen' => '<svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="40" height="40"><rect x="4" y="4" width="22" height="18" rx="2"/><path d="M30 14h6v18a2 2 0 0 1-2 2H14"/><path d="M16 28l-4 4m4 0l-4-4"/></svg>',

            'heart' => '<svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="40" height="40"><rect x="6" y="2" width="28" height="36" rx="2"/><path d="M20 28l-5.4-5.4a3.8 3.8 0 0 1 5.4-5.4 3.8 3.8 0 0 1 5.4 5.4L20 28z"/><line x1="12" y1="8" x2="22" y2="8"/></svg>',

            'building' => '<svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="40" height="40"><path d="M6 36h28"/><path d="M8 36V12l12-8 12 8v24"/><line x1="14" y1="16" x2="14" y2="20"/><line x1="20" y1="16" x2="20" y2="20"/><line x1="26" y1="16" x2="26" y2="20"/><line x1="14" y1="24" x2="14" y2="28"/><line x1="20" y1="24" x2="20" y2="28"/><line x1="26" y1="24" x2="26" y2="28"/></svg>',

            'money' => '<svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="40" height="40"><rect x="2" y="8" width="36" height="24" rx="3"/><circle cx="20" cy="20" r="6"/><path d="M6 14h2m24 0h2M6 26h2m24 0h2"/></svg>',

            'card' => '<svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="40" height="40"><rect x="2" y="8" width="36" height="24" rx="3"/><line x1="2" y1="16" x2="38" y2="16"/><line x1="8" y1="24" x2="18" y2="24"/><line x1="8" y1="28" x2="14" y2="28"/></svg>',

            'phone' => '<svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="40" height="40"><rect x="10" y="2" width="20" height="36" rx="3"/><line x1="17" y1="32" x2="23" y2="32"/></svg>',

            'mail' => '<svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="40" height="40"><rect x="2" y="8" width="36" height="24" rx="3"/><path d="M2 11l18 12 18-12"/></svg>',

            'user' => '<svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="40" height="40"><circle cx="20" cy="14" r="8"/><path d="M4 36c0-8.8 7.2-16 16-16s16 7.2 16 16"/></svg>',

            'settings' => '<svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="40" height="40"><circle cx="20" cy="20" r="6"/><path d="M20 2v6m0 24v6M2 20h6m24 0h6M6.9 6.9l4.2 4.2m17.8 17.8l4.2 4.2M33.1 6.9l-4.2 4.2M11.1 28.9l-4.2 4.2"/></svg>',

            'chart' => '<svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="40" height="40"><polyline points="4,32 14,18 22,24 36,8"/><polyline points="28,8 36,8 36,16"/></svg>',

            'shield' => '<svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="40" height="40"><path d="M20 2L4 10v10c0 10.5 6.8 18.3 16 22 9.2-3.7 16-11.5 16-22V10L20 2z"/><polyline points="14 20 18 24 26 16"/></svg>',
        );

        return isset( $icons[ $name ] ) ? $icons[ $name ] : $icons['grid'];
    }
}

MLR_Shortcode::get_instance();
