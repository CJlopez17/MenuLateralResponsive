<?php
/**
 * Clase para el registro y renderizado del shortcode.
 *
 * Estructura del panel:
 *  ┌─────────────────────────────────────────┐
 *  │ ✕  Menú                                 │  ← Header
 *  │ Link1 Link2 Link3                       │  ← Top links
 *  ├──────────────┬──────────────────────────┤
 *  │  ┌────────┐  │  Categoría 1             │
 *  │  │ [icon] │  │    - Link A              │
 *  │  │ Texto  │  │    - Link B              │
 *  │  └────────┘  │  Categoría 2             │
 *  │  ┌────────┐  │    - Link C              │
 *  │  │ [icon] │  │    - Link D              │
 *  │  │ Texto  │  │                          │
 *  │  └────────┘  │                          │
 *  └──────────────┴──────────────────────────┘
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
            'title' => isset( $options['menu_title'] ) ? $options['menu_title'] : 'Menú',
            'width' => isset( $options['panel_width'] ) ? $options['panel_width'] : '280',
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

        $menu_data = get_option( 'mlr_menu_data', array( 'top_links' => array(), 'cards' => array() ) );

        $title          = esc_html( $atts['title'] );
        $width          = absint( $atts['width'] );
        $submenu_width  = isset( $options['submenu_width'] ) ? absint( $options['submenu_width'] ) : 520;
        $header_color   = isset( $options['header_color'] ) ? esc_attr( $options['header_color'] ) : '#7B2D8E';
        $header_text    = isset( $options['header_text'] ) ? esc_attr( $options['header_text'] ) : '#ffffff';
        $card_border    = isset( $options['card_border'] ) ? esc_attr( $options['card_border'] ) : '#7B2D8E';
        $card_icon      = isset( $options['card_icon_color'] ) ? esc_attr( $options['card_icon_color'] ) : '#7B2D8E';
        $card_text      = isset( $options['card_text_color'] ) ? esc_attr( $options['card_text_color'] ) : '#333333';
        $card_hover     = isset( $options['card_bg_hover'] ) ? esc_attr( $options['card_bg_hover'] ) : '#f5f0f7';
        $card_indicator = isset( $options['card_active_indicator'] ) ? esc_attr( $options['card_active_indicator'] ) : '#7B2D8E';
        $overlay_op     = isset( $options['overlay_opacity'] ) ? esc_attr( $options['overlay_opacity'] ) : '0.6';
        $sub_cat_color  = isset( $options['submenu_cat_color'] ) ? esc_attr( $options['submenu_cat_color'] ) : '#7B2D8E';
        $sub_link_color = isset( $options['submenu_link_color'] ) ? esc_attr( $options['submenu_link_color'] ) : '#555555';
        $sub_link_hover = isset( $options['submenu_link_hover'] ) ? esc_attr( $options['submenu_link_hover'] ) : '#7B2D8E';

        $top_links = isset( $menu_data['top_links'] ) ? $menu_data['top_links'] : array();
        $cards     = isset( $menu_data['cards'] ) ? $menu_data['cards'] : array();
        $has_submenu_cards = false;
        foreach ( $cards as $card ) {
            if ( ! empty( $card['categories'] ) ) {
                $has_submenu_cards = true;
                break;
            }
        }

        ob_start();
        ?>
        <!-- MLR: Botón hamburger -->
        <button
            class="mlr-toggle-btn"
            aria-label="<?php esc_attr_e( 'Abrir menú', 'menu-lateral-responsive' ); ?>"
            aria-expanded="false"
            aria-controls="mlr-panel"
            style="--mlr-header-color: <?php echo $header_color; ?>; --mlr-header-text: <?php echo $header_text; ?>;"
        >
            <svg class="mlr-hamburger-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" aria-hidden="true"><path d="M120-240v-60h720v60H120Zm0-210v-60h720v60H120Zm0-210v-60h720v60H120Z"/></svg>
        </button>

        <!-- MLR: Overlay -->
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
                --mlr-submenu-width: <?php echo $submenu_width; ?>px;
                --mlr-header-color: <?php echo $header_color; ?>;
                --mlr-header-text: <?php echo $header_text; ?>;
                --mlr-card-border: <?php echo $card_border; ?>;
                --mlr-card-icon: <?php echo $card_icon; ?>;
                --mlr-card-text: <?php echo $card_text; ?>;
                --mlr-card-hover: <?php echo $card_hover; ?>;
                --mlr-card-active-indicator: <?php echo $card_indicator; ?>;
                --mlr-sub-cat-color: <?php echo $sub_cat_color; ?>;
                --mlr-sub-link-color: <?php echo $sub_link_color; ?>;
                --mlr-sub-link-hover: <?php echo $sub_link_hover; ?>;
            "
        >
            <!-- Header -->
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
                <?php if ( ! empty( $top_links ) ) : ?>
                <nav class="mlr-header-nav" aria-label="<?php esc_attr_e( 'Links de navegación', 'menu-lateral-responsive' ); ?>">
                    <ul class="mlr-top-links">
                        <?php foreach ( $top_links as $link ) : ?>
                            <li class="mlr-top-link-item">
                                <a href="<?php echo esc_url( $link['url'] ); ?>" class="mlr-top-link"><?php echo esc_html( $link['title'] ); ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>

            <!-- Body -->
            <div class="mlr-panel-body">
                <div class="mlr-cards-sidebar">
                    <ul class="mlr-cards-grid">
                        <?php foreach ( $cards as $index => $card ) :
                            $has_submenu = ! empty( $card['categories'] );
                            $icon_html = self::get_card_icon( $card );
                        ?>
                            <li class="mlr-card-item">
                                <?php if ( $has_submenu ) : ?>
                                    <div class="mlr-card-wrapper">
                                        <button
                                            type="button"
                                            class="mlr-card"
                                            data-card-index="<?php echo esc_attr( $index ); ?>"
                                            data-has-submenu="1"
                                            aria-expanded="false"
                                        >
                                            <span class="mlr-card-icon"><?php echo $icon_html; ?></span>
                                            <span class="mlr-card-label"><?php echo esc_html( $card['title'] ); ?></span>
                                        </button>
                                    </div>
                                <?php else :
                                    $card_url = ! empty( $card['url'] ) ? $card['url'] : '#';
                                ?>
                                    <a
                                        href="<?php echo esc_url( $card_url ); ?>"
                                        class="mlr-card"
                                        data-card-index="<?php echo esc_attr( $index ); ?>"
                                    >
                                        <span class="mlr-card-icon"><?php echo $icon_html; ?></span>
                                        <span class="mlr-card-label"><?php echo esc_html( $card['title'] ); ?></span>
                                    </a>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Submenu panel -->
                <?php if ( $has_submenu_cards ) : ?>
                <div class="mlr-submenu-panel" aria-hidden="true">
                    <?php foreach ( $cards as $index => $card ) :
                        if ( empty( $card['categories'] ) ) continue;
                    ?>
                        <div class="mlr-submenu-content" data-card-index="<?php echo esc_attr( $index ); ?>" aria-hidden="true">
                            <?php foreach ( $card['categories'] as $cat ) :
                                $cat_color = ! empty( $cat['color'] ) ? $cat['color'] : $sub_cat_color;
                            ?>
                                <div class="mlr-submenu-category">
                                    <h3 class="mlr-submenu-cat-title" style="color: <?php echo esc_attr( $cat_color ); ?>;">
                                        <?php echo esc_html( $cat['title'] ); ?>
                                    </h3>
                                    <?php if ( ! empty( $cat['links'] ) ) : ?>
                                        <ul class="mlr-submenu-links">
                                            <?php foreach ( $cat['links'] as $link ) : ?>
                                                <li>
                                                    <a href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['title'] ); ?></a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Genera el HTML del icono de una tarjeta.
     */
    private static function get_card_icon( $card ) {
        if ( ! empty( $card['icon_type'] ) && 'custom' === $card['icon_type'] && ! empty( $card['icon_url'] ) ) {
            // Solo permitir archivos SVG
            $url = $card['icon_url'];
            $parsed_path = wp_parse_url( $url, PHP_URL_PATH );
            if ( $parsed_path && '.svg' === strtolower( substr( $parsed_path, -4 ) ) ) {
                return '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( $card['title'] ) . '" class="mlr-card-icon-img">';
            }
        }

        $icon_name = ! empty( $card['icon_name'] ) ? $card['icon_name'] : 'grid';
        return self::get_icon_svg( $icon_name );
    }

    /**
     * Retorna un SVG de icono por nombre.
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
