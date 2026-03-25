<?php

/** 
 * @Package: Ecxod\Admin_with_no_extensions
 *
 * Plugin Name:       Admin with no extensions
 * Text Domain:       admin-with-no-extensions
 * Plugin URI:        https://github.com/ecxod/admin-with-no-extensions
 * Description:       Create admins who can not install extensions, plugins or themes
 * Author:            Christian Eichert <mailto:c@zp1.net>
 * Author URI:        https://github.com/ecxod
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Version:           1.0
 * Date:              03.03.2026
 */
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

