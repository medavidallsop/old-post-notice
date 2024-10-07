<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'Old_Post_Notice_Old_Posts' ) ) {

	class Old_Post_Notice_Old_Posts {

		public function __construct() {

			add_action( 'admin_menu', array( $this, 'page' ) );
			add_action( 'wp_ajax_old_post_notice_old_posts', array( $this, 'ajax' ) );

		}

		public function page() {

			// Add the posts page

			$settings = get_option( 'old_post_notice_settings' );
			$enable = ( isset( $settings['enable'] ) ? $settings['enable'] : '0' );
			$dashboard_page = ( isset( $settings['dashboard_page'] ) ? $settings['dashboard_page'] : '0' );

			if ( '1' == $enable && '1' == $dashboard_page && current_user_can( 'edit_posts' ) ) {

				add_posts_page(
					esc_html__( 'Old Posts', 'old-post-notice' ),
					esc_html__( 'Old Posts', 'old-post-notice' ),
					'manage_options',
					'old-post-notice-old-posts',
					array( $this, 'page_render' ),
				);

			}

		}

		public function page_render() {

			// Renders the old posts page

			?>

			<div id="old-post-notice-old-posts" class="wrap">
				<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
				<?php self::table( 'page' ); ?>
			</div>

			<?php

		}

		public static function table( $type = 'page' ) {

			// Renders the initial table ready for the old posts to be added to via AJAX

			$settings = get_option( 'old_post_notice_settings' );
			$days = $settings['days'];
			$date = $settings['date'];
			$dashboard_page = ( isset( $settings['dashboard_page'] ) ? $settings['dashboard_page'] : '0' );

			if ( 'published' == $date ) {

				// translators: %s: days
				echo '<p>' . sprintf( esc_html__( 'An old post notice is being displayed on these posts as the published date is more than %s days ago.', 'old-post-notice' ), esc_html( $days ) ) . '</p>';

			} elseif ( 'modified' == $date ) {

				// translators: %s: days
				echo '<p>' . sprintf( esc_html__( 'An old post notice is being displayed on these posts as the modified date is more than %s days ago', 'old-post-notice' ), esc_html( $days ) ) . '</p>';

			}

			if ( 'page' == $type ) {

				echo '<p><a href="' . esc_url( get_admin_url() . 'options-general.php?page=old-post-notice' ) . '" class="button button-small">' . esc_html__( 'Configure settings' ) . '</a></p>';

			}

			// Note that table below should not use the fixed class, this is due to the use of colspan % in both the loading and results state, using fixed causes the layout to break

			?>

			<table id="old-post-notice-old-posts-table" class="striped widefat" data-type="<?php echo esc_attr( $type ); ?>">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Post', 'old-post-notice' ); ?></th>
						<th><?php esc_html_e( 'Published', 'old-post-notice' ); ?></th>
						<th><?php esc_html_e( 'Modified', 'old-post-notice' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th colspan="100%"><?php esc_html_e( 'Loading...', 'old-post-notice' ); ?></th>
					</tr>
				</tbody>
			</table>

			<?php

			if ( 'widget' == $type ) {

				if ( '1' == $dashboard_page ) {

					echo '<p><a href="' . esc_url( get_admin_url() . 'edit.php?page=old-post-notice-old-posts' ) . '">' . esc_html__( 'View all old posts' ) . '</a></p>';

				} else {

					echo '<p><a href="' . esc_url( get_admin_url() . 'options-general.php?page=old-post-notice' ) . '">' . esc_html__( 'Enable the dashboard page to view more old posts' ) . '</a></p>';

				}

			}

		}

		public function ajax() {

			// Returns an array of old posts to be inserted into the old posts table

			if ( isset( $_POST['nonce'] ) ) {

				if ( wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'old_post_notice_admin' ) ) {

					global $wpdb;

					$settings = get_option( 'old_post_notice_settings' );
					$days = $settings['days'];
					$date = $settings['date'];
					$old_posts = array();

					if (  !empty( $days ) && !empty( $date ) ) {

						$posts = array();
						$date_compare = gmdate( 'Y-m-d H:i:s', strtotime( '-' . $days . ' days' ) );
						$type = $_POST['type'];
						$widget_limit = 20;

						if ( 'published' == $date ) {

							$posts = $wpdb->get_results(
								$wpdb->prepare(
									"SELECT `ID` FROM `{$wpdb->prefix}posts` WHERE`post_status` = 'publish' AND `post_type` = 'post' AND `post_date` < %s ORDER BY `post_date_gmt` ASC LIMIT %d;",
									$date_compare,
									( 'widget' == $type ? $widget_limit : PHP_INT_MAX ),
								)
							);

						} elseif ( 'modified' == $date ) {

							$posts = $wpdb->get_results(
								$wpdb->prepare(
									"SELECT `ID` FROM `{$wpdb->prefix}posts` WHERE`post_status` = 'publish' AND `post_type` = 'post' AND `post_modified` < %s ORDER BY `post_modified_gmt` ASC LIMIT %d;",
									$date_compare,
									( 'widget' == $type ? $widget_limit : PHP_INT_MAX ),
								)
							);

						}

						if ( !empty( $posts ) ) {

							foreach ( $posts as $post ) {

								$old_posts[] = array(
									'id'		=> $post->ID,
									'title'		=> wp_kses_post( get_the_title( $post->ID ) ),
									'url'		=> esc_url( get_permalink( $post->ID ) ),
									'modified'	=> esc_html( get_the_modified_date( '', $post->ID ) ),
									'published'	=> esc_html( get_the_date( '', $post->ID ) ),
								);

							}

						}

					}

					echo wp_json_encode( $old_posts );

				}

			}

			wp_die();

		}

	}

}
