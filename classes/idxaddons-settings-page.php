<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! class_exists( 'Idx_Addons_Mailchimp_Setting' ) ) {
	
	/**
	 * Creates a custom page
	 */
	class Idx_Addons_Mailchimp_Setting extends Idx_Addons_Mailchimp_Module{
		protected $settings;
		public static $default_settings;
		protected $mc;
		protected $apikey_idx;
		protected $cron_time_available = array('none'=>'Frequency','hourly' => 'Once Hourly', 'everytwohours' => 'Every 2 hours' ,'daily' => 'Daily', 'twicedaily' => 'Twice Daily' , 'weekly' => 'Once Weekly');
		protected static $readable_properties  = array( 'settings' );
		protected static $writeable_properties = array( 'settings' );
		const REQUIRED_CAPABILITY = 'administrator';

		/**
		 * Constructor
		 *
		 * @mvc Controller
		 */
		protected function __construct() {
			$this->register_hook_callbacks();
		}

		/**
		 * Public setter for protected variables
		 *
		 * Updates settings outside of the Settings API or other subsystems
		 *
		 * @mvc Controller
		 *
		 * @param string $variable
		 * @param array  $value This will be merged with WPPS_Settings->settings, so it should mimic the structure of the WPPS_Settings::$default_settings. It only needs the contain the values that will change, though. See WordPress_Plugin_Skeleton->upgrade() for an example.
		 */
		public function __set( $variable, $value ) {
			// Note: Idx_Mailchimp_Module::__set() is automatically called before this
					
			if ( $variable != 'settings' ) {
				return;
			}
			$this->settings = self::validate_settings_setting_mailchimp( $value );				
			update_option( 'wpps_settings', $this->settings );
		}

		/**
		 * Retrieves all of the settings from the database
		 *
		 * @mvc Model
		 *
		 * @return array
		 */
		protected static function get_settings() {
			$settings = shortcode_atts(
				self::$default_settings,
				get_option( 'wpps_settings', array() )
			);

			return $settings;
		}

		/**
		 * Establishes initial values for all settings
		 *
		 * @mvc Model
		 *
		 * @return array
		 */
		protected static function get_default_settings() {
			$basic = array(
				'enable_auto_sync' => 'n',
				'sync_list' => 'false'
			);
			$cron = array(
				'enable_cron_idx_mailchimp' => 'n',
				'cron_time' => 'none'
			);			
			return array(
				'db-version' => '0',
				'basic'      => $basic,
				'cron' 		 => $cron			
			);
		}

		/**
		 * Register callbacks for actions and filters
		 *
		 * @mvc Controller
		 */
		public function register_hook_callbacks() {			
			add_action( 'admin_menu',                     __CLASS__ . '::create_main_page' );
			add_action( 'init',                     array( $this, 'init' ) );
			add_action( 'admin_init',               array( $this, 'register_settings' ) );
		}

		/**
		 * Registers the Pages
		 *
		 * @mvc Controller
		 */
		public static function create_main_page() {	
			    // Add a submenu 
   				add_submenu_page('mailchimp-idx', 'Settings', 'Settings', self::REQUIRED_CAPABILITY, 'settings-mailchimp-idx',__CLASS__ . '::main_mailchimp_idx');
		}
		
		/**
		 * Creates the markup for the Main page
		 *
		 * @mvc Controller
		 */
		public static function main_mailchimp_idx() {
			if ( current_user_can( self::REQUIRED_CAPABILITY ) ) {
				
					$actions = (isset($_GET['action']))?$_GET['action']:'';
					switch ($actions) {						
						default:
							echo self::render_template( 'idxaddons-mailchimp-setting/page-settings.php' );
							break;
					}
				
			} else {
				wp_die( 'Access denied.' );
			}
		}			

		/**
		 * Registers settings sections, fields and settings
		 *
		 * @mvc Controller
		 */
		public function register_settings() {
			/*
			 * Basic Section
			 */
			add_settings_section(
				'wpps_section-basic',
				'Settings',
				__CLASS__ . '::markup_section_headers',
				'wpps_settings'
			);

			add_settings_field(
				'enable_auto_sync',
				'Enable auto-sync',
				array( $this, 'markup_fields' ),
				'wpps_settings',
				'wpps_section-basic',
				array( 'label_for' => 'enable_auto_sync' )
			);

			add_settings_field(
				'sync_list',
				'Sync Idx with this list',
				array( $this, 'markup_fields' ),
				'wpps_settings',
				'wpps_section-basic',
				array( 'label_for' => 'sync_list' )
			);

			/*
			 * Cron Section
			 */
			add_settings_section(
				'wpps_section-cron',
				'Update Settings',
				__CLASS__ . '::markup_section_headers',
				'wpps_settings'
			);

			add_settings_field(
				'enable_cron_idx_mailchimp',
				'Enable Update',
				array( $this, 'markup_fields' ),
				'wpps_settings',
				'wpps_section-cron',
				array( 'label_for' => 'enable_cron_idx_mailchimp' )
			);

			add_settings_field(
				'cron_time',
				'Time',
				array( $this, 'markup_fields' ),
				'wpps_settings',
				'wpps_section-cron',
				array( 'label_for' => 'cron_time' )
			);


			// The settings container
			register_setting(
				'wpps_settings',
				'wpps_settings',
				array( $this, 'validate_settings_setting_mailchimp' )
			);
		}	

		/**
		 * Validates submitted setting values before they get saved to the database. Invalid data will be overwritten with defaults.
		 *
		 * @mvc Model
		 *
		 * @param array $new_settings
		 * @return array
		 */
		public function validate_settings_setting_mailchimp( $input ) {
			
			$new_settings = shortcode_atts( $this->settings, $input );			

			if ( ! is_string( $new_settings['db-version'] ) ) {
				$new_settings['db-version'] = Idx_Mailchimp::VERSION;
			}

			$data = array(
			    'enable_auto_sync' => $input['basic']['enable_auto_sync'],
			    'sync_list' => $input['basic']['sync_list'],
			    'enable_cron_idx_mailchimp' => $input['cron']['enable_cron_idx_mailchimp'],
			    'cron_time' => $input['cron']['cron_time'],			    
			);

			$validated = GUMP::is_valid($data, array(
			    'enable_auto_sync' => 'contains,y n',
			    'enable_cron_idx_mailchimp' => 'contains,y n',
			));

			if($validated === true) {
			    $new_settings['basic']['enable_auto_sync'] = $input['basic']['enable_auto_sync'];	
				$new_settings['basic']['sync_list'] = $input['basic']['sync_list'];	

				//Cron Options
				$new_settings['cron']['enable_cron_idx_mailchimp'] = $input['cron']['enable_cron_idx_mailchimp'];			
				
				if (array_key_exists($input['cron']['cron_time'],$this->cron_time_available)) {
					$new_settings['cron']['cron_time'] = $input['cron']['cron_time'];
				}
				add_notice("Saved");
			} else {				
				foreach ($validated as $vl) {
					add_notice($vl );
				}								    
			}				
				
			return $new_settings;
		}

		/**
		 * Adds the section introduction text to the Settings page
		 *
		 * @mvc Controller
		 *
		 * @param array $section
		 */
		public static function markup_section_headers( $section ) {		
			echo self::render_template( 'idxaddons-mailchimp-setting/page-settings-section-headers.php', array( 'section' => $section ), 'always' );
		}

		/**
		 * Delivers the markup for settings fields
		 *
		 * @mvc Controller
		 *
		 * @param array $field
		 */
		public function markup_fields( $field ) {
			$lists = null;
			switch ( $field['label_for'] ) {
				case 'enable_auto_sync':
					// Do any extra processing here
					break;
				case 'sync_list':					
					$data = array(
							'apikey' => $this->apikey_idx,
							'getlist' => 'true',
					);					
					$result_sever_lists = self::idxaddons_connect_bridge_txt_msg("http://beta.idxaddons.com/rc-bridge-api/v1/",'idx-mailchimp', $data);
					$lists = json_decode($result_sever_lists);
						
					break;
			}			
			echo self::render_template( 'idxaddons-mailchimp-setting/page-settings-fields.php', array( 'settings' => $this->settings, 'field' => $field, 'lists' => $lists, 'cron_time_available' => $this->cron_time_available ), 'always' );
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
		}

		/**
		 * Initializes variables
		 *
		 * @mvc Controller
		 */
		public function init() {
		
			
			self::$default_settings = self::get_default_settings();
			$this->settings         = self::get_settings();

			$this->apikey_idx = get_option('idx_broker_apikey');
			//config api mailchimp
			
			
		}

		/**
		 * Executes the logic of upgrading from specific older versions of the plugin to the current version
		 *
		 * @mvc Model
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
		 *
		 * @return bool
		 */
		protected function is_valid( $property = 'all' ) {
			return true;
		}
	} // end IdxAddons_Mailchimp_Setting
}
