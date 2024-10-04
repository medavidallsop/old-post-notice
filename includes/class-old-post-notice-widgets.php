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

			$settings = get_option( 'old_post_notice_settings' );
			$enable = ( isset( $settings['enable'] ) ? $settings['enable'] : '0' );
			$widget_dashboard = ( isset( $settings['widget_dashboard'] ) ? $settings['widget_dashboard'] : '0' );

			if ( '1' == $enable && '1' == $widget_dashboard ) {

				wp_add_dashboard_widget(
					'old-post-notice-widget-dashboard',
					__( 'Old Post Notice', 'old-post-notice' ),
					array( $this, 'widget_dashboard' )
				);

			}

		}

		public function widget_dashboard() {

			global $wpdb;

			$settings = get_option( 'old_post_notice_settings' );
			$days = $settings['days'];
			$date = $settings['date'];

			if (  !empty( $days ) && !empty( $date ) ) {

				$posts = array();
				$date_compare = gmdate( 'Y-m-d H:i:s', strtotime( '-' . $days . ' days' ) );

				if ( 'published' == $date ) {

					$posts = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT `ID` FROM `{$wpdb->prefix}posts` WHERE`post_status` = 'publish' AND `post_type` = 'post' AND `post_date` < %s ORDER BY `post_date_gmt` ASC;",
							$date_compare
						)
					);

				} elseif ( 'modified' == $date ) {

					$posts = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT `ID` FROM `{$wpdb->prefix}posts` WHERE`post_status` = 'publish' AND `post_type` = 'post' AND `post_date` < %s ORDER BY `post_modified_gmt` ASC;",
							$date_compare
						)
					);

				}

				if ( !empty( $posts ) ) {

					if ( 'published' == $date ) {

						// translators: %s: days
						echo '<p>' . sprintf( esc_html__( 'An old post notice is being displayed on these posts as the published date is more than %s days ago.', 'old-post-notice' ), esc_html( $days ) ) . '</p>';

					} elseif ( 'modified' == $date ) {

						// translators: %s: days
						echo '<p>' . sprintf( esc_html__( 'An old post notice is being displayed on these posts as the modified date is more than %s days ago', 'old-post-notice' ), esc_html( $days ) ) . '</p>';

					}

					echo '<div class="old-post-notice-widget-dashboard-table-scroller">';
					echo '<table class="fixed striped widefat">';
					echo '<thead>';
					echo '<tr>';
					echo '<th>' . esc_html__( 'Post', 'old-post-notice' ) . '</th>';
					echo '<th>' . esc_html__( 'Published', 'old-post-notice' ) . '</th>';
					echo '<th>' . esc_html__( 'Modified', 'old-post-notice' ) . '</th>';
					echo '</tr>';
					echo '</thead>';
					echo '<tbody>';

					foreach ( $posts as $post ) {

						echo '<tr>';
						echo '<td><a href="' . esc_url( get_permalink( $post->ID ) ) . '" target="_blank">' . wp_kses_post( get_the_title( $post->ID ) ) . '</a></td>';
						echo '<td>' . esc_html( get_the_date( '', $post->ID ) ) . '</td>';
						echo '<td>' . esc_html( get_the_modified_date( '', $post->ID ) ) . '</td>';
						echo '</tr>';

					}

					echo '</tbody>';
					echo '</table>';
					echo '</div>';

				} else {

					echo '<p>' . esc_html__( 'No posts are displaying an old post notice.', 'old-post-notice' ) . '</p>';

				}

				echo '<p><a href="' . esc_url( get_admin_url() . 'options-general.php?page=old-post-notice' ) . '" class="button button-small">' . esc_html__( 'Settings', 'old-post-notice' ) . '</a></p>';

			}

		}

	}

}
