<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'Old_Post_Notice_Nag' ) ) {

	class Old_Post_Notice_Nag {

		public function __construct() {

			add_action( 'admin_notices', array( $this, 'display' ) );

		}

		public function display() {

			// Shows nag on specific pages

			global $pagenow;

			$display_nag = false;

			$settings = get_option( 'old_post_notice_settings' );

			if ( isset( $settings['nag'] ) ) {

				if ( '1' == $settings['nag'] ) {

					if ( 'edit.php' == $pagenow ) {

						if ( isset( $_GET['page'] ) ) {

							if ( 'old-post-notice-old-posts' == sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) {

								$display_nag = true; // Display the nag if enabled and it is the old posts page

							}

						}

					} elseif ( 'options-general.php' == $pagenow ) {

						if ( isset( $_GET['page'] ) ) {

							if ( 'old-post-notice' == sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) {

								$display_nag = true; // Display the nag if enabled and it is the settings page

							}

						}

					}

				}

			}

			if ( true == $display_nag ) {

				// translators: %1$s: review link, %2$s: settings link
				echo '<div class="notice notice-success"><p>' . sprintf( esc_html__( 'Hello! I\'m David. I develop this plugin in my spare time. If it has helped you, please consider %1$s. You can disable this nag in %2$s.', 'old-post-notice' ), '<a href="https://wordpress.org/support/plugin/old-post-notice/reviews/" target="_blank">' . esc_html__( 'leaving a review', 'old-post-notice' ) . '</a>', '<a href="' . esc_url( get_admin_url() . 'options-general.php?page=old-post-notice' ) . '">' . esc_html__( 'settings', 'old-post-notice' ) . '</a>' ) . '</p></div>';

			}

		}

	}

}
