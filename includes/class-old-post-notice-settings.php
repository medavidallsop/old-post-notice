<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'Old_Post_Notice_Settings' ) ) {

	class Old_Post_Notice_Settings {

		public function __construct() {

			add_action( 'admin_menu', array( $this, 'page' ) );
			add_action( 'admin_notices', array( $this, 'notices' ) );
			add_action( 'admin_init', array( $this, 'register' ) );
			add_filter( 'plugin_action_links_' . OLD_POST_NOTICE_BASENAME, array( $this, 'plugins_link' ) );

		}

		public function page() {

			// Add the settings page

			add_options_page(
				esc_html__( 'Old Post Notice Settings', 'old-post-notice' ),
				esc_html__( 'Old Post Notice', 'old-post-notice' ),
				'manage_options',
				'old-post-notice',
				array( $this, 'page_render' ),
			);

		}

		public function notices() {

			// Shows notices on the settings page

			global $pagenow;

			if ( 'options-general.php' == $pagenow ) {

				if ( isset( $_GET['page'] ) ) {

					if ( 'old-post-notice' == sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) {

						$settings = get_option( 'old_post_notice_settings' );

						if ( isset( $settings['nag'] ) ) {

							if ( '1' == $settings['nag'] ) {

								// translators: %1$s: sponsor link, %2$s: review link
								echo '<div class="notice notice-success"><p>' . sprintf( esc_html__( 'Hello! I\'m David, I develop this plugin in my spare time. If it has helped you, please consider %1$s (one-time or monthly) and/or %2$s. This helps me commit more time to development and keeps it free. You can disable this nag below.', 'old-post-notice' ), '<a href="https://github.com/sponsors/medavidallsop" target="_blank">' . esc_html__( 'sponsoring me on GitHub', 'old-post-notice' ) . '</a>', '<a href="https://wordpress.org/support/plugin/old-post-notice/reviews/" target="_blank">' . esc_html__( 'leaving a review', 'old-post-notice' ) . '</a>' ) . '</p></div>';

							}

						}

					}

				}

			}

		}

		public function register() {

			// Registers the old_post_notice_settings option, all fields get saved as a serialized array to this

			register_setting( 'old-post-notice', 'old_post_notice_settings' );

			// Register a new section

			add_settings_section(
				'old-post-notice-section',
				'',
				'',
				'old-post-notice'
			);

			// Define fields

			$fields = array(
				array(
					'id'			=> 'enable',
					'label'			=> esc_html__( 'Enable', 'old-post-notice' ),
					'description'	=> esc_html__( 'Enable or disable the old post notice.', 'old-post-notice' ),
					'type'			=> 'checkbox',
				),
				array(
					'id'			=> 'notice',
					'label'			=> esc_html__( 'Notice', 'old-post-notice' ),
					// translators: %s: date placeholder
					'description'	=> sprintf( esc_html__( 'Enter the notice you want to be displayed on old posts. Use %s to include the post date.', 'old-post-notice' ), '<code>[date]</code>' ),
					'type'			=> 'textarea',
				),
				array(
					'id'			=> 'days',
					'label'			=> esc_html__( 'Days', 'old-post-notice' ),
					'description'	=> esc_html__( 'How many days old a post needs to be for the notice to be displayed.', 'old-post-notice' ),
					'type'			=> 'number',
				),
				array(
					'id'			=> 'date',
					'label'			=> esc_html__( 'Date', 'old-post-notice' ),
					'description'	=> esc_html__( 'Choose if the notice should be displayed based on the published or modified date of the post.', 'old-post-notice' ),
					'type'			=> 'select',
					'options'		=> array(
						'published'	=> esc_html__( 'Published date', 'old-post-notice' ),
						'modified'	=> esc_html__( 'Modified date', 'old-post-notice' ),
					),
				),
				array(
					'id'			=> 'position',
					'label'			=> esc_html__( 'Position', 'old-post-notice' ),
					'description'	=> esc_html__( 'Choose where the notice will appear in the post.', 'old-post-notice' ),
					'type'			=> 'select',
					'options'		=> array(
						'before'	=> esc_html__( 'Before the content', 'old-post-notice' ),
						'after'		=> esc_html__( 'After the content', 'old-post-notice' ),
					),
				),
				array(
					'id'			=> 'styling',
					'label'			=> esc_html__( 'Styling', 'old-post-notice' ),
					// translators: %s: class name
					'description'	=> sprintf( esc_html__( 'Choose the styling type. The default option adds some basic styling and uses the background/text colors set below. Use the none option if you wish to style the notice with CSS by targeting the %s class.', 'old-post-notice' ), '<code>old-post-notice</code>' ),
					'type'			=> 'select',
					'options'		=> array(
						'default'	=> esc_html__( 'Default', 'old-post-notice' ),
						'none'		=> esc_html__( 'None', 'old-post-notice' ),
					),
				),
				array(
					'id'			=> 'color_background',
					'label'			=> esc_html__( 'Background color', 'old-post-notice' ),
					'description'	=> esc_html__( 'Background color of the notice, used when the default styling option above is used.', 'old-post-notice' ),
					'type'			=> 'color',
				),
				array(
					'id'			=> 'color_text',
					'label'			=> esc_html__( 'Text color', 'old-post-notice' ),
					'description'	=> esc_html__( 'Text color of the notice, used when the default styling option above is used.', 'old-post-notice' ),
					'type'			=> 'color',
				),
				array(
					'id'			=> 'widget_dashboard',
					'label'			=> esc_html__( 'Dashboard widget', 'old-post-notice' ),
					'description'	=> esc_html__( 'Enable or disable a dashboard widget that displays an overview of all posts that are displaying the old post notice.', 'old-post-notice' ),
					'type'			=> 'checkbox',
				),
				array(
					'id'			=> 'nag',
					'label'			=> esc_html__( 'Nag', 'old-post-notice' ),
					'description'	=> esc_html__( 'Enable or disable the sponsor/review nag notice.', 'old-post-notice' ),
					'type'			=> 'checkbox',
				),
			);

			// Register fields

			foreach ( $fields as $field ) {

				add_settings_field(
					$field['id'],
					$field['label'],
					array( $this, 'field_render' ),
					'old-post-notice',
					'old-post-notice-section',
					array(
						'label_for'	=> $field['id'],
						'field'		=> $field,
					)
				);

			}

		}

		public function plugins_link( $links ) {

			// Add settings link to plugins list, unshift ensures settings link first

			array_unshift( $links, '<a href="' . esc_url( get_admin_url() . 'options-general.php?page=old-post-notice' ) . '">' . esc_html__( 'Settings', 'old-post-notice' ) . '</a>' );
			return $links;

		}

		public function field_render( $args ) {

			// Render the field based on the passed field type

			$field = $args['field'];
			$settings = get_option( 'old_post_notice_settings' );
			$show_description = false;

			if ( 'checkbox' == $field['type'] ) {

				?>

				<input type="checkbox" id="<?php echo esc_attr( $field['id'] ); ?>" name="old_post_notice_settings[<?php echo esc_attr( $field['id'] ); ?>]" value="1"<?php echo isset( $settings[ $field['id'] ] ) ? ( checked( $settings[ $field['id'] ], 1, false ) ) : ( '' ); ?>>

				<?php

				$show_description = true;

			} elseif ( 'color' == $field['type'] ) {

				?>

				<input type="text" id="<?php echo esc_attr( $field['id'] ); ?>" class="old-post-notice-color-picker" name="old_post_notice_settings[<?php echo esc_attr( $field['id'] ); ?>]" value="<?php echo isset( $settings[ $field['id'] ] ) ? esc_attr( $settings[ $field['id'] ] ) : ''; ?>">

				<?php

				$show_description = true;

			} elseif ( 'number' == $field['type'] ) {

				?>

				<input type="number" id="<?php echo esc_attr( $field['id'] ); ?>" name="old_post_notice_settings[<?php echo esc_attr( $field['id'] ); ?>]" value="<?php echo isset( $settings[ $field['id'] ] ) ? esc_attr( $settings[ $field['id'] ] ) : ''; ?>" min="1" step="1">

				<?php

				$show_description = true;

			} elseif ( 'select' == $field['type'] ) {

				?>

				<select id="<?php echo esc_attr( $field['id'] ); ?>" name="old_post_notice_settings[<?php echo esc_attr( $field['id'] ); ?>]">
					<?php
					foreach ( $field['options'] as $key => $option ) {
						?>
						<option value="<?php echo esc_html( $key ); ?>"<?php echo isset( $settings[ $field['id'] ] ) ? ( selected( $settings[ $field['id'] ], $key, false ) ) : ( '' ); ?>><?php echo esc_html( $option ); ?></option>
						<?php
					}
					?>
				</select>

				<?php

				$show_description = true;

			} elseif ( 'text' == $field['type'] ) {

				?>

				<input type="text" id="<?php echo esc_attr( $field['id'] ); ?>" name="old_post_notice_settings[<?php echo esc_attr( $field['id'] ); ?>]" value="<?php echo isset( $settings[ $field['id'] ] ) ? esc_attr( $settings[ $field['id'] ] ) : ''; ?>">

				<?php

				$show_description = true;

			} elseif ( 'textarea' == $field['type'] ) {

				?>

				<textarea id="<?php echo esc_attr( $field['id'] ); ?>" name="old_post_notice_settings[<?php echo esc_attr( $field['id'] ); ?>]"><?php echo isset( $settings[ $field['id'] ] ) ? esc_attr( $settings[ $field['id'] ] ) : ''; ?></textarea>

				<?php

				$show_description = true;

			}

			if ( true == $show_description ) {

				?>

				<p class="description"><?php echo wp_kses_post( $field['description'] ); ?></p>

				<?php

			}

		}

		public function page_render() {

			// Display the settings form

			?>
			<div id="old-post-notice-settings" class="wrap">
				<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
				<form action="options.php" method="post">
					<?php
					settings_fields( 'old-post-notice' );
					do_settings_sections( 'old-post-notice' );
					submit_button();
					?>
				</form>
			</div>
			<?php

		}

	}

}
