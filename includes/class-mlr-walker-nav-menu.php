<?php
/**
 * Walker personalizado para el menú lateral.
 * Soporta submenús con toggle de expansión.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MLR_Walker_Nav_Menu extends Walker_Nav_Menu {

    /**
     * Inicia un nivel del árbol (submenú).
     *
     * @param string $output HTML de salida.
     * @param int    $depth  Profundidad del ítem.
     * @param array  $args   Argumentos del menú.
     */
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        $indent  = str_repeat( "\t", $depth );
        $output .= "\n{$indent}<ul class=\"mlr-sub-menu mlr-sub-menu-depth-{$depth}\">\n";
    }

    /**
     * Finaliza un nivel del árbol (submenú).
     *
     * @param string $output HTML de salida.
     * @param int    $depth  Profundidad del ítem.
     * @param array  $args   Argumentos del menú.
     */
    public function end_lvl( &$output, $depth = 0, $args = null ) {
        $indent  = str_repeat( "\t", $depth );
        $output .= "{$indent}</ul>\n";
    }

    /**
     * Inicia un elemento del menú.
     *
     * @param string   $output HTML de salida.
     * @param WP_Post  $item   Objeto del ítem del menú.
     * @param int      $depth  Profundidad del ítem.
     * @param stdClass $args   Argumentos del menú.
     * @param int      $id     ID del ítem.
     */
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        $classes   = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'mlr-menu-item';
        $classes[] = 'mlr-menu-item-depth-' . $depth;

        $has_children = in_array( 'menu-item-has-children', $classes, true );
        if ( $has_children ) {
            $classes[] = 'mlr-has-children';
        }

        $class_names = implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        $id_attr = apply_filters( 'nav_menu_item_id', 'mlr-menu-item-' . $item->ID, $item, $args, $depth );
        $id_attr = $id_attr ? ' id="' . esc_attr( $id_attr ) . '"' : '';

        $output .= $indent . '<li' . $id_attr . $class_names . '>';

        $atts           = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target ) ? $item->target : '';
        $atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
        $atts['href']   = ! empty( $item->url ) ? $item->url : '';
        $atts['class']  = 'mlr-menu-link';

        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $title = apply_filters( 'the_title', $item->title, $item->ID );
        $title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

        $item_output  = isset( $args->before ) ? $args->before : '';
        $item_output .= '<a' . $attributes . '>';
        $item_output .= ( isset( $args->link_before ) ? $args->link_before : '' ) . $title . ( isset( $args->link_after ) ? $args->link_after : '' );
        $item_output .= '</a>';

        if ( $has_children ) {
            $item_output .= '<button class="mlr-submenu-toggle" aria-expanded="false" aria-label="' . esc_attr__( 'Expandir submenú', 'menu-lateral-responsive' ) . '">';
            $item_output .= '<svg class="mlr-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>';
            $item_output .= '</button>';
        }

        $item_output .= isset( $args->after ) ? $args->after : '';

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }

    /**
     * Finaliza un elemento del menú.
     *
     * @param string   $output HTML de salida.
     * @param WP_Post  $item   Objeto del ítem del menú.
     * @param int      $depth  Profundidad del ítem.
     * @param stdClass $args   Argumentos del menú.
     */
    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= "</li>\n";
    }
}
