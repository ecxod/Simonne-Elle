<?php
/**
 * Plugin Name: Media Taxonomies & Columns
 * Description: Aktiviert Kategorien/Tags für Medien und macht sie in der Mediathek sortierbar.
 * Author: Christian Eichert
 * Version: 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// 1. Grundfunktion: Kategorien & Tags für Medien aktivieren
add_action('init', function() {
    register_taxonomy_for_object_type('category', 'attachment');
    register_taxonomy_for_object_type('post_tag', 'attachment');
});

// 2. Spalten in der Mediathek-Liste registrieren
add_filter( 'manage_media_columns', function( $columns ) {
    $columns['categories'] = __('Kategorien');
    $columns['tags'] = __('Schlagworte');
    return $columns;
});

// 3. Inhalt in die Spalten füllen
add_action( 'manage_media_custom_column', function( $column_name, $id ) {
    $taxonomy = ($column_name === 'categories') ? 'category' : (($column_name === 'tags') ? 'post_tag' : null);
    
    if ( $taxonomy ) {
        $terms = get_the_terms( $id, $taxonomy );
        if ( ! empty( $terms ) ) {
            $out = array();
            foreach ( $terms as $term ) {
                $out[] = esc_html( $term->name );
            }
            echo implode( ', ', $out );
        } else {
            echo '—';
        }
    }
}, 10, 2 );

// 4. Sortier-Pfeile aktivieren (UI)
add_filter( 'manage_upload_sortable_columns', function( $sortable_columns ) {
    $sortable_columns['categories'] = 'categories';
    $sortable_columns['tags'] = 'tags';
    return $sortable_columns;
});