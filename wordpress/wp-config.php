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

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'wpuser' );

/** Database password */
define( 'DB_PASSWORD', 'Ein:Sehr:Langes:Passwort:2025!' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

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
define( 'AUTH_KEY',         '6c|w W^%v=F1wc&:7H!fO`~-ng0KUFn:L^%De0%k0d:^fPI9oT#>1rn?G<Ja<~+E' );
define( 'SECURE_AUTH_KEY',  'DGTa4n:]~?[Zc>UUbgHiyUbBJd$J&0?X{.^Iy.}-c0V~TJg=iKHU=% )C]1-QlBk' );
define( 'LOGGED_IN_KEY',    'pnWZLxp);!iI6hlN0.L98Z=XH{g#Sh4!%H:GJERs@Y.>/SBxlEg#^[Et22L* x/A' );
define( 'NONCE_KEY',        ',K;y(-0PG>2r;m|.@sVw,HzFMS2axZ60;b0ZD4l0mu~7*5fAbI=-gj2]V%LkApui' );
define( 'AUTH_SALT',        'Y:nS04VrsPh!kPy* UM@D#!OlvVkNX+eC!uC|?y.V+PM*`BjC/g;lBX$|(iQ7~`5' );
define( 'SECURE_AUTH_SALT', '[11OAVEIK;:<($x+*ag!;?ZOLM >1e<Pf=``=-[KFQP^iz/P9<ISK.[7N+H{wVj)' );
define( 'LOGGED_IN_SALT',   '](CI v]#DMJq5vu|Gpb~xB<YDR^{]^)FovwR8-{lF9]alqZ[Yk~pD16SgK=HLzBW' );
define( 'NONCE_SALT',       'c8X_m^d.[nlvin& <7^e+^/~4 ?&8C~LjEo1/kSL=w|>)I/u%E(VhRXJ1|>=h8Ll' );

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
$table_prefix = 'se_';

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
define( 'WP_DEBUG', true );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
