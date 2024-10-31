<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! class_exists( 'Idx_Addons_Mailchimp_Cron' ) ) {

	/**
	 * Handles cron jobs and intervals
	 *
	 * Note: Because WP-Cron only fires hooks when HTTP requests are made, make sure that an external monitoring service pings the site regularly to ensure hooks are fired frequently
	 */
	class Idx_Addons_Mailchimp_Cron extends Idx_Addons_Mailchimp_Module {
		protected static $readable_properties  = array();
		protected static $writeable_properties = array();
	
		/**
		 * Constructor
		 *
		 * @mvc Controller
		 */
		protected function __construct() {
			$this->register_hook_callbacks();
		}

		/**
		 * Adds custom intervals to the cron schedule.
		 *
		 * @mvc Model
		 *
		 * @param array $schedules
		 * @return array
		 */
		public static function add_custom_cron_intervals( $schedules ) {
			$schedules[ 'everytwohours' ] = array(
				'interval' => 20,
				'display'  => 'Every 2 hours'
			);

			$schedules['weekly'] = array(
				'interval' => 604800,
				'display' => __('Once Weekly')
			);
			
			return $schedules;
		}

		/**
		 * Fires a cron job at a specific time of day, rather than on an interval
		 *
		 * @mvc Controller
		 */
		public static function fire_job_at_time() {
			$now = current_time( 'timestamp' );

			// Example job to fire between 1am and 3am
			if ( (int) date( 'G', $now ) >= 1 && (int) date( 'G', $now ) <= 3 ) {
				if ( ! get_transient( 'wpps_cron_example_timed_job' ) ) {
					//WPPS_CPT_Example::exampleTimedJob();
					set_transient( 'wpps_cron_example_timed_job', true, 60 * 60 * 6 );
				}
			}
		}

		/**		
		 *
		 * @mvc Model
		 *
		 * @param array $schedules
		 * @return array
		 */
		public static function cron_sync_list($list_id) {
			
			// Do stuff
			$data = array(
							'apikey' => get_option('idx_broker_apikey'),
							'list_id' => $list_id						
					);
			$response = IdxAddons_Mailchimp_Page::idxaddons_connect_bridge_txt_msg("http://beta.idxaddons.com/rc-bridge-api/v1/",'idx-mailchimp', $data);
			error_log($response, 0);
			
		}


		/*
		 * Instance methods
		 */

		/**
		 * Register callbacks for actions and filters
		 *
		 * @mvc Controller
		 */
		public function register_hook_callbacks() {			

			$prowp_options = get_option( 'wpps_settings', array() );

			if ($prowp_options) {
				$enable_cron_idx_mailchimp = $prowp_options['cron']['enable_cron_idx_mailchimp'];
				$cron_time = $prowp_options['cron']['cron_time'];
				$list_id = $prowp_options['basic']['sync_list'];
				if ($enable_cron_idx_mailchimp == "y" && $cron_time != "none" && $list_id != "") {
					
					add_action( 'idxaddond_cron_mailchimp',  __CLASS__ . '::cron_sync_list');
					add_action( 'init',                  array( $this, 'init' ) );
					add_filter( 'cron_schedules',        __CLASS__ . '::add_custom_cron_intervals' );

					if ( wp_next_scheduled( 'idxaddond_cron_mailchimp',array($list_id) ) === false ) {
						wp_schedule_event(
							current_time( 'timestamp' ),
							 $cron_time,
							'idxaddond_cron_mailchimp',
							 array($list_id)
						);
					}
					
				}elseif($enable_cron_idx_mailchimp == "n"){

					$timestamp = wp_next_scheduled( 'idxaddond_cron_mailchimp' );
					wp_unschedule_event( $timestamp, 'idxaddond_cron_mailchimp',array($list_id));
					wp_clear_scheduled_hook( 'idxaddond_cron_mailchimp',array($list_id) );
				}
			}
			
		}

		/**
		 * Prepares site to use the plugin during activation
		 *
		 * @mvc Controller
		 *
		 * @param bool $network_wide
		 */
		public function activate( $network_wide ) {
						
		}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @mvc Controller
		 */
		public function deactivate() {
			$prowp_options = get_option( 'wpps_settings', array() );

			if ($prowp_options) {
				$list_id = $prowp_options['basic']['sync_list'];
				wp_clear_scheduled_hook( 'idxaddond_cron_mailchimp',array($list_id) );
			}
			
		}

		/**
		 * Initializes variables
		 *
		 * @mvc Controller
		 */
		public function init() {
		}		
		public function upgrade( $db_version = 0 ) {			
		}
		protected function is_valid( $property = 'all' ) {
			return true;
		}
	} // end Idxaddons_Cron
}
