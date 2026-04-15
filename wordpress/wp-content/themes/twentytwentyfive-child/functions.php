<?php

// kein Zugriff auf xmlrpc.php
add_filter('xmlrpc_enabled', '__return_false');

// Client-Cache-Antwort-Header
add_action('send_headers', 'add_cache_headers');
function add_cache_headers() {
    header("Cache-Control: public, max-age=31536000");
    header("Expires: " . gmdate("D, d M Y H:i:s", time() + 31536000) . " GMT");

    header("Age: 0"); // Altersheader
    header("Last-Modified: " . gmdate("D, d M Y H:i:s", time()) . " GMT"); // Letzte Änderung
    header("ETag: \"" . md5(time()) . "\""); // ETag-Header, hier als Hash des Zeitstempels
    header("X-Cache-Enabled: true"); // Cache aktiviert
    header("X-Cache-Disabled: false"); // Cache deaktiviert
    header("X-Srcache-Store-Status: stored"); // Status der Cache-Speicherung
    header("X-Srcache-Fetch-Status: hit"); // Status des Cache-Abrufs
}



add_action(hook_name:'wp_enqueue_scripts', callback:'child_theme_enqueue_styles');
function child_theme_enqueue_styles():void 
{
    // Parent-Theme-Stylesheet laden
    wp_enqueue_style(handle:'parent-style', src:get_template_directory_uri() . '/style.css');

    // Child-Theme-Stylesheet laden ( dies ladet die style.css im child Verzeichnis.)
    /**
     * wp_enqueue_style(handle:'child-style', src:get_stylesheet_uri()); 
     * get_stylesheet_uri = > gibt direkt den plad inklusive style.css
     * Es ist das selbe wie das hier
     * wp_enqueue_style('child-style1', get_stylesheet_directory_uri() . '/style.css');
     * get_stylesheet_directory_uri() bibt eben nur den Ordner aus wo sichj die style.css befindet.
     * "template" = ist immer das template
     * "stylesheet" = immer das child
     */
    wp_enqueue_style(handle:'child-style', src:get_stylesheet_uri());
    wp_enqueue_style(handle:'child-style1', src:get_stylesheet_directory_uri() . '/assets/css/style1.css');
    wp_enqueue_style(handle:'child-style2', src:get_stylesheet_directory_uri() . '/assets/css/style2.css');
    wp_enqueue_style(handle:'child-style3', src:get_stylesheet_directory_uri() . '/assets/css/style3.css');
    wp_enqueue_style(handle:'child-style4', src:get_stylesheet_directory_uri() . '/assets/css/style4-gt.css');
}




// Zusätzliche Spalte "Link / Permalink" bei Seiten-Übersicht hinzufügen
add_filter( 'manage_pages_columns', 'meine_seiten_spalte_link_hinzufuegen' );
function meine_seiten_spalte_link_hinzufuegen( $columns ) {
    // Spalte möglichst weit hinten einfügen (z. B. nach "Datum")
    $columns['seite_permalink'] = 'Link / URL';
    return $columns;
}

// Inhalt der neuen Spalte ausgeben
add_action( 'manage_pages_custom_column', 'meine_seiten_spalte_link_inhalt', 10, 2 );
function meine_seiten_spalte_link_inhalt( $column_name, $post_id ) {
    if ( $column_name === 'seite_permalink' ) {
        $permalink = get_permalink( $post_id );
        if ( $permalink ) {
            // Nur den Pfad nach der Domain anzeigen
            $path = str_replace( home_url(), '', $permalink );
            echo esc_html( $path );
        } else {
            echo '—';
        }
    }
}

