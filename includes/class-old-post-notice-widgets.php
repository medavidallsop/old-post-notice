<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'Old_Post_Notice_Widgets' ) ) {

	class Old_Post_Notice_Widgets {

		public function __construct() {

			add_action( 'wp_dashboard_setup', array( $this, 'add' ) );

		}

		public function add() {

			// Adds the dashboard widget if old post notice is enabled, the dashboard widget is enabled and if the user has the edit_posts capability

			$settings = get_option( 'old_post_notice_settings' );
			$enable = ( isset( $settings['enable'] ) ? $settings['enable'] : '0' );
			$dashboard_widget = ( isset( $settings['dashboard_widget'] ) ? $settings['dashboard_widget'] : '0' );

			if ( '1' == $enable && '1' == $dashboard_widget && current_user_can( 'edit_posts' ) ) {

				wp_add_dashboard_widget(
					'old-post-notice-dashboard-widget',
					esc_html__( 'Old Posts', 'old-post-notice' ),
					array( $this, 'dashboard' )
				);

			}

		}

		public function dashboard() {

			Old_Post_Notice_Old_Posts::table( 'widget' );

		}

	}

}
