<?php
/**
 * Clase para el registro y renderizado del shortcode.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MLR_Shortcode {

    /**
     * Instancia única.
     *
     * @var MLR_Shortcode|null
     */
    private static $instance = null;

    /**
     * Retorna la instancia única.
     *
     * @return MLR_Shortcode
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
        add_shortcode( 'menu_lateral', array( $this, 'render_shortcode' ) );
    }

    /**
     * Renderiza el shortcode del menú lateral.
     *
     * @param array $atts Atributos del shortcode.
     * @return string HTML del menú lateral.
     */
    public function render_shortcode( $atts ) {
        $options = get_option( 'mlr_options', array() );

        $atts = shortcode_atts( array(
            'position'  => isset( $options['position'] ) ? $options['position'] : 'left',
            'width'     => isset( $options['width'] ) ? $options['width'] : '300',
            'theme'     => 'dark',
            'menu'      => '',
            'show_logo' => isset( $options['show_logo'] ) ? $options['show_logo'] : false,
        ), $atts, 'menu_lateral' );

        return self::render_menu( $atts, $options );
    }

    /**
     * Renderiza el HTML del menú. Usado tanto por el shortcode como por el widget de Elementor.
     *
     * @param array $atts    Atributos de renderizado.
     * @param array $options Opciones del plugin.
     * @return string HTML del menú.
     */
    public static function render_menu( $atts, $options = array() ) {
        if ( empty( $options ) ) {
            $options = get_option( 'mlr_options', array() );
        }

        $position    = esc_attr( $atts['position'] );
        $width       = absint( $atts['width'] );
        $bg_color    = isset( $options['bg_color'] ) ? esc_attr( $options['bg_color'] ) : '#1a1a2e';
        $text_color  = isset( $options['text_color'] ) ? esc_attr( $options['text_color'] ) : '#ffffff';
        $hover_color = isset( $options['hover_color'] ) ? esc_attr( $options['hover_color'] ) : '#16213e';
        $accent_color = isset( $options['accent_color'] ) ? esc_attr( $options['accent_color'] ) : '#0f3460';
        $show_logo   = ! empty( $atts['show_logo'] );
        $logo_url    = isset( $options['logo_url'] ) ? esc_url( $options['logo_url'] ) : '';

        $menu_args = array(
            'theme_location' => 'mlr_sidebar_menu',
            'container'      => 'nav',
            'container_class' => 'mlr-nav',
            'menu_class'     => 'mlr-menu-list',
            'depth'          => 3,
            'echo'           => false,
            'walker'         => new MLR_Walker_Nav_Menu(),
            'fallback_cb'    => array( __CLASS__, 'fallback_menu' ),
        );

        if ( ! empty( $atts['menu'] ) ) {
            $menu_args['menu'] = sanitize_text_field( $atts['menu'] );
            unset( $menu_args['theme_location'] );
        }

        ob_start();
        ?>
        <!-- Menu Lateral Responsive - Botón Toggle -->
        <button class="mlr-toggle-btn" aria-label="<?php esc_attr_e( 'Abrir menú', 'menu-lateral-responsive' ); ?>" aria-expanded="false" aria-controls="mlr-sidebar">
            <span class="mlr-hamburger">
                <span class="mlr-hamburger-line"></span>
                <span class="mlr-hamburger-line"></span>
                <span class="mlr-hamburger-line"></span>
            </span>
        </button>

        <!-- Menu Lateral Responsive - Overlay -->
        <div class="mlr-overlay" aria-hidden="true"></div>

        <!-- Menu Lateral Responsive - Sidebar -->
        <aside
            id="mlr-sidebar"
            class="mlr-sidebar mlr-position-<?php echo $position; ?>"
            role="navigation"
            aria-label="<?php esc_attr_e( 'Menú lateral', 'menu-lateral-responsive' ); ?>"
            style="
                --mlr-width: <?php echo $width; ?>px;
                --mlr-bg-color: <?php echo $bg_color; ?>;
                --mlr-text-color: <?php echo $text_color; ?>;
                --mlr-hover-color: <?php echo $hover_color; ?>;
                --mlr-accent-color: <?php echo $accent_color; ?>;
            "
        >
            <!-- Header del sidebar -->
            <div class="mlr-sidebar-header">
                <?php if ( $show_logo && $logo_url ) : ?>
                    <div class="mlr-logo">
                        <img src="<?php echo $logo_url; ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
                    </div>
                <?php else : ?>
                    <div class="mlr-site-title">
                        <?php echo esc_html( get_bloginfo( 'name' ) ); ?>
                    </div>
                <?php endif; ?>

                <button class="mlr-close-btn" aria-label="<?php esc_attr_e( 'Cerrar menú', 'menu-lateral-responsive' ); ?>">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <!-- Navegación -->
            <div class="mlr-sidebar-content">
                <?php echo wp_nav_menu( $menu_args ); ?>
            </div>

            <!-- Footer del sidebar -->
            <div class="mlr-sidebar-footer">
                <p>&copy; <?php echo esc_html( gmdate( 'Y' ) . ' ' . get_bloginfo( 'name' ) ); ?></p>
            </div>
        </aside>
        <?php
        return ob_get_clean();
    }

    /**
     * Menú de respaldo si no se ha configurado ningún menú.
     *
     * @param array $args Argumentos del menú.
     * @return string HTML de respaldo.
     */
    public static function fallback_menu( $args ) {
        $output = '<nav class="mlr-nav">';
        $output .= '<ul class="mlr-menu-list">';
        $output .= '<li class="mlr-menu-item">';
        $output .= '<a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">';
        $output .= esc_html__( 'Configura tu menú', 'menu-lateral-responsive' );
        $output .= '</a>';
        $output .= '</li>';
        $output .= '</ul>';
        $output .= '</nav>';

        if ( $args['echo'] ) {
            echo $output;
        }

        return $output;
    }
}

MLR_Shortcode::get_instance();
