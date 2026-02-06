<?php
/**
 * Widget de Elementor para Menu Lateral Responsive.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MLR_Elementor_Widget extends \Elementor\Widget_Base {

    /**
     * Nombre del widget.
     *
     * @return string
     */
    public function get_name() {
        return 'mlr_sidebar_menu';
    }

    /**
     * Título del widget.
     *
     * @return string
     */
    public function get_title() {
        return esc_html__( 'Menu Lateral Responsive', 'menu-lateral-responsive' );
    }

    /**
     * Icono del widget.
     *
     * @return string
     */
    public function get_icon() {
        return 'eicon-menu-bar';
    }

    /**
     * Categorías del widget.
     *
     * @return array
     */
    public function get_categories() {
        return array( 'mlr-category', 'general' );
    }

    /**
     * Palabras clave del widget.
     *
     * @return array
     */
    public function get_keywords() {
        return array( 'menu', 'sidebar', 'lateral', 'navigation', 'responsive', 'hamburger' );
    }

    /**
     * Registra los controles del widget.
     */
    protected function register_controls() {

        // --- Sección: Contenido ---
        $this->start_controls_section(
            'section_content',
            array(
                'label' => esc_html__( 'Configuración del menú', 'menu-lateral-responsive' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $menus = wp_get_nav_menus();
        $menu_options = array( '' => esc_html__( 'Usar menú por defecto del plugin', 'menu-lateral-responsive' ) );
        foreach ( $menus as $menu ) {
            $menu_options[ $menu->slug ] = $menu->name;
        }

        $this->add_control(
            'menu_select',
            array(
                'label'   => esc_html__( 'Seleccionar menú', 'menu-lateral-responsive' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => $menu_options,
                'default' => '',
            )
        );

        $this->add_control(
            'position',
            array(
                'label'   => esc_html__( 'Posición', 'menu-lateral-responsive' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'left'  => esc_html__( 'Izquierda', 'menu-lateral-responsive' ),
                    'right' => esc_html__( 'Derecha', 'menu-lateral-responsive' ),
                ),
                'default' => 'left',
            )
        );

        $this->add_control(
            'width',
            array(
                'label'   => esc_html__( 'Ancho del menú (px)', 'menu-lateral-responsive' ),
                'type'    => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range' => array(
                    'px' => array(
                        'min'  => 200,
                        'max'  => 600,
                        'step' => 10,
                    ),
                ),
                'default' => array(
                    'unit' => 'px',
                    'size' => 300,
                ),
            )
        );

        $this->add_control(
            'show_logo',
            array(
                'label'        => esc_html__( 'Mostrar logo', 'menu-lateral-responsive' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Sí', 'menu-lateral-responsive' ),
                'label_off'    => esc_html__( 'No', 'menu-lateral-responsive' ),
                'return_value' => '1',
                'default'      => '',
            )
        );

        $this->end_controls_section();

        // --- Sección: Estilos ---
        $this->start_controls_section(
            'section_style',
            array(
                'label' => esc_html__( 'Estilos', 'menu-lateral-responsive' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'bg_color',
            array(
                'label'   => esc_html__( 'Color de fondo', 'menu-lateral-responsive' ),
                'type'    => \Elementor\Controls_Manager::COLOR,
                'default' => '#1a1a2e',
            )
        );

        $this->add_control(
            'text_color',
            array(
                'label'   => esc_html__( 'Color del texto', 'menu-lateral-responsive' ),
                'type'    => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
            )
        );

        $this->add_control(
            'hover_color',
            array(
                'label'   => esc_html__( 'Color hover', 'menu-lateral-responsive' ),
                'type'    => \Elementor\Controls_Manager::COLOR,
                'default' => '#16213e',
            )
        );

        $this->add_control(
            'accent_color',
            array(
                'label'   => esc_html__( 'Color de acento', 'menu-lateral-responsive' ),
                'type'    => \Elementor\Controls_Manager::COLOR,
                'default' => '#0f3460',
            )
        );

        $this->end_controls_section();
    }

    /**
     * Renderiza el widget.
     */
    protected function render() {
        $settings = $this->get_settings_for_display();

        $options = get_option( 'mlr_options', array() );

        // Sobreescribir opciones con las del widget
        if ( ! empty( $settings['bg_color'] ) ) {
            $options['bg_color'] = $settings['bg_color'];
        }
        if ( ! empty( $settings['text_color'] ) ) {
            $options['text_color'] = $settings['text_color'];
        }
        if ( ! empty( $settings['hover_color'] ) ) {
            $options['hover_color'] = $settings['hover_color'];
        }
        if ( ! empty( $settings['accent_color'] ) ) {
            $options['accent_color'] = $settings['accent_color'];
        }

        $atts = array(
            'position'  => ! empty( $settings['position'] ) ? $settings['position'] : 'left',
            'width'     => ! empty( $settings['width']['size'] ) ? $settings['width']['size'] : 300,
            'menu'      => ! empty( $settings['menu_select'] ) ? $settings['menu_select'] : '',
            'show_logo' => ! empty( $settings['show_logo'] ) ? $settings['show_logo'] : false,
        );

        echo MLR_Shortcode::render_menu( $atts, $options );
    }
}
