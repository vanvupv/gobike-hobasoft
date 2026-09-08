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
define( 'DB_NAME', 'thangma1_gobike' );

/** Database username */
define( 'DB_USER', 'thangma1_gobike' );

/** Database password */
define( 'DB_PASSWORD', 'bpj]3!W#MIhs4_.H' );

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
define( 'AUTH_KEY',         'c}@E+*+i(s68C)-1#ENq@i).#>+XjB~z,rcHHJN:M =s}qP*Q:8EQM5ev.3BWU{_' );
define( 'SECURE_AUTH_KEY',  'DR/MZo3H` b(Gp%2mv;_bP|oSF)4T7p(CL3&kvj|1%M+Vg%Q[Dg2LA}(#uo$5(|u' );
define( 'LOGGED_IN_KEY',    'pzdTX39Y)/(z}K{+VND%mS*6 v#7;mVfefmT]IJ4T>unK(E92_KTdC-cgH5I6Z&F' );
define( 'NONCE_KEY',        'W0%IV6|,,Xku8-GGX3-X0s%`kHf+G%$BpeAHc<nYyQ&!Yu%vli&H%OW$:w,c<bdh' );
define( 'AUTH_SALT',        'z6Bas%IWDl~;rhdrQ=^F%s6F@F gw5]PO^..R1~1)z&kb>(ee}[*9UV,V`ucC0@%' );
define( 'SECURE_AUTH_SALT', ')=[5f`)KxcJU{h,2#GY.BZ+:(iSC<}|,uag`R0hD K#HKhn-Uo~^2t_`@H=D{I@V' );
define( 'LOGGED_IN_SALT',   'Wqa4%ra3{O+E}<CTb}=~EHJcO0;T[cDqR3CpC+UKlzut00RBMRr*YOu/,^(Iz~AH' );
define( 'NONCE_SALT',       'NzR$j>z2@%&a<s`C6M<OuxXI8W}_z9j)i}jlqIdGsK33Q&f#C~bAh$69EV3),^`i' );

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

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
