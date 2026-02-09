<?php
/**
 * Widget de Elementor para Menu Lateral Responsive.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MLR_Elementor_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'mlr_sidebar_menu';
    }

    public function get_title() {
        return esc_html__( 'Menu Lateral Responsive', 'menu-lateral-responsive' );
    }

    public function get_icon() {
        return 'eicon-menu-bar';
    }

    public function get_categories() {
        return array( 'mlr-category', 'general' );
    }

    public function get_keywords() {
        return array( 'menu', 'sidebar', 'lateral', 'navigation', 'responsive', 'hamburger', 'popup' );
    }

    protected function register_controls() {

        // --- Sección: Contenido ---
        $this->start_controls_section(
            'section_content',
            array(
                'label' => esc_html__( 'Configuración del menú', 'menu-lateral-responsive' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'menu_title',
            array(
                'label'   => esc_html__( 'Título del menú', 'menu-lateral-responsive' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Menú',
            )
        );

        $menus = wp_get_nav_menus();
        $menu_options = array( '' => esc_html__( 'Usar ubicación por defecto', 'menu-lateral-responsive' ) );
        foreach ( $menus as $menu ) {
            $menu_options[ $menu->slug ] = $menu->name;
        }

        $this->add_control(
            'top_menu',
            array(
                'label'   => esc_html__( 'Menú de links superiores', 'menu-lateral-responsive' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => $menu_options,
                'default' => '',
            )
        );

        $this->add_control(
            'card_menu',
            array(
                'label'   => esc_html__( 'Menú de tarjetas', 'menu-lateral-responsive' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => $menu_options,
                'default' => '',
            )
        );

        $this->add_control(
            'width',
            array(
                'label'      => esc_html__( 'Ancho del panel (px)', 'menu-lateral-responsive' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array( 'min' => 220, 'max' => 500, 'step' => 10 ),
                ),
                'default'    => array( 'unit' => 'px', 'size' => 280 ),
            )
        );

        $this->end_controls_section();

        // --- Sección: Estilos Header ---
        $this->start_controls_section(
            'section_header_style',
            array(
                'label' => esc_html__( 'Estilos del Header', 'menu-lateral-responsive' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'header_color',
            array(
                'label'   => esc_html__( 'Color de fondo', 'menu-lateral-responsive' ),
                'type'    => \Elementor\Controls_Manager::COLOR,
                'default' => '#7B2D8E',
            )
        );

        $this->add_control(
            'header_text',
            array(
                'label'   => esc_html__( 'Color del texto', 'menu-lateral-responsive' ),
                'type'    => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
            )
        );

        $this->end_controls_section();

        // --- Sección: Estilos Tarjetas ---
        $this->start_controls_section(
            'section_card_style',
            array(
                'label' => esc_html__( 'Estilos de Tarjetas', 'menu-lateral-responsive' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'card_border',
            array(
                'label'   => esc_html__( 'Color del borde', 'menu-lateral-responsive' ),
                'type'    => \Elementor\Controls_Manager::COLOR,
                'default' => '#7B2D8E',
            )
        );

        $this->add_control(
            'card_icon_color',
            array(
                'label'   => esc_html__( 'Color de iconos', 'menu-lateral-responsive' ),
                'type'    => \Elementor\Controls_Manager::COLOR,
                'default' => '#7B2D8E',
            )
        );

        $this->add_control(
            'card_text_color',
            array(
                'label'   => esc_html__( 'Color del texto', 'menu-lateral-responsive' ),
                'type'    => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $options  = get_option( 'mlr_options', array() );

        // Sobreescribir opciones con las del widget
        $widget_overrides = array(
            'header_color', 'header_text', 'card_border', 'card_icon_color', 'card_text_color',
        );
        foreach ( $widget_overrides as $key ) {
            if ( ! empty( $settings[ $key ] ) ) {
                $options[ $key ] = $settings[ $key ];
            }
        }

        $atts = array(
            'title'     => ! empty( $settings['menu_title'] ) ? $settings['menu_title'] : 'Menú',
            'width'     => ! empty( $settings['width']['size'] ) ? $settings['width']['size'] : 280,
            'top_menu'  => ! empty( $settings['top_menu'] ) ? $settings['top_menu'] : '',
            'card_menu' => ! empty( $settings['card_menu'] ) ? $settings['card_menu'] : '',
        );

        echo MLR_Shortcode::render_menu( $atts, $options );
    }
}
