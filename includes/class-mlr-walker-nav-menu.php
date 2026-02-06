<?php
/**
 * Walkers personalizados para los dos tipos de menú del panel lateral.
 *
 * 1. MLR_Walker_Top_Links  → Links horizontales en el header púrpura
 * 2. MLR_Walker_Cards      → Tarjetas con icono en el body blanco
 *
 * Para asignar un icono a una tarjeta, agrega una clase CSS al item del menú
 * en Apariencia > Menús con el formato: mlr-icon-NOMBRE
 *
 * Iconos disponibles:
 *   grid, screen, heart, building, money, card, phone, mail, user, settings, chart, shield
 *
 * También puedes poner una URL de imagen en el campo "Descripción" del item del menú.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Walker para los links superiores del header (simples, horizontales).
 */
class MLR_Walker_Top_Links extends Walker_Nav_Menu {

    public function start_lvl( &$output, $depth = 0, $args = null ) {
        // Sin submenús
    }

    public function end_lvl( &$output, $depth = 0, $args = null ) {
        // Sin submenús
    }

    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $classes = array( 'mlr-top-link-item' );
        if ( $item->current ) {
            $classes[] = 'mlr-current';
        }
        $class_attr = ' class="' . esc_attr( implode( ' ', $classes ) ) . '"';

        $href  = ! empty( $item->url ) ? esc_url( $item->url ) : '#';
        $title = apply_filters( 'the_title', $item->title, $item->ID );
        $title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

        $target = ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
        $rel    = ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';

        $output .= '<li' . $class_attr . '>';
        $output .= '<a href="' . $href . '"' . $target . $rel . ' class="mlr-top-link">';
        $output .= esc_html( $title );
        $output .= '</a>';
    }

    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= "</li>\n";
    }
}

/**
 * Walker para las tarjetas del body (con icono + texto).
 */
class MLR_Walker_Cards extends Walker_Nav_Menu {

    public function start_lvl( &$output, $depth = 0, $args = null ) {
        // Sin submenús en tarjetas
    }

    public function end_lvl( &$output, $depth = 0, $args = null ) {
        // Sin submenús en tarjetas
    }

    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $classes   = empty( $item->classes ) ? array() : (array) $item->classes;
        $href      = ! empty( $item->url ) ? esc_url( $item->url ) : '#';
        $title     = apply_filters( 'the_title', $item->title, $item->ID );
        $title     = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );
        $target    = ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
        $rel       = ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';

        // Detectar icono desde las clases CSS del item
        $icon_name = 'grid'; // default
        foreach ( $classes as $class ) {
            if ( strpos( $class, 'mlr-icon-' ) === 0 ) {
                $icon_name = str_replace( 'mlr-icon-', '', $class );
                break;
            }
        }

        // Si la descripción del item es una URL de imagen, usarla como icono
        $description = ! empty( $item->description ) ? trim( $item->description ) : '';
        $use_image   = ( 0 === strpos( $description, 'http' ) );

        $icon_html = '';
        if ( $use_image ) {
            $icon_html = '<img src="' . esc_url( $description ) . '" alt="' . esc_attr( $title ) . '" class="mlr-card-icon-img">';
        } else {
            $icon_html = MLR_Shortcode::get_icon_svg( $icon_name );
        }

        $output .= '<li class="mlr-card-item">';
        $output .= '<a href="' . $href . '"' . $target . $rel . ' class="mlr-card">';
        $output .= '<span class="mlr-card-icon">' . $icon_html . '</span>';
        $output .= '<span class="mlr-card-label">' . esc_html( $title ) . '</span>';
        $output .= '</a>';
    }

    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= "</li>\n";
    }
}
