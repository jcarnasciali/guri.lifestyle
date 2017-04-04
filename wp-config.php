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
define('DB_NAME', 'gurilifestyle');

/** MySQL database username */
define('DB_USER', 'gurilifestyle');

/** MySQL database password */
define('DB_PASSWORD', 'bildstudio1236987450');

/** MySQL hostname */
define('DB_HOST', 'mysql.gurilifestyle.com');

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
define('AUTH_KEY',         'kaKL&F9N_iWtTjxC~]-2?rZBy8XLWM76sEG/yfST,c<EN.yA42SIoRCfZI2Q9;29');
define('SECURE_AUTH_KEY',  'y{^x{xR&1k4;,EaqRE}_7(ntArm5JsAs5A)tS/bL1&)2LW[W2U|. HD[P===Ze8-');
define('LOGGED_IN_KEY',    '|{qc])7/>zjc[bY?E%eO:z_Y.3hdpCO5r*A=iX_a6;+EE}UHZ5EP;4FMI NDv$Xx');
define('NONCE_KEY',        'r;Q[<SVh{|Ee bA;9bv|]x]3 &Q`|lDTA|>(++c7Y$<:dx~w-q^8t!tSI|Bk}k1d');
define('AUTH_SALT',        '&l;(p(zR]WTLM,&-oNLABZ[ cT_2y V[U$~ANW &+vG?`wbL+xn#L[|uN%`>k?kf');
define('SECURE_AUTH_SALT', 'Y`_Dy>HZo7]P^Y`=W?2hE!hQ <{VeK|e&T3o^)<x+ +E3W{<H`,`[66nhWue+rrK');
define('LOGGED_IN_SALT',   '+,fnz)q90c|b_hDA})V#1sHb&*7O_o%[>pFyTj7#oMB-lR?3f-uY kg.dCZ!~_#b');
define('NONCE_SALT',       'dpR!`_SEvfDG_b9+=*eSx^FA8rC4|2-L$8>5h{4^-R7O-bV2%WO@uY`Xi#0!D/,L');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wpgl_';

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
ini_set('log_errors','On');
ini_set('display_errors','Off');
ini_set('error_reporting', E_ALL );
define('WP_DEBUG', false);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

define( 'WP_MEMORY_LIMIT', '256M' );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
