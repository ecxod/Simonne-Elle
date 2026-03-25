<?php
/**
 * Plugin Name:       Custom Site Health – wp-content writable (erweitert: Ordner + Dateien + Real-Tests)
 * Text Domain:       custom-site-health-wp-content-writable
 * Plugin URI:        https://github.com/ecxod/custom-site-health-wp-content-writable
 * Description:       Prüft wp-content-Ordner, wichtige Dateien und reale Schreibbarkeit. Entfernt den nervigen Core-Dateien-Test.
 * Author:            Christian Eichert <c@zp1.net>
 * Author URI:        https://github.com/ecxod
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Version:           1.0
 * Datum:             03.03.2026
 */

add_filter( 'site_status_tests', function( $tests ) {
    // Entferne den originalen "Core-Dateien nicht beschreibbar"-Test
    unset( $tests['direct']['all_files_writable'] ); // Key kann je WP-Version variieren, ggf. 'files_writable' testen

    $tests['direct']['custom_writable_content_extended'] = array(
        'label'       => __( 'wp-content Schreibrechte (Ordner + Dateien + Real-Test)' ),
        'test'        => 'custom_test_content_writable_extended',
        'async'       => false,
    );

    return $tests;
}, 20 );

function custom_test_content_writable_extended() {
    $issues = [];

    // === Wichtige Ordner (müssen writable sein) ===
    $dirs = [
        ['wp-content',          true,  'Haupt-Ordner für Plugins, Themes, Uploads'],
        ['wp-content/uploads',  true,  'Medien-Uploads (sehr wichtig)'],
        ['wp-content/plugins',  true,  'Plugin-Installation & Updates'],
        ['wp-content/themes',   true,  'Theme-Installation & Updates'],
        ['wp-content/upgrade',  true,  'Wird während Core-Updates temporär genutzt'],
        ['wp-content/mu-plugins', false, 'Soll nicht beschreibbar sein Must-Use-Plugins'],

        // Beispiele für optionale / zusätzliche Ordner – einfach true/false ändern oder auskommentieren
        // ['wp-content/cache',       false, 'Caching-Ordner – meist nicht zwingend writable vom Webserver'],
        // ['wp-content/languages',   false, 'Sprachdateien – normalerweise read-only'],
        // ['wp-content/backup-db',   false, 'Typischer Backup-Ordner von manchen Plugins'],
        // ['wp-content/wflogs',      false, 'Wordfence Logs – meist nicht vom Webserver beschrieben'],
    ];

foreach ( $dirs as $item ) {
    [$rel_path, $should_be_writable, $description] = $item;
    $full_path = ABSPATH . $rel_path;

    if ( ! file_exists( $full_path ) ) {
        if ( $should_be_writable ) {
            $issues[] = "Erwarteter Ordner fehlt: $rel_path ($description)";
        }
        // wenn ! $should_be_writable → Fehlen ist okay → ignorieren
        continue;
    }

    $is_writable = is_writable( $full_path );

    if ( $should_be_writable && ! $is_writable ) {
        $issues[] = "$rel_path nicht beschreibbar, aber sollte es sein ($description)";
    } elseif ( ! $should_be_writable && $is_writable ) {
        $issues[] = "$rel_path ist beschreibbar, sollte es aber nicht sein – Sicherheitsrisiko! ($description)";
    }
}

    // === Wichtige Dateien ===
    $files = [
        // Sollte writable sein (falls existent)
        ['wp-content/.htaccess',     false,  'sollte beschreibbar sein (Permalinks etc.)'],
        ['wp-content/index.php',     false,  'sollte beschreibbar sein (Directory-Schutz)'],

        // Sollte NICHT writable sein (Sicherheit)
        ['wp-config.php',            false, 'ist beschreibbar – Sicherheitsrisiko! (sollte 644 sein)'],
    ];

    foreach ( $files as $item ) {
        [$rel_file, $should_be_writable, $msg] = $item;
        $full = ABSPATH . $rel_file;

        if ( ! file_exists( $full ) ) {
            if ( $should_be_writable ) {
                $issues[] = "Erwartete Datei fehlt: $rel_file";
            }
            continue;
        }

        $writable = is_writable( $full );

        if ( $should_be_writable && ! $writable ) {
            $issues[] = "$rel_file $msg (aktuell nicht beschreibbar)";
        } elseif ( ! $should_be_writable && $writable ) {
            $issues[] = "$rel_file $msg (aktuell beschreibbar – ändere auf 644!)";
        }
    }

    // === Realer Schreibtest 1: In uploads/ ===
    $upload = wp_upload_dir();
    if ( ! empty( $upload['basedir'] ) && is_dir( $upload['basedir'] ) ) {
        $test_file = $upload['basedir'] . '/.wp-health-test-' . time() . '.txt';
        if ( false === @file_put_contents( $test_file, 'test' ) ) {
            $issues[] = 'Uploads-Verzeichnis: Kann keine Datei schreiben (echter Fehler)';
        } else {
            @unlink( $test_file );
        }
    } else {
        $issues[] = 'Uploads-Verzeichnis: Konnte nicht ermittelt werden';
    }

    // === Realer Schreibtest 2: Direkt in wp-content/ (Fallback) ===
    $test_file_root = ABSPATH . 'wp-content/.wp-health-test-root-' . time() . '.txt';
    if ( false === @file_put_contents( $test_file_root, 'test' ) ) {
        if ( empty( $issues ) ) {  // Nur melden, wenn noch keine anderen Issues
            $issues[] = 'wp-content/: Kann keine Datei im Root schreiben (meist harmlos, aber prüfe Rechte)';
        }
    } else {
        @unlink( $test_file_root );
    }

    // === Ergebnis ===
    if ( empty( $issues ) ) {
        return [
            'label'       => __( 'wp-content ist voll funktionsfähig beschreibbar' ),
            'status'      => 'good',
            'badge'       => [ 'label' => __( 'Sicherheit' ), 'color' => 'green' ],
            'description' => '<p>Alle geprüften Ordner, Dateien und Real-Tests erfolgreich. Keine Probleme mit Updates/Uploads erwartet.</p>',
            'test'        => 'custom_test_content_writable_extended',
        ];
    }

    return [
        'label'       => __( 'wp-content Schreibrechte-Probleme gefunden' ),
        'status'      => 'critical',
        'badge'       => [ 'label' => __( 'Sicherheit' ), 'color' => 'red' ],
        'description' => '<p>Probleme:' . '</p><ul><li>' . implode( '</li><li>', array_map( 'esc_html', $issues ) ) . '</li></ul>'
                       . '<p><strong>Tipps:</strong> Ordner → 755/775, Dateien → 644/664. Owner/Group: www-data. Keine 777!</p>',
        'test'        => 'custom_test_content_writable_extended',
    ];
}
