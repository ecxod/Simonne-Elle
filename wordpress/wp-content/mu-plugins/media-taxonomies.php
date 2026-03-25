<?php
/**
 * Plugin Name:       Media Taxonomies & Columns
 * Text Domain:       media-taxonomies-columns
 * Plugin URI:        https://github.com/ecxod/media-taxonomies-columns
 * Description:       Aktiviert Kategorien/Tags für Medien und macht sie in der Mediathek sortierbar.
 * Author:            Christian Eichert
 * Author URI:        https://github.com/ecxod
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Version:           1.0
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

/**
 * 5. Suche in der Mediathek auf Kategorien und Schlagworte ausweiten
 */
add_filter( 'posts_clauses', function( $clauses, $query ) {
    global $wpdb;

    // Nur in der Administration und nur bei der Mediathek-Suche ausführen
    if ( ! is_admin() || ! $query->is_main_query() || $query->get('post_type') !== 'attachment' ) {
        return $clauses;
    }

    $s = $query->get('s');
    if ( empty($s) ) {
        return $clauses;
    }

    // Die Suche auf Taxonomien (category & post_tag) ausweiten
    $clauses['where'] .= $wpdb->prepare("
        OR EXISTS (
            SELECT 1 FROM {$wpdb->term_relationships} tr
            JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
            WHERE tr.object_id = {$wpdb->posts}.ID
            AND (tt.taxonomy = 'category' OR tt.taxonomy = 'post_tag')
            AND t.name LIKE %s
        )", '%' . $wpdb->esc_like($s) . '%'
    );

    // Verhindert Duplikate in der Ergebnisliste
    $clauses['distinct'] = 'DISTINCT';

    return $clauses;
}, 10, 2 );


/**
 * 6. Performance-Optimierte Suche für große Mediatheken (Gitter & Liste)
 */

// Korrektur für die Kachel-Ansicht (Gitter/AJAX)
add_filter( 'ajax_query_attachments_args', 'dry_heavy_media_search' );
// Korrektur für die Listen-Ansicht
add_filter( 'request', 'dry_heavy_media_search' );

function dry_heavy_media_search( $query ) {
    // Nur ausführen, wenn ein Suchbegriff ('s') vorhanden ist
    if ( empty( $query['s'] ) ) {
        return $query;
    }

    // Wir nutzen eine Tax-Query, die von WordPress besser indiziert wird
    // Das ist performanter als ein manueller SQL-Join
    $search_term = $query['s'];
    
    // Wir sagen WP: Suche in Kategorien ODER Tags nach diesem Begriff
    $query['tax_query'] = array(
        'relation' => 'OR',
        array(
            'taxonomy' => 'category',
            'field'    => 'name',
            'terms'    => $search_term,
            'operator' => 'LIKE' // Suche nach Teilbegriffen
        ),
        array(
            'taxonomy' => 'post_tag',
            'field'    => 'name',
            'terms'    => $search_term,
            'operator' => 'LIKE'
        )
    );

    return $query;
}