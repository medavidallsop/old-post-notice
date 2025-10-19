<?php

declare( strict_types = 1 );

namespace OPN\OldPostNotice;

defined( 'ABSPATH' ) or exit;

/**
 * Posts class.
 *
 * @since 2.0.0
 */
class Posts {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_old_post_notice_old_posts', array( $this, 'get_old_posts_ajax' ) );
		add_action( 'admin_menu', array( $this, 'add_dashboard_page' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		add_action( 'save_post', array( $this, 'save_metabox' ) );
	}

	/**
	 * Get the old posts.
	 *
	 * @param int $limit The limit of posts to get.
	 * @return array The old posts.
	 * @since 2.0.0
	 */
	public function get_old_posts( int $limit = PHP_INT_MAX ): array {

		global $wpdb;

		$settings  = Settings::get_settings();
		$days      = $settings['days'];
		$date      = $settings['date'];
		$old_posts = array();

		if ( ! empty( $days ) && ! empty( $date ) ) {

			$posts        = array();
			$date_compare = gmdate( 'Y-m-d H:i:s', strtotime( '-' . $days . ' days' ) );

			if ( 'published' === $date ) {

				$posts = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT `ID` FROM `{$wpdb->prefix}posts` WHERE`post_status` = 'publish' AND `post_type` = 'post' AND `post_date` < %s ORDER BY `post_date_gmt` ASC LIMIT %d;",
						$date_compare,
						$limit,
					)
				);

			} elseif ( 'modified' === $date ) {

				$posts = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT `ID` FROM `{$wpdb->prefix}posts` WHERE`post_status` = 'publish' AND `post_type` = 'post' AND `post_modified` < %s ORDER BY `post_modified_gmt` ASC LIMIT %d;",
						$date_compare,
						$limit,
					)
				);

			}

			if ( ! empty( $posts ) ) {

				foreach ( $posts as $post ) {

					$old_posts[] = array(
						'id'        => $post->ID,
						'title'     => wp_kses_post( get_the_title( $post->ID ) ),
						'url'       => esc_url( get_permalink( $post->ID ) ),
						'modified'  => esc_html( get_the_modified_date( '', $post->ID ) ),
						'published' => esc_html( get_the_date( '', $post->ID ) ),
					);

				}
			}
		}

		return $old_posts;
	}

	/**
	 * Get the old posts via AJAX.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function get_old_posts_ajax(): void {

		// Check if required POST data is present.
		if ( ! isset( $_POST['nonce'] ) || ! isset( $_POST['type'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Missing required data.', 'old-post-notice' ) ) );
			return;
		}

		// Verify nonce.
		if ( ! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'old_post_notice_admin' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'old-post-notice' ) ) );
			return;
		}

		try {
			$settings     = Settings::get_settings();
			$type         = sanitize_text_field( wp_unslash( $_POST['type'] ) );
			$widget_limit = (int) $settings['dashboard_widget_posts'];

			if ( 'widget' === $type ) {
				$old_posts = $this->get_old_posts( $widget_limit );
			} else {
				$old_posts = $this->get_old_posts();
			}

			wp_send_json_success( $old_posts );

		} catch ( \Exception $e ) {
			wp_send_json_error( array( 'message' => __( 'An error occurred while fetching old posts.', 'old-post-notice' ) ) );
		}
	}

	/**
	 * Adds the dashboard page if old post notice is enabled, the dashboard page is enabled and if the user has the edit_posts capability
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function add_dashboard_page(): void {

		$settings       = Settings::get_settings();
		$enable         = ( isset( $settings['enable'] ) ? $settings['enable'] : '0' );
		$dashboard_page = ( isset( $settings['dashboard_page'] ) ? $settings['dashboard_page'] : '0' );

		if ( '1' === $enable && '1' === $dashboard_page && current_user_can( 'edit_posts' ) ) {

			add_posts_page(
				esc_html__( 'Old Posts', 'old-post-notice' ),
				esc_html__( 'Old Posts', 'old-post-notice' ),
				'edit_posts',
				'old-post-notice-old-posts',
				array( $this, 'dashboard_page_render' ),
			);

		}
	}

	/**
	 * Render the dashboard page.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function dashboard_page_render(): void {
		?>
		<div id="old-post-notice-old-posts" class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<?php $this->dashboard_table_render( 'page' ); ?>
		</div>
		<?php
	}

	/**
	 * Add the dashboard widget.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function add_dashboard_widget(): void {
		// Adds the dashboard widget if old post notice is enabled, the dashboard widget is enabled and if the user has the edit_posts capability.
		$settings         = Settings::get_settings();
		$enable           = ( isset( $settings['enable'] ) ? $settings['enable'] : '0' );
		$dashboard_widget = ( isset( $settings['dashboard_widget'] ) ? $settings['dashboard_widget'] : '0' );

		if ( '1' === $enable && '1' === $dashboard_widget && current_user_can( 'edit_posts' ) ) {

			wp_add_dashboard_widget(
				'old-post-notice-dashboard-widget',
				esc_html__( 'Old Posts', 'old-post-notice' ),
				array( $this, 'dashboard_widget_render' )
			);

		}
	}

	/**
	 * Render the dashboard widget.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function dashboard_widget_render(): void {
		$this->dashboard_table_render( 'widget' );
	}

	/**
	 * Render an old posts dashboard table.
	 *
	 * @param string $type The type of table to render.
	 * @return void
	 * @since 2.0.0
	 */
	public function dashboard_table_render( string $type = 'page' ): void {

		$settings       = Settings::get_settings();
		$days           = $settings['days'];
		$date           = $settings['date'];
		$dashboard_page = ( isset( $settings['dashboard_page'] ) ? $settings['dashboard_page'] : '0' );

		if ( 'published' === $date ) {

			// translators: %s: days.
			echo '<p>' . sprintf( esc_html__( 'These posts display an old post notice because their published date is more than %s days ago.', 'old-post-notice' ), esc_html( $days ) ) . '</p>';

		} elseif ( 'modified' === $date ) {

			// translators: %s: days.
			echo '<p>' . sprintf( esc_html__( 'These posts display an old post notice because their modified date is more than%s days ago', 'old-post-notice' ), esc_html( $days ) ) . '</p>';

		}

		if ( 'page' === $type ) {

			echo '<p><a href="' . esc_url( get_admin_url() . 'options-general.php?page=old-post-notice' ) . '" class="button button-small">' . esc_html__( 'Settings', 'old-post-notice' ) . '</a></p>';

		}

		// Note that table below should not use the fixed class, this is due to the use of colspan % in both the loading and results state, using fixed causes the layout to break.
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
					<th colspan="100%"><?php esc_html_e( 'Fetching old posts...', 'old-post-notice' ); ?></th>
				</tr>
			</tbody>
		</table>

		<?php

		if ( 'widget' === $type ) {

			if ( '1' === $dashboard_page ) {

				echo '<p><a href="' . esc_url( get_admin_url() . 'edit.php?page=old-post-notice-old-posts' ) . '">' . esc_html__( 'View all old posts', 'old-post-notice' ) . '</a></p>';

			} else {

				echo '<p><a href="' . esc_url( get_admin_url() . 'options-general.php?page=old-post-notice' ) . '">' . esc_html__( 'Enable the dashboard page to view more old posts', 'old-post-notice' ) . '</a></p>';

			}
		}
	}

	/**
	 * Add the metabox.
	 *
	 * @return void
	 * @since 2.1.0
	 */
	public function add_metabox(): void {
		// Only add metabox if old post notice is enabled.
		$settings = Settings::get_settings();
		if ( '1' !== $settings['enable'] || empty( $settings['notice'] ) || empty( $settings['days'] ) ) {
			return;
		}

		// Check if this post is old enough to show the notice using Notice class method.
		$notice = new Notice();
		if ( ! $notice->is_post_old_enough( $settings ) ) {
			return;
		}

		// Add metabox.
		add_meta_box(
			'old_post_notice_metabox',
			__( 'Old Post Notice', 'old-post-notice' ),
			array( $this, 'metabox_render' ),
			'post',
			'normal',
			'default',
		);
	}

	/**
	 * Render the metabox.
	 *
	 * @param WP_Post $post The post object.
	 * @return void
	 * @since 2.1.0
	 */
	public function metabox_render( \WP_Post $post ): void {
		$notes_value      = get_post_meta( $post->ID, '_old_post_notice', true );
		$behavior_value   = get_post_meta( $post->ID, '_old_post_notice_behavior', true );

		wp_nonce_field( 'old_post_notice_save_metabox', 'old_post_notice_metabox_nonce' );

		echo '<textarea id="old-post-notice-metabox-notice" name="old_post_notice" placeholder="' . esc_attr__( 'This is an old post, it will display a default notice to the user. You can replace the default notice or append to it.', 'old-post-notice' ) . '">' . esc_textarea( $notes_value ) . '</textarea>';

		echo '<p><select id="old-post-notice-metabox-behavior" name="old_post_notice_behavior">';
		$options = array(
			'replace' => __( 'Replace default notice', 'old-post-notice' ),
			'append'  => __( 'Append to default notice', 'old-post-notice' ),
		);
		foreach ( $options as $key => $label ) {
			printf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $key ),
				selected( $behavior_value, $key, false ),
				esc_html( $label )
			);
		}
		echo '</select></p>';
	}

	/**
	 * Save the metabox values.
	 *
	 * @param int $post_id The post ID.
	 * @return void
	 * @since 2.1.0
	 */
	public function save_metabox( int $post_id ): void {
		// Verify nonce
		if ( ! isset( $_POST['old_post_notice_metabox_nonce'] ) ||
		     ! wp_verify_nonce( $_POST['old_post_notice_metabox_nonce'], 'old_post_notice_save_metabox' ) ) {
			return;
		}

		// Prevent autosave or quick edit overwrites
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( isset( $_POST['action'] ) && $_POST['action'] === 'inline-save' ) {
			return;
		}

		// Check permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Sanitize and save WYSIWYG content
		if ( isset( $_POST['old_post_notice'] ) ) {
			$new_value = wp_kses_post( $_POST['old_post_notice'] ); // Allows safe HTML
			update_post_meta( $post_id, '_old_post_notice', $new_value );
		}

		// Sanitize and save select field
		if ( isset( $_POST['old_post_notice_behavior'] ) ) {
			$behavior_value = sanitize_text_field( $_POST['old_post_notice_behavior'] );
			if ( in_array( $behavior_value, array( 'replace', 'append' ), true ) ) {
				update_post_meta( $post_id, '_old_post_notice_behavior', $behavior_value );
			}
		}
	}
}

