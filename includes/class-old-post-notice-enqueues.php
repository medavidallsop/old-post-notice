<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'Old_Post_Notice_Enqueues' ) ) {

	class Old_Post_Notice_Enqueues {

		public function __construct() {

			add_action( 'admin_enqueue_scripts', array( $this, 'assets_admin' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'assets_public' ) );

		}

		public function assets_admin() {

			global $pagenow;

			// Enqueue global admin assets

			wp_enqueue_style(
				'old-post-notice-admin',
				plugins_url( 'assets/css/admin.css', __DIR__ ),
				array(),
				OLD_POST_NOTICE_VERSION,
				'all'
			);

			// Enqueue settings admin assets

			if ( 'options-general.php' == $pagenow ) {

				if ( isset( $_GET['page'] ) ) {

					if ( 'old-post-notice' == $_GET['page'] ) {

						wp_enqueue_script( 'jquery' );

						wp_enqueue_script(
							'old-post-notice-admin',
							plugins_url( 'assets/js/admin.min.js', __DIR__ ),
							array(
								'jquery',
								'wp-color-picker',
							),
							OLD_POST_NOTICE_VERSION,
							true
						);

						wp_enqueue_style( 'wp-color-picker' );

					}

				}

			}

		}

		public function assets_public() {

			// Enqueue public assets if it is a post, old post notice is enabled, and the styling setting is not none

			if ( is_single() ) {

				$settings = get_option( 'old_post_notice_settings' );
				$enable = ( isset( $settings['enable'] ) ? $settings['enable'] : '0' );
				$styling = $settings['styling'];

				if ( '1' == $enable && 'none' !== $styling ) {

					wp_enqueue_style(
						'old-post-notice-public',
						plugins_url( 'assets/css/public.css', __DIR__ ),
						array(),
						OLD_POST_NOTICE_VERSION,
						'all'
					);

				}

			}

		}

	}

}
