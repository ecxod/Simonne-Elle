<?php

add_action('after_setup_theme', function() {
    // Nur einmal ausführen – danach wieder auskommentieren!
    $caps = get_role('administrator')->capabilities;

    // Entferne die kritischen Rechte
    unset($caps['install_plugins']);
    unset($caps['activate_plugins']);
    unset($caps['delete_plugins']);
    unset($caps['edit_plugins']);
    unset($caps['update_plugins']);

    unset($caps['install_themes']);
    unset($caps['switch_themes']);
    unset($caps['delete_themes']);
    unset($caps['edit_themes']);
    unset($caps['update_themes']);

    // Optional: auch das hier raus, wenn wirklich ALLE Einstellungen gesperrt sein sollen
    // unset($caps['manage_options']);

    add_role(
        'admin_no_extensions',
        'Admin ohne Plugins & Themes',
        $caps
    );
});

