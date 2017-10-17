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
define('DB_NAME', 'ec_sbca');

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
define('AUTH_KEY',         'A3=T%[sn)dw;[VT~U4g^z%3.#3ZC]4Ma[=|{/,j39&ZLa:P$r05$yG]7Yz!cz1&x');
define('SECURE_AUTH_KEY',  'UerNtu.`I[h:$^9[M2p 1/#v]&OP6q5}zC&aw/1d9)ybWF^>UV)wUTd0=KN{Wxn*');
define('LOGGED_IN_KEY',    'C[k4?<_jnk-*a@OuxY0$.0l.~(;TE4diV$=itW%Qw?3;aV/F]Xezvv5n~>e^|Q|:');
define('NONCE_KEY',        'A/6Yzh 9*J1Ab.t:u6fsAtCYhI+}JR>2J+/WJ82nMC;RiVr9}j?FNddDb;+{;wV8');
define('AUTH_SALT',        '&8FpmPWhj}1wj3m1uy9I [ebyDf8j_+AfwS$/L/:<q`+;%30:@bi.6H0m=cUJ.us');
define('SECURE_AUTH_SALT', 'VfTtdeB5,}-)}Nx[~XD/RRw4#bKaZ3M%T;Q,)zjrKPBf^Rga1cnWiL(E,_pMPJMX');
define('LOGGED_IN_SALT',   'O1M$PU&~u-2L-VP&Wp.B8:H+I:261fZ2{fTQ6YA6L@sFsbw7R{7/H}:{*SpHgDMZ');
define('NONCE_SALT',       'mts9qPu)>%eo,`29nG|QV[6j83{W(8enrmCQi#?>nEUg.gseT7T$S$C89V#)zq&c');
define('REST_API_SECRET_KEY', 'oVFY^Q9C!d+..t7aq4f,`JgX,c-%/`P9x0gAo(;G|8C2`*b`e!2gmdOzM#uB.`?7');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'ec_';

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

// Enable Debug logging to the /wp-content/debug.log file
define( 'WP_DEBUG_LOG', false );
