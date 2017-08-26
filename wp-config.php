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
define('DB_NAME', 'wordpress-db-restauran');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         '5#`lM[gVA @:0a&UdO{oHdv+ r^^f2U--+c=LIx{dSi f7E-JYlJya./.iwUtT~8');
define('SECURE_AUTH_KEY',  '`6R)i`p=8<^M}wfm<?ZmXd|)|6.U|m4C*a.hkLJ<2p*<.qyjB/SG2F=uz46B/K$A');
define('LOGGED_IN_KEY',    'v$fi@i^RI`Cd42Zi|zPv(VCsRJaSDpXN<H+9=JhCIlL88^#+)^r~/6Q%TRZF:(+L');
define('NONCE_KEY',        'SHCIp5<F,Cwcl9L>v;yfveCZfG@4SbWg`hX;Grd2?p7So&6C?{B?d`EU,bn,&tJ{');
define('AUTH_SALT',        '4lziF`C(B473ZB:6d+G{iVruywL1(8k@O: Qcrjo+<R2Td|@buYC;6nt^>LhzMqr');
define('SECURE_AUTH_SALT', '92rY5glZ;KP59:;Q_?TDZ|e8V12ZIp,F T5v7*lt{yc)`7,$k2!w;.vY~O7u.Y#<');
define('LOGGED_IN_SALT',   '8)gCUK{*=+.t!8zV.UUa{MsCUJfFFEp;+pM0W,0|rv$#I j7/L[#H~WXzYz~DyxB');
define('NONCE_SALT',       'nOD.0@s9_!I|4[L5i we>f[[fbv[MXPT2UeFVr*]q.wB^]bW[27P(Z>>J2|XYKKq');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
