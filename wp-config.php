<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'mentorhelp' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         'rn`6Iw_fwcGsnY_1X%11Y|;cl:cf<|x4weMdJ6.2WTuHHDc&rZ@po$ _+?^2<BtR' );
define( 'SECURE_AUTH_KEY',  'kVE2=-Z?-guNlk[D_D[0a4y;mh0,[)pscre}ZhL 3#8lggO^++@L]6e|TA3uyhRU' );
define( 'LOGGED_IN_KEY',    ']2f`nPYAB2nk!-T~S`82^:S_Hv=wauH)E1Lo@!o0[]P`w>&r-zs<D%9qM~~[wh49' );
define( 'NONCE_KEY',        'H],55EG]epT-Urv4HMH63`RiGDFOhVq:}W/CQ%D<HgFdf%I`]A2u6~Es!W@7v.pd' );
define( 'AUTH_SALT',        'GMEVJk,_X=4Yct%_e}8up-DF|wXk|5aU|^|xoQu P]Edc(O2O` ceI`^--|~zU:~' );
define( 'SECURE_AUTH_SALT', 'j7(H 4!be#afI<{M)X(NQ(bMf0pnet*TvKx_#s`x$4(xVy^HEhdqo6R|@lSreth<' );
define( 'LOGGED_IN_SALT',   'Vf+]|=w}91[(ovD` tFjCanqhdM-mnz|P0!iG9vQS#es~lIk~T?P9,M<&ie|i^@v' );
define( 'NONCE_SALT',       'sg3=ELGw9I86qwa/is1}..{_U?e(/8+Ddi|cn,:V1Cf(9-9[M7kgyov7q<)KguQU' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
