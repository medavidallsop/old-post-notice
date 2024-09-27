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

					$default_settings = array(
						// 'enable' not included as the default is disabled and if saving the settings via the page 'enable' is not included in the array
						'notice'			=> esc_html__( 'This post is old, the information may be outdated.', 'old-post-notice' ),
						'days'				=> '365',
						'date'				=> 'published',
						'position'			=> 'before',
						'styling'			=> 'default',
						'color_background'	=> '#0000ff',
						'color_text'			=> '#ffffff',
					);

					update_option( 'old_post_notice_settings', $default_settings );

				}

				update_option( 'old_post_notice_version', OLD_POST_NOTICE_VERSION );

			}

		}

	}

}
