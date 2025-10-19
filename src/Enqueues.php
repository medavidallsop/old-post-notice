<?php

declare( strict_types = 1 );

namespace OPN\OldPostNotice;

defined( 'ABSPATH' ) or exit;

/**
 * Enqueues class.
 *
 * @since 2.0.0
 */
class Enqueues {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_assets' ) );
	}

	/**
	 * Enqueue the admin assets.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function enqueue_admin_assets(): void {
		global $pagenow;

		wp_enqueue_script( 'jquery' );

		wp_enqueue_script(
			'old-post-notice-admin',
			OLD_POST_NOTICE_PLUGIN_DIR_URL . 'assets/static/admin.min.js',
			array(
				'jquery',
				'wp-color-picker',
				'wp-i18n',
			),
			OLD_POST_NOTICE_PLUGIN_VERSION,
			true
		);

		wp_set_script_translations(
			'old-post-notice-admin',
			'old-post-notice',
			OLD_POST_NOTICE_PLUGIN_DIR_PATH . 'i18n/languages'
		);

		wp_localize_script(
			'old-post-notice-admin',
			'oldPostNotice',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'old_post_notice_admin' ),
			)
		);

		wp_enqueue_style(
			'old-post-notice-admin',
			OLD_POST_NOTICE_PLUGIN_DIR_URL . 'assets/static/admin.min.css',
			array(),
			OLD_POST_NOTICE_PLUGIN_VERSION
		);

		if ( 'options-general.php' === $pagenow ) {

			if ( isset( $_GET['page'] ) ) {

				if ( 'old-post-notice' === $_GET['page'] ) {

					wp_enqueue_style( 'wp-color-picker' );

				}
			}
		}
	}

	/**
	 * Enqueue the public assets.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function enqueue_public_assets(): void {

		if ( is_single() ) {

			$settings = Settings::get_settings();
			$enable   = ( isset( $settings['enable'] ) ? $settings['enable'] : '0' );
			$styling  = $settings['styling'];

			if ( '1' === $enable && 'none' !== $styling ) {

				wp_enqueue_style(
					'old-post-notice-public',
					OLD_POST_NOTICE_PLUGIN_DIR_URL . 'assets/static/public.min.css',
					array(),
					OLD_POST_NOTICE_PLUGIN_VERSION
				);

			}
		}
	}

}
