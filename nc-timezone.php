<?php
/**
 * Plugin Name: NC TimeZone
 * Description: TimeZone Widget for NC.
 * Author:      Benedick Sahagun
 * Version:     0.0.1
 * Text Domain: nc-timezone
 * Author URI:	https://bennn.work/
*/

/** Define ABSPATH as this file's directory */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

define( 'NCTIMEZONE__PLUGIN_NAME','NC Amp' );
define( 'NCTIMEZONE__PLUGIN_VERSION','0.0.1' );
define( 'NCTIMEZONE__PLUGIN_DIR', dirname( __FILE__ ) );

function nc_enqueue_scripts() {
	wp_enqueue_style( 'nc-timezone-style', plugins_url('/assets/css/styles.css', __FILE__ ));
	wp_enqueue_script( 'nc-timezone-scripts', plugins_url('/assets/js/scripts.js', __FILE__ ), array('jquery'));
	wp_enqueue_script( 'nc-timezone-scripts-removebtn', plugins_url('/assets/js/jquery-1.10.2.js', __FILE__ ), array('jquery'));
	wp_localize_script( 'nc-timezone-scripts', 'nc_timezone', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}

add_action( 'wp_enqueue_scripts', 'nc_enqueue_scripts' );

function nc_activation_redirect( $plugin ) {
    if( $plugin == plugin_basename( __FILE__ ) ) {
        exit( wp_redirect( admin_url( 'options-general.php?page=nc-timezone-settings' ) ) );
    }
}
add_action( 'activated_plugin', 'nc_activation_redirect' );


include(dirname(__FILE__) .'/includes/admin.php');
include(dirname(__FILE__) .'/includes/timezone.php');
include(dirname(__FILE__) .'/includes/shortcode.php');