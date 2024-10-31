<?php
/*
Plugin Name: RealtyCandy MailChimp IDX Broker Connector
Plugin URI:  http://realtycandy.com/
Description: Import Leads from IDXBroker to MailChimp using RealtyCandy
Version:     0.1
Author:      realtycandy
Author URI:  http://realtycandy.com
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}
require_once( __DIR__ . '/includes/idx-api-helper/helpercurl.php' );
define( 'IDX_AM',                 'Idx_Addons_Mailchimp' );
define( IDX_AM.'_NAME',                 'IDX Addons Mailchimp' );
if(!defined(IDX_AM.'_API')){
	define( IDX_AM.'_DOMAIN',                 'http://beta.idxaddons.com/rc-bridge-api/v1/' );
	define( IDX_AM.'_API',                 get_option('idx_broker_apikey') );
}

define( IDX_AM.'_REQUIRED_PHP_VERSION', '5' );                          // because of get_called_class()
define( IDX_AM.'_REQUIRED_WP_VERSION',  '3.1' );                          // because of esc_textarea()
define(IDX_AM.'_URL',plugins_url().'/'.strtolower('idxadoons-leads').'/');


/**
 * Checks if the system requirements are met
 *
 * @return bool True if system requirements are met, false if not
 */
function IDX_AM_requirements_met() {
	global $wp_version;

	if ( version_compare( PHP_VERSION, IDX_AM.'_REQUIRED_PHP_VERSION', '<' ) ) {
		return false;
	}

	if ( version_compare( $wp_version, IDX_AM.'_REQUIRED_WP_VERSION', '<' ) ) {
		return false;
	}

	return true;
}

/**
 * Prints an error that the system requirements weren't met.
 */
function IDX_AM_requirements_error() {
	global $wp_version;

	require_once( dirname( __FILE__ ) . '/views/requirements-error.php' );
}

/*
 * Check requirements and load main class
 * The main program needs to be in a separate file that only gets loaded if the plugin requirements are met. Otherwise older PHP installations could crash when trying to parse it.
 */
if ( IDX_AM_requirements_met() ) {
	require_once( __DIR__ . '/classes/wpps-module.php' );
	require_once( __DIR__ . '/includes/admin-notice-helper/admin-notice-helper.php' );
	require_once( __DIR__ . '/includes/GUMP/gump.class.php' );
	
	require_once( __DIR__ . '/classes/idxaddons-cron.php' );
	require_once( __DIR__ . '/classes/idxaddons-settings-page.php' );
	require_once( __DIR__ . '/classes/idxaddons-main-page.php' );
	require_once( __DIR__ . '/classes/wordpress-idx-mailchimp.php' );


	if ( class_exists( 'Idx_Addons_Mailchimp' ) ) {
		$GLOBALS['wpps'] = Idx_Addons_Mailchimp::get_instance();
		register_activation_hook( __FILE__, array( $GLOBALS['wpps'], 'activate' ) );
		register_deactivation_hook( __FILE__, array( $GLOBALS['wpps'], 'deactivate' ) );		
	}
} else {
	add_action( 'admin_notices', 'wpps_requirements_error' );
}
