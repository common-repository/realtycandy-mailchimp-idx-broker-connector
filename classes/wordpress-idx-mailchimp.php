<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! class_exists( 'Idx_Addons_Mailchimp' ) ) {

	/**
	 * Main / front controller class
	 *
	 * Idx_Mailchimp is an object-oriented/MVC base for building WordPress plugins
	 */
	class Idx_Addons_Mailchimp extends Idx_Addons_Mailchimp_Module {
		protected static $readable_properties  = array();    // These should really be constants, but PHP doesn't allow class constants to be arrays
		protected static $writeable_properties = array();
		protected $modules;

		const VERSION    = '0.1';
		const PREFIX     = 'idxaddons_idx_mailchimp';
		const DEBUG_MODE = true;


		/*
		 * Magic methods
		 */

		/**
		 * Constructor
		 *
		 * @mvc Controller
		 */
		protected function __construct() {
			$this->register_hook_callbacks();

			$this->modules = array(				
				'Idx_Addons_Mailchimp_Sync' => Idx_Addons_Mailchimp_Sync::get_instance(),
				'Idx_Addons_Mailchimp_Setting' => Idx_Addons_Mailchimp_Setting::get_instance(),
				'Idx_Addons_Mailchimp_Cron'        => Idx_Addons_Mailchimp_Cron::get_instance()
			);
		}


		/*
		 * Static methods
		 */

		/**
		 * Enqueues CSS, JavaScript, etc
		 *
		 * @mvc Controller
		 */
		public static function load_resources() {			

			wp_register_script(self::PREFIX . 'script-idx-mailchimp',plugins_url( 'javascript/idxaddons-mailchimp.js', dirname( __FILE__ ) ),array( 'jquery' ),
				self::VERSION,true);
			//Bootstrap
			wp_register_script( 'bootstrap', '//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js', array('jquery'), 3.3, true);
			wp_register_style('bootstrap-styles', '//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css', array(), null, 'all');

			//Data Tables
			wp_register_script( 'datatables', '//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js', array('jquery'), 3.3, true);
			wp_register_style('datatables-styles', '//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css', array(), null, 'all');

			wp_register_style(self::PREFIX . 'admin-styles',plugins_url( 'css/style-mailchimp.css', dirname( __FILE__ ) ),array(),self::VERSION,'all');

			if ( is_admin() ) {
				wp_enqueue_script(self::PREFIX . 'sync-list-idx-mailchimp',plugins_url( 'javascript/sync-idx-mailchimp.js', dirname( __FILE__ ) ),array( 'jquery' ),self::VERSION,true);

				wp_localize_script( self::PREFIX . 'sync-list-idx-mailchimp', 'sync_ajax_object', array( 'ajaxurl' => "http://beta.idxaddons.com/rc-bridge-api/v1/idx-mailchimp" ,'apikey' => get_option('idx_broker_apikey')));


				$screen = get_current_screen();
				if ( in_array( $screen->id, array('toplevel_page_mailchimp-idx','mailchimp-idx_page_settings-mailchimp-idx'))) {
					wp_enqueue_script('bootstrap');
					wp_enqueue_style('bootstrap-styles');
					wp_enqueue_style( self::PREFIX . 'admin-styles' );
					//Data Tables
					wp_enqueue_script('datatables');
					wp_enqueue_style('datatables-styles');
				}


				$prowp_options = get_option( 'wpps_settings' );
				if ($prowp_options) {
					$enable_auto_sync = $prowp_options['basic']['enable_auto_sync'];
					$list_id = $prowp_options['basic']['sync_list'];
					if ($enable_auto_sync=="y" && $list_id != "") {
						wp_enqueue_script( self::PREFIX . 'script-idx-mailchimp' );
					    wp_localize_script( self::PREFIX . 'script-idx-mailchimp', 'idx_mailchimp_ajax_object', array( 'ajaxurl' => "http://beta.idxaddons.com/rc-bridge-api/v1/idx-mailchimp" ,'apikey' => get_option('idx_broker_apikey'),'list_id'=>$list_id));
					    wp_enqueue_script( self::PREFIX . 'script-idx-mailchimp' );
					}
				}



			}
		}
		


		/*
		 * Instance methods
		 */

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @mvc Controller
		 *
		 * @param bool $network_wide
		 */
		public function activate( $network_wide ) {
			if ( $network_wide && is_multisite() ) {
				$sites = wp_get_sites( array( 'limit' => false ) );

				foreach ( $sites as $site ) {
					switch_to_blog( $site['blog_id'] );
					$this->single_activate( $network_wide );
					restore_current_blog();
				}
			} else {
				$this->single_activate( $network_wide );
			}
		}

		/**
		 * Prepares a single blog to use the plugin
		 *
		 * @mvc Controller
		 *
		 * @param bool $network_wide
		 */
		protected function single_activate( $network_wide ) {
			foreach ( $this->modules as $module ) {
				$module->activate( $network_wide );
			}

			flush_rewrite_rules();
		}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @mvc Controller
		 */
		public function deactivate() {
			foreach ( $this->modules as $module ) {
				$module->deactivate();
			}

			flush_rewrite_rules();
		}

		/**
		 * Register callbacks for actions and filters
		 *
		 * @mvc Controller
		 */
		public function register_hook_callbacks() {

			add_action( 'wp_enqueue_scripts',    __CLASS__ . '::load_resources' );
			add_action( 'admin_enqueue_scripts', __CLASS__ . '::load_resources' );

			//add_action( 'init',                  array( $this, 'init' ) );
			//add_action( 'init',                  array( $this, 'upgrade' ), 11 );
		}

		/**
		 * Initializes variables
		 *
		 * @mvc Controller
		 */
		public function init() {			
		}

		/**
		 * Checks if the plugin was recently updated and upgrades if necessary
		 *
		 * @mvc Controller
		 *
		 * @param string $db_version
		 */
		public function upgrade( $db_version = 0 ) {			
		}

		/**
		 * Checks that the object is in a correct state
		 *
		 * @mvc Model
		 *
		 * @param string $property An individual property to check, or 'all' to check all of them
		 * @return bool
		 */
		protected function is_valid( $property = 'all' ) {
			return true;
		}
	} // end Idx_Mailchimp
}
