<?php

/**
 * Plugin name: Old Post Notice
 * Plugin URI: https://wordpress.org/plugins/old-post-notice/
 * Description: Display a notice on old posts.
 * Author: David Allsop
 * Author URI: https://davidallsop.com
 * Version: 1.3.2
 * Requires at least: 5.0.0
 * Requires PHP: 7.0.0
 * Domain path: /languages
 * Text domain: old-post-notice
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'Old_Post_Notice' ) ) {

	define( 'OLD_POST_NOTICE_BASENAME', plugin_basename( __FILE__ ) );
	define( 'OLD_POST_NOTICE_VERSION', '1.3.2' );

	class Old_Post_Notice {

		public function __construct() {

			require_once __DIR__ . '/includes/class-old-post-notice-display.php';
			require_once __DIR__ . '/includes/class-old-post-notice-enqueues.php';
			require_once __DIR__ . '/includes/class-old-post-notice-nag.php';
			require_once __DIR__ . '/includes/class-old-post-notice-old-posts.php';
			require_once __DIR__ . '/includes/class-old-post-notice-settings.php';
			require_once __DIR__ . '/includes/class-old-post-notice-translation.php';
			require_once __DIR__ . '/includes/class-old-post-notice-update.php';
			require_once __DIR__ . '/includes/class-old-post-notice-widgets.php';

			new Old_Post_Notice_Display();
			new Old_Post_Notice_Enqueues();
			new Old_Post_Notice_Nag();
			new Old_Post_Notice_Old_Posts();
			new Old_Post_Notice_Settings();
			new Old_Post_Notice_Translation();
			new Old_Post_Notice_Update();
			new Old_Post_Notice_Widgets();

		}

	}

	new Old_Post_Notice();

}
