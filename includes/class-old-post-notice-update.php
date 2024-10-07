<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'Old_Post_Notice_Update' ) ) {

	class Old_Post_Notice_Update {

		public function __construct() {

			add_action( 'wp_loaded', array( $this, 'do' ) );

		}

		public function do() {

			$version = get_option( 'old_post_notice_version' );

			if ( OLD_POST_NOTICE_VERSION !== $version ) {

				if ( version_compare( $version, '1.0.0', '<' ) ) {

					$settings = array(
						// 'enable' not included as the default is disabled and if saving the settings via the page 'enable' is not set and therefore not included in the array
						'notice'			=> esc_html__( 'This post is old, the information may be outdated.', 'old-post-notice' ),
						'days'				=> '365',
						'date'				=> 'published',
						'position'			=> 'before',
						'styling'			=> 'default',
						'color_background'	=> '#0000ff',
						'color_text'		=> '#ffffff',
						'nag'				=> '1',
					);

					update_option( 'old_post_notice_settings', $settings );

				}

				if ( version_compare( $version, '1.2.0', '<' ) ) {

					$settings = get_option( 'old_post_notice_settings' );

					if ( isset( $settings['widget_dashboard'] ) ) {

						// This version introduced a dashboard_page setting, so for naming consistency the old widget_dashboard setting is renamed to dashboard_widget

						$settings['dashboard_widget'] = $settings['widget_dashboard'];
						unset( $settings['widget_dashboard'] );
						update_option( 'old_post_notice_settings', $settings );

					}

				}

				update_option( 'old_post_notice_version', OLD_POST_NOTICE_VERSION );

			}

		}

	}

}
