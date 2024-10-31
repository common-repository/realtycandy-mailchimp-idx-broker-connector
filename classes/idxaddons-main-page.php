<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! class_exists( 'Idx_Addons_Mailchimp_Sync' ) ) {
	
	/**
	 * Creates a custom page
	 */
	class Idx_Addons_Mailchimp_Sync extends Idx_Addons_Mailchimp_Module{
		protected $settings;
		public static $default_settings;
		protected $mc;
		protected $apikey_idx;		
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
			//add_action( 'admin_init',               array( $this, 'register_settings' ) );
			
		}

		/**
		 * Registers the Pages
		 *
		 * @mvc Controller
		 */
		public static function create_main_page() {		
			    add_menu_page('Mailchimp IDX','Mailchimp IDX',self::REQUIRED_CAPABILITY,'mailchimp-idx',__CLASS__ . '::main_mailchimp_idx',
			        'dashicons-email');			   
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
							$leads = idxaddons_api_idx('leads', 'lead');
							$data = array(
									'apikey' => get_option('idx_broker_apikey'),
									'getlist' => 'true',
							);
							
							$lists = json_decode(self::idxaddons_connect_bridge_txt_msg("http://beta.idxaddons.com/rc-bridge-api/v1/",'idx-mailchimp', $data));
					
							echo self::render_template( 'page-sync-mailchimp.php', array( 'leads' => $leads, 'lists' => $lists), 'always'  );
							break;
					}
				
			} else {
				wp_die( 'Access denied.' );
			}
		}			


		/**
		 *
		 * Connect with Bridge
		 *
		 */
		public static function idxaddons_connect_bridge_txt_msg($api_url, $endpoint, $data ) {
		        $api_url = $api_url."$endpoint"; 
		        $ch = curl_init( $api_url );            
		        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                    
		        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));                                                                  
		        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		        return curl_exec($ch); 
		}
		
		/**
		 * Prepares site to use the plugin during activation
		 *
		 * @mvc Controller
		 *
		 * @param bool $network_wide
		 */
		public function activate( $network_wide ) {}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @mvc Controller
		 */
		public function deactivate() {}

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
		public function upgrade( $db_version = 0 ) {}

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
	} // end IdxAddons_Mailchimp_Sync
}
