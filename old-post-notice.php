<?php
/**
 * Plugin Name: Old Post Notice
 * Plugin URI: https://wordpress.org/plugins/old-post-notice/
 * Description: Automatically display a customizable notice on posts older than a set number of days.
 * Author: David Allsop
 * Author URI: https://davidallsop.com
 * Version: 2.2.2
 * Requires PHP: 7.4
 * Requires at least: 5.5
 * Domain Path: /i18n/languages/
 * Text Domain: old-post-notice
 * License: GNU General Public License v3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package OldPostNotice
 */

declare( strict_types = 1 );

namespace OPN\OldPostNotice;

defined( 'ABSPATH' ) or exit;

require_once __DIR__ . '/vendor_prefixed/autoload.php';
require_once __DIR__ . '/vendor/autoload.php';

use OPN\OldPostNotice\Enqueues;
use OPN\OldPostNotice\Notice;
use OPN\OldPostNotice\Posts;
use OPN\OldPostNotice\Settings;
use OPN\OldPostNotice\Lifecycle\Activator;
use OPN\OldPostNotice\Lifecycle\Deactivator;
use OPN\OldPostNotice\Lifecycle\Installer;
use OPN\OldPostNotice\Lifecycle\Updater;

if ( ! class_exists( 'Old_Post_Notice' ) ) {

	/**
	 * Main class for the plugin.
	 *
	 * @since 2.0.0
	 */
	class Old_Post_Notice {

		/**
		 * Instance of the plugin.
		 *
		 * @var Old_Post_Notice|null
		 * @since 2.0.0
		 */
		private static $instance = null;

		/**
		 * Plugin version.
		 *
		 * @var string
		 * @since 2.0.0
		 */
		public $version = '2.2.2';

		/**
		 * Main instance of the plugin.
		 *
		 * Ensures only one instance of the plugin is loaded or can be loaded.
		 *
		 * @return Old_Post_Notice Main instance.
		 * @since 2.0.0
		 */
		public static function instance(): Old_Post_Notice {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * Sets up the plugin and initializes hooks.
		 *
		 * @since 2.0.0
		 */
		private function __construct() {
			$this->define_constants();
			$this->init_hooks();
		}

		/**
		 * Define plugin constants.
		 *
		 * @return void
		 * @since 2.0.0
		 */
		private function define_constants(): void {
			define( 'OLD_POST_NOTICE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			define( 'OLD_POST_NOTICE_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
			define( 'OLD_POST_NOTICE_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
			define( 'OLD_POST_NOTICE_PLUGIN_VERSION', $this->version );
		}

		/**
		 * Initialize hooks for activation, deactivation, installation, updates, etc.
		 *
		 * @return void
		 * @since 2.0.0
		 */
		private function init_hooks(): void {
			register_activation_hook( __FILE__, array( Activator::class, 'activate' ) );
			register_deactivation_hook( __FILE__, array( Deactivator::class, 'deactivate' ) );
			add_action( 'init', array( $this, 'install_or_update' ) );
		}

		/**
		 * Install or update the plugin.
		 *
		 * This method checks if the plugin is being installed for the first time or if it is being updated.
		 * It calls the appropriate method from the Installer or Updater class.
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function install_or_update(): void {
			if ( ! get_option( 'old_post_notice_version' ) ) {
				Installer::install();
			} elseif ( get_option( 'old_post_notice_version' ) !== $this->version ) {
				Updater::update();
			}
		}

		/**
		 * Run the plugin.
		 *
		 * This method is called to run the plugin and initialize any necessary components.
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function run(): void {
			new Enqueues();
			new Notice();
			new Posts();
			new Settings();
		}
	}

}

if ( ! function_exists( 'old_post_notice' ) ) {
	/**
	 * Returns the main instance of the plugin.
	 *
	 * @return Old_Post_Notice Main instance of the plugin.
	 * @since 2.0.0
	 */
	function old_post_notice(): Old_Post_Notice {
		return Old_Post_Notice::instance();
	}
	old_post_notice()->run();
}
