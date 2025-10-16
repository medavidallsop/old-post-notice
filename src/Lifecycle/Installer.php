<?php

declare( strict_types = 1 );

namespace OPN\OldPostNotice\Lifecycle;

use OPN\OldPostNotice\Settings;

defined( 'ABSPATH' ) or exit;

/**
 * Installer class.
 *
 * Handles the installation tasks for the plugin.
 *
 * @since 2.0.0
 */
class Installer {

	/**
	 * Perform plugin installation tasks.
	 *
	 * This method is called when the plugin is installed.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public static function install(): void {
		// Add default settings.
		add_option( 'old_post_notice_settings', Settings::get_default_settings() );

		// Add the version number so in future the installer doesn't run again and the updater knows the current version to compare to.
		add_option( 'old_post_notice_version', OLD_POST_NOTICE_PLUGIN_VERSION );
	}
}
