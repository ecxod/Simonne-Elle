<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// TODO: ABSPATH ermittlung muss man härten ..
/** Absolute path to the WordPress directory. 
 * /var/www/simonneelle_de/wordpress/
 */
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}
$autoload = realpath(dirname(ABSPATH,1) . '/vendor/autoload.php');
if (file_exists($autoload)) {
    require_once $autoload;
}

$envfile = realpath(dirname(ABSPATH, 1));
$dotenv = Dotenv\Dotenv::createImmutable($envfile);
$dotenv->load();
# $dotenv->safeLoad();
$dotenv->required('SENTRY_DSN');

\Sentry\init(['dsn' => $_ENV['SENTRY_DSN']]);

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME',     $_ENV['DB_NAME'] );
/** Database username */
define( 'DB_USER',     $_ENV['DB_USER'] );
/** Database password */
define( 'DB_PASSWORD', $_ENV['DB_PASSWORD'] );
/** Database hostname */
define( 'DB_HOST',     $_ENV['DB_HOST'] );
/** Database charset to use in creating database tables. */
define( 'DB_CHARSET',  $_ENV['DB_CHARSET'] );
/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE',  $_ENV['DB_COLLATE'] );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         $_ENV['AUTH_KEY'] );
define( 'SECURE_AUTH_KEY',  $_ENV['SECURE_AUTH_KEY'] );
define( 'LOGGED_IN_KEY',    $_ENV['LOGGED_IN_KEY'] );
define( 'NONCE_KEY',        $_ENV['NONCE_KEY'] );
define( 'AUTH_SALT',        $_ENV['AUTH_SALT'] );
define( 'SECURE_AUTH_SALT', $_ENV['SECURE_AUTH_SALT'] );
define( 'LOGGED_IN_SALT',   $_ENV['LOGGED_IN_SALT'] );
define( 'NONCE_SALT',       $_ENV['NONCE_SALT'] );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = $_ENV['TABLE_PREFIX'];

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define('WP_DEBUG', true); // Debugging überhaupt einschalten
# define( 'WP_DEBUG_LOG', true );      // ← das ist das Wichtigste! → schreibt in ~/wp-content/debug.log
define('WP_DEBUG_LOG', '/var/log/wordpress/simonneelle_de/wp-errors.log');
define('WP_DEBUG_DISPLAY', false); // Fehler NICHT auf der Website anzeigen (sehr wichtig bei Live-Seiten!)
@ini_set('display_errors', 0);

# Das deaktiviert den Plugin- und Theme-Editor sowie alle Update-Benachrichtigungen im Dashboard komplett.
define( 'DISALLOW_FILE_MODS', true );

/* Add any custom values between this line and the "stop editing" line. */

/** REDIS */
define( 'WP_REDIS_HOST',     $_ENV['WP_REDIS_HOST'] );
define( 'WP_REDIS_PORT',     $_ENV['WP_REDIS_PORT'] );
define( 'WP_REDIS_DATABASE', $_ENV['WP_REDIS_DATABASE'] );
define( 'WP_CACHE_KEY_SALT', $_ENV['WP_CACHE_KEY_SALT'] );

/**
 * Das zwingt WordPress, direkt über PHP zu schreiben (statt FTP-Fallback). 
 * Bei korrekten Rechten funktioniert das zu 95 %. 
 */
//define('FS_METHOD', 'direct');


// /**
//  * SMTP Konfiguration aus der .env
//  * Sorgt dafür, dass WordPress PHPMailer direkt deinen Postfix nutzt
//  */
// add_action( 'phpmailer_init', function( $phpmailer ) {
//     $phpmailer->isSMTP();
//     $phpmailer->Host       = $_ENV['SMTP_HOST'] ?? '127.0.0.1';
//     $phpmailer->SMTPAuth   = $_ENV['SMTP_AUTH'] === 'true';
//     $phpmailer->Port       = $_ENV['SMTP_PORT'] ?? 587;
//     $phpmailer->Username   = $_ENV['SMTP_USER'] ?? '';
//     $phpmailer->Password   = $_ENV['SMTP_PASS'] ?? '';
//     $phpmailer->SMTPSecure = $_ENV['SMTP_SECURE'] ?? 'tls';
//     $phpmailer->From       = $_ENV['SMTP_FROM'] ?? 'webmaster@deine-domain.de';
//     $phpmailer->FromName   = $_ENV['SMTP_NAME'] ?? 'WordPress Service';
// });



/* That's all, stop editing! Happy publishing. */



/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';