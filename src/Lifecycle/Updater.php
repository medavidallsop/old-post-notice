<?php

declare( strict_types = 1 );

namespace OPN\OldPostNotice\Lifecycle;

defined( 'ABSPATH' ) or exit;

/**
 * Updater class.
 *
 * Handles the update tasks for the plugin.
 *
 * @since 2.0.0
 */
class Updater {

	/**
	 * Perform plugin update tasks.
	 *
	 * This method is called when the plugin is updated.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public static function update(): void {
		$version  = get_option( 'old_post_notice_version' );
		$settings = get_option( 'old_post_notice_settings' );

		if ( OLD_POST_NOTICE_PLUGIN_VERSION !== $version ) {

			if ( version_compare( $version, '1.2.0', '<' ) ) {
				if ( isset( $settings['widget_dashboard'] ) ) {
					// Introduced a dashboard_page setting, so for naming consistency the old widget_dashboard setting is renamed to dashboard_widget.
					$settings['dashboard_widget'] = $settings['widget_dashboard'];
					unset( $settings['widget_dashboard'] );
					update_option( 'old_post_notice_settings', $settings );
				}
			}

			if ( version_compare( $version, '1.3.0', '<' ) ) {
				// Set the default number of posts to display in the dashboard widget.
				$settings['dashboard_widget_posts'] = '5';
				update_option( 'old_post_notice_settings', $settings );
			}

			if ( version_compare( $version, '2.0.0', '<' ) ) {
				// Remove the nag setting as it is no longer used.
				unset( $settings['nag'] );
				update_option( 'old_post_notice_settings', $settings );
			}
		}

		update_option( 'old_post_notice_version', OLD_POST_NOTICE_PLUGIN_VERSION );
	}
}