// Zusätzliche Spalte "Titelform / Vorgeschlagener Slug" bei Seiten-Übersicht
add_filter( 'manage_pages_columns', 'meine_seiten_spalte_titelform_hinzufuegen' );
function meine_seiten_spalte_titelform_hinzufuegen( $columns ) {
    // Spalte möglichst weit hinten (nach Datum)
    $new_columns = array();
    foreach ( $columns as $key => $title ) {
        $new_columns[ $key ] = $title;
        if ( $key === 'date' ) {  // nach "Datum" einfügen
            $new_columns['seite_titelform'] = 'Titelform / Slug';
        }
    }
    return $new_columns ?: $columns;
}

// Inhalt der neuen Spalte ausgeben
add_action( 'manage_pages_custom_column', 'meine_seiten_spalte_titelform_inhalt', 10, 2 );
function meine_seiten_spalte_titelform_inhalt( $column_name, $post_id ) {
    if ( $column_name !== 'seite_titelform' ) {
        return;
    }

    $post = get_post( $post_id );
    if ( ! $post ) {
        echo '—';
        return;
    }

    if ( in_array( $post->post_status, array( 'publish', 'future' ) ) ) {
        // Bei veröffentlicht oder geplant → echter Permalink (wie vorher)
        $permalink = get_permalink( $post_id );
        $path      = str_replace( home_url(), '', $permalink );
        $path      = untrailingslashit( $path ) . '/';  // mit abschließendem Slash
        echo esc_html( $path );
    } else {
        // Bei Entwurf, pending, privat, auto-draft → den "Titelform"-Wert wie im Schnellbearbeiten
        list( $permalink, $sample_slug ) = get_sample_permalink( $post_id );
        
        if ( $sample_slug ) {
            // Den vollen vorgeschlagenen Pfad bauen (inkl. Eltern-Seiten)
            $path = get_sample_permalink( $post_id )[1];  // das ist schon der relative Pfad/Slug
            $path = untrailingslashit( $path ) . '/';
            echo esc_html( $path );
        } else {
            echo '—';
        }
    }
}

// 1. Neuen Post-Status "archiviert" registrieren
add_action( 'init', 'mein_archiviert_status_registrieren' );
function mein_archiviert_status_registrieren() {
    register_post_status( 'archiviert', array(
        'label'                     => _x( 'Archiviert', 'post status' ),
        'public'                    => false,          // Nicht öffentlich sichtbar
        'exclude_from_search'       => true,
        'show_in_admin_all_list'    => true,          // In "Alle Seiten" anzeigen
        'show_in_admin_status_list' => true,          // In Status-Dropdown + Zähler
        'label_count'               => _n_noop(
            'Archiviert <span class="count">(%s)</span>',
            'Archiviert <span class="count">(%s)</span>'
        ),
    ) );
}

// 2. Den Status im "Schnell bearbeiten" und im normalen Bearbeiten-Dropdown sichtbar machen
add_filter( 'display_post_states', 'mein_archiviert_status_anzeigen', 10, 2 );
function mein_archiviert_status_anzeigen( $post_states, $post ) {
    if ( 'archiviert' === $post->post_status ) {
        $post_states['archiviert'] = '<span style="color:#999;">Archiviert</span>';
    }
    return $post_states;
}

// Im Quick Edit und Bulk Edit + Einzel-Bearbeitung hinzufügen – aber sauber nur einmal
add_action( 'admin_footer', 'mein_archiviert_quick_edit_js' );  // statt zwei separater Hooks
function mein_archiviert_quick_edit_js() {
    global $current_screen;
    
    // Nur auf Seiten-Seiten ausführen
    if ( ! isset( $current_screen ) || 
         ( $current_screen->base !== 'edit' && $current_screen->base !== 'post' ) || 
         $current_screen->post_type !== 'page' ) {
        return;
    }
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var $selects = $('select[name="_status"], select[name="post_status"]');  // beide Varianten abdecken
        
        // Nur anhängen, wenn noch nicht da (vermeidet Duplikate)
        if ( $selects.find('option[value="archiviert"]').length === 0 ) {
            $selects.append('<option value="archiviert">Archiviert</option>');
        }
    });
    </script>
    <?php
}
