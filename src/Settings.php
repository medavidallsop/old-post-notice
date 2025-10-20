<?php

declare( strict_types = 1 );

namespace OPN\OldPostNotice;

defined( 'ABSPATH' ) or exit;

/**
 * Settings class.
 *
 * @since 2.0.0
 */
class Settings {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_filter( 'plugin_action_links_' . OLD_POST_NOTICE_PLUGIN_BASENAME, array( $this, 'settings_plugin_action_link' ) );
	}

	/**
	 * Get default settings.
	 *
	 * @return array Default settings array.
	 * @since 2.0.0
	 */
	public static function get_default_settings(): array {
		$settings = array(
			// Checkbox based settings which are not set by default are not included, they don't get included in the settings array.
			'notice'                 => esc_html__( 'This post is old, the information may be outdated.', 'old-post-notice' ),
			'days'                   => '365',
			'date'                   => 'published',
			'position'               => 'before',
			'styling'                => 'default',
			'color_background'       => '#0000ff',
			'color_text'             => '#ffffff',
			'dashboard_widget_posts' => '5',
		);

		return $settings;
	}

	/**
	 * Get settings with defaults applied.
	 *
	 * @return array Settings array with defaults applied.
	 * @since 2.0.0
	 */
	public static function get_settings(): array {
		$saved_settings = get_option( 'old_post_notice_settings', array() );
		$defaults       = self::get_default_settings();

		return array(
			'enable'                 => isset( $saved_settings['enable'] ) ? $saved_settings['enable'] : '0', // Checkbox so 0 if not set, not in defaults.
			'notice'                 => isset( $saved_settings['notice'] ) ? $saved_settings['notice'] : $defaults['notice'],
			'days'                   => isset( $saved_settings['days'] ) ? $saved_settings['days'] : $defaults['days'],
			'date'                   => isset( $saved_settings['date'] ) ? $saved_settings['date'] : $defaults['date'],
			'position'               => isset( $saved_settings['position'] ) ? $saved_settings['position'] : $defaults['position'],
			'styling'                => isset( $saved_settings['styling'] ) ? $saved_settings['styling'] : $defaults['styling'],
			'color_background'       => isset( $saved_settings['color_background'] ) ? $saved_settings['color_background'] : $defaults['color_background'],
			'color_text'             => isset( $saved_settings['color_text'] ) ? $saved_settings['color_text'] : $defaults['color_text'],
			'dashboard_page'         => isset( $saved_settings['dashboard_page'] ) ? $saved_settings['dashboard_page'] : '0', // Checkbox so 0 if not set, not in defaults.
			'dashboard_widget'       => isset( $saved_settings['dashboard_widget'] ) ? $saved_settings['dashboard_widget'] : '0', // Checkbox so 0 if not set, not in defaults.
			'dashboard_widget_posts' => isset( $saved_settings['dashboard_widget_posts'] ) ? $saved_settings['dashboard_widget_posts'] : $defaults['dashboard_widget_posts'],
		);
	}

	/**
	 * Register the settings.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function register_settings(): void {

		// Register the old_post_notice_settings option.
		register_setting(
			'old-post-notice',
			'old_post_notice_settings',
			array(
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'type'              => 'array',
			)
		);

		// Register section.
		add_settings_section(
			'old-post-notice-section',
			'',
			'',
			'old-post-notice'
		);

		// Define fields.
		$fields = array(
			array(
				'id'          => 'enable',
				'label'       => esc_html__( 'Enable', 'old-post-notice' ),
				'description' => esc_html__( 'Toggle the display of the old post notice.', 'old-post-notice' ),
				'type'        => 'checkbox',
			),
			array(
				'id'          => 'notice',
				'label'       => esc_html__( 'Notice', 'old-post-notice' ),
				// translators: %s: date placeholder.
				'description' => sprintf( esc_html__( 'Enter the message to display on old posts. Use %s to insert the post\'s date.', 'old-post-notice' ), '<code>[date]</code>' ),
				'type'        => 'textarea',
			),
			array(
				'id'          => 'days',
				'label'       => esc_html__( 'Days', 'old-post-notice' ),
				'description' => esc_html__( 'Set the number of days after which a post is considered old and the notice will appear.', 'old-post-notice' ),
				'type'        => 'number',
			),
			array(
				'id'          => 'date',
				'label'       => esc_html__( 'Date', 'old-post-notice' ),
				'description' => esc_html__( 'Choose whether to base the notice on the post\'s published or last modified date.', 'old-post-notice' ),
				'type'        => 'select',
				'options'     => array(
					'published' => esc_html__( 'Published date', 'old-post-notice' ),
					'modified'  => esc_html__( 'Modified date', 'old-post-notice' ),
				),
			),
			array(
				'id'          => 'position',
				'label'       => esc_html__( 'Position', 'old-post-notice' ),
				'description' => esc_html__( 'Select where the notice should appear within the post.', 'old-post-notice' ),
				'type'        => 'select',
				'options'     => array(
					'before' => esc_html__( 'Before the content', 'old-post-notice' ),
					'after'  => esc_html__( 'After the content', 'old-post-notice' ),
				),
			),
			array(
				'id'          => 'styling',
				'label'       => esc_html__( 'Styling', 'old-post-notice' ),
				// translators: %s: class name.
				'description' => sprintf( esc_html__( 'Select the styling type. The Default option applies basic styling using the colors defined below. Choose None to style the notice manually via CSS (targeting the %s class).', 'old-post-notice' ), '<code>old-post-notice</code>' ),
				'type'        => 'select',
				'options'     => array(
					'default' => esc_html__( 'Default', 'old-post-notice' ),
					'none'    => esc_html__( 'None', 'old-post-notice' ),
				),
			),
			array(
				'id'          => 'color_background',
				'label'       => esc_html__( 'Background color', 'old-post-notice' ),
				'description' => esc_html__( 'Background color of the notice (applies when using the Default styling option).', 'old-post-notice' ),
				'type'        => 'color',
			),
			array(
				'id'          => 'color_text',
				'label'       => esc_html__( 'Text color', 'old-post-notice' ),
				'description' => esc_html__( 'Text color of the notice (applies when using the Default styling option).', 'old-post-notice' ),
				'type'        => 'color',
			),
			array(
				'id'          => 'dashboard_page',
				'label'       => esc_html__( 'Dashboard page', 'old-post-notice' ),
				'description' => esc_html__( 'Add a page under the Posts menu that lists all posts displaying the old post notice.', 'old-post-notice' ),
				'type'        => 'checkbox',
			),
			array(
				'id'          => 'dashboard_widget',
				'label'       => esc_html__( 'Dashboard widget', 'old-post-notice' ),
				'description' => esc_html__( 'Add a widget to the dashboard homepage that lists all posts displaying the old post notice.', 'old-post-notice' ),
				'type'        => 'checkbox',
			),
			array(
				'id'          => 'dashboard_widget_posts',
				'label'       => esc_html__( 'Dashboard widget posts', 'old-post-notice' ),
				'description' => esc_html__( 'Set the number of posts to display in the dashboard widget.', 'old-post-notice' ),
				'type'        => 'select',
				'options'     => array(
					'5'   => '5',
					'10'  => '10',
					'25'  => '25',
					'50'  => '50',
					'100' => '100',
				),
			),
		);

		// Register fields.
		foreach ( $fields as $field ) {

			add_settings_field(
				$field['id'],
				$field['label'],
				array( $this, 'render_settings_field' ),
				'old-post-notice',
				'old-post-notice-section',
				array(
					'label_for' => $field['id'],
					'field'     => $field,
				)
			);
		}
	}

	/**
	 * Sanitize the settings.
	 *
	 * @param array $settings The settings to sanitize.
	 * @return mixed Sanitized settings.
	 * @since 2.0.0
	 */
	public function sanitize_settings( array $settings ): mixed {
		// Sanitize all settings with sanitize_text_field except the notice field.
		$sanitized_settings = array();

		foreach ( $settings as $key => $value ) {
			if ( 'notice' === $key ) {
				// For the notice field, use wp_kses_post to allow safe HTML while sanitizing.
				$sanitized_settings[ $key ] = wp_kses_post( $value );
			} else {
				// For all other fields, use sanitize_text_field.
				$sanitized_settings[ $key ] = sanitize_text_field( $value );
			}
		}

		return $sanitized_settings;
	}

	/**
	 * Render the settings field.
	 *
	 * @param array $args The arguments for the field.
	 * @return void
	 * @since 2.0.0
	 */
	public function render_settings_field( array $args ): void {

		// Render the field based on the passed field type.
		$field            = $args['field'];
		$settings         = get_option( 'old_post_notice_settings' );
		$show_description = false;

		if ( 'checkbox' === $field['type'] ) {

			?>

			<input type="checkbox" id="<?php echo esc_attr( $field['id'] ); ?>" name="old_post_notice_settings[<?php echo esc_attr( $field['id'] ); ?>]" value="1"<?php echo isset( $settings[ $field['id'] ] ) ? ( checked( $settings[ $field['id'] ], 1, false ) ) : ( '' ); ?>>

			<?php

			$show_description = true;

		} elseif ( 'color' === $field['type'] ) {

			?>

			<input type="text" id="<?php echo esc_attr( $field['id'] ); ?>" class="old-post-notice-color-picker" name="old_post_notice_settings[<?php echo esc_attr( $field['id'] ); ?>]" value="<?php echo isset( $settings[ $field['id'] ] ) ? esc_attr( $settings[ $field['id'] ] ) : ''; ?>">

			<?php

			$show_description = true;

		} elseif ( 'number' === $field['type'] ) {

			// This includes some specific attributes for the days field, if additonal number fields are used in future will need to set attributes in an array and pass here instead, note that the max attribute set below is done as found in testing if using something like 999999999 it causes the date queries to break when getting posts showing the notice.
			?>

			<input type="number" id="<?php echo esc_attr( $field['id'] ); ?>" name="old_post_notice_settings[<?php echo esc_attr( $field['id'] ); ?>]" value="<?php echo isset( $settings[ $field['id'] ] ) ? esc_attr( $settings[ $field['id'] ] ) : ''; ?>" min="1" step="1" max="36525">

			<?php

			$show_description = true;

		} elseif ( 'select' === $field['type'] ) {

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

		} elseif ( 'text' === $field['type'] ) {

			?>

			<input type="text" id="<?php echo esc_attr( $field['id'] ); ?>" name="old_post_notice_settings[<?php echo esc_attr( $field['id'] ); ?>]" value="<?php echo isset( $settings[ $field['id'] ] ) ? esc_html( $settings[ $field['id'] ] ) : ''; ?>">

			<?php

			$show_description = true;

		} elseif ( 'textarea' === $field['type'] ) {

			?>

			<textarea id="<?php echo esc_attr( $field['id'] ); ?>" name="old_post_notice_settings[<?php echo esc_attr( $field['id'] ); ?>]"><?php echo isset( $settings[ $field['id'] ] ) ? esc_textarea( $settings[ $field['id'] ] ) : ''; ?></textarea>

			<?php

			$show_description = true;

		}

		if ( true === $show_description ) {

			?>

			<p class="description"><?php echo wp_kses_post( $field['description'] ); ?></p>

			<?php

		}
	}

	/**
	 * Add the settings page.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function add_settings_page(): void {
		add_options_page(
			esc_html__( 'Old Post Notice Settings', 'old-post-notice' ),
			esc_html__( 'Old Post Notice', 'old-post-notice' ),
			'manage_options',
			'old-post-notice',
			array( $this, 'render_settings_page' ),
		);
	}

	/**
	 * Render the settings page.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function render_settings_page(): void {
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

	/**
	 * Add plugin action links.
	 *
	 * @param array $links The plugin action links.
	 * @return array Plugin action links.
	 * @since 2.0.0
	 */
	public function settings_plugin_action_link( array $links ): array {
		array_unshift( $links, '<a href="' . esc_url( get_admin_url() . 'options-general.php?page=old-post-notice' ) . '">' . esc_html__( 'Settings', 'old-post-notice' ) . '</a>' );
		return $links;
	}
}
