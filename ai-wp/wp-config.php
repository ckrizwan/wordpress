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
define( 'DB_PASSWORD', 'Wp@12345!' );

/** Database hostname */
define( 'DB_HOST', 'db:3306' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

define( 'WP_ENVIRONMENT_TYPE', 'local' );

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
define('AUTH_KEY',         '^;5neu@|3Orh-&zW!u5zo5|zmoq-NoRS|A9ymu!B`w-;+f#0}P76?-|nN.Fx=B:$');
define('SECURE_AUTH_KEY',  'tld$>0S)Yh)9UFpa(6%Z#.a=*^F4(U ;v<zl(OXOB~*Z-Zds:.f)9F9)g2Zc/`fM');
define('LOGGED_IN_KEY',    'PK1s@I4%h<)7%fa*7g-dm-+P9s!^-m.D-U4ZuT17!k^a3g-_<d!zY^PeAVps}^(<');
define('NONCE_KEY',        '(-n-;f4Y|0ovd<|}itM5wG|x-IQ3Qb%q%toW@++auR#C|-VDe57f4|wPQ7*FVETL');
define('AUTH_SALT',        't;a}(By*Y#TWO)Cl6ExSCV~]C5B7Q_Dq6{*F(6`HI,fFbsn?<8q!C}:^b-~R6*;0');
define('SECURE_AUTH_SALT', '=jN+$Ek.UDHyFSq}&qh<|Nf[cxWe#0S)4gef?fG.>ArLs)o0#_z-Ua|BL*!`>}uT');
define('LOGGED_IN_SALT',   'r;5YMHK~Y|Ey@:?/6x5R;|x%|;?DdA|=gKQ2CDR|Fqh$J$o){xyu+9=z+aUBIDL~');
define('NONCE_SALT',       'U./AxP[6,Z[R<:<P|R;D?HZpZ.5t)l|j@@A5OzyJBY5!7*zq0Y>?+sNNIFtAI3_K');

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

define('FS_METHOD', 'direct');

define('JWT_AUTH_SECRET_KEY', 'WQ#6x!4%kf7PuMc$K9@jh^mwe!93$Zw9Ax3S*a)SDs81@ds9Vs');
define('JWT_AUTH_CORS_ENABLE', true);


/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
