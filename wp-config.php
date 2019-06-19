<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'WPCACHEHOME', '/home/clients/67c1fe57fb0cf88e78fd0bdc5a9b56b7/wpdadoo/wp-content/plugins/wp-super-cache/' );
define('WP_CACHE', true);
define('DB_NAME', 'pcbj_WP284045');

/** MySQL database username */
define('DB_USER', 'pcbj_WP284045');

/** MySQL database password */
define('DB_PASSWORD', '3ue4OK2DEK');

/** MySQL hostname */
define('DB_HOST', 'pcbj.myd.infomaniak.com');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'U-~Qk:u_neItvfR}59fFxoRh->UE-Q#M%WT:jGSBx!xO&+G0G1e?SAKN;t7CL+,z');
define('SECURE_AUTH_KEY',  'v13_k{gZ7}Z6eVxl*53TBSB44}<-0d8v3,TE439w#jvd{cHDnq(BLKzo&73vo^_I');
define('LOGGED_IN_KEY',    'g(=aI/27~H_&5O4-<MFkpBm&>7mcMmFih62d5N_nNEHHx/T{Hi3ry0mSOtk~k485');
define('NONCE_KEY',        '#Kipm{#<6BcC~RXnp<otqeb3-*v+elFeWs8&dkIxoh3:Lc2~7=&s^01QOB+I4*Dw');
define('AUTH_SALT',        'ZK6z_-@H(4CRF&O4h/~lm(yx&o|ft~LJEd`CW.a*4tR2//s<J1*@(Z=/U.vQ4dz|');
define('SECURE_AUTH_SALT', 'F),ed4NP>5}C:.fHyB(<s|{eKMyg%qYd{c5bptF&(swP71,t79}Ab)&@d<x!VXDO');
define('LOGGED_IN_SALT',   'C:%@6dRlN2?:yymqIis{*hUFnJwl8hm+L14VFgNhWmUosf`c0)-}V~7Js>H/~iJb');
define('NONCE_SALT',       'geTn,@kUo:%W;L>l5={h@TPnjX=jY<YK:%:zO.L(m.q02j_*)WfSYn_lR,R7N,S9');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_284045_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
//define('WPLANG', 'fr_FR');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG',          false);
define('WP_DEBUG_LOG',      false);
define('WP_DEBUG_DISPLAY',  false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
