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

			// Enqueue assets if it is the settings page

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

						wp_enqueue_style(
							'old-post-notice-admin',
							plugins_url( 'assets/css/admin.css', __DIR__ ),
							array(),
							OLD_POST_NOTICE_VERSION,
							'all'
						);

					}

				}

			}

		}

		public function assets_public() {

			// Enqueue assets on frontend if is a post and the styling setting is not none

			if ( is_single() ) {

				$settings = get_option( 'old_post_notice_settings' );

				if ( 'none' !== $settings['styling'] ) {

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
