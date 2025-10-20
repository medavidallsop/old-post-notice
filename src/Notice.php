<?php

declare( strict_types = 1 );

namespace OPN\OldPostNotice;

defined( 'ABSPATH' ) or exit;

/**
 * Notice class.
 *
 * @since 2.0.0
 */
class Notice {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_filter( 'the_content', array( $this, 'render_notice' ) );
	}

	/**
	 * Render the notice.
	 *
	 * @param string $content The content of the post.
	 * @return string The content of the post with the notice added.
	 * @since 2.0.0
	 */
	public function render_notice( string $content ): string {
		// Check if we should display the notice.
		if ( ! $this->should_display_notice() ) {
			return $content;
		}

		// Get the notice HTML.
		$notice_html = $this->get_notice();

		// If no notice HTML, return original content.
		if ( empty( $notice_html ) ) {
			return $content;
		}

		// Get position setting.
		$settings = Settings::get_settings();
		$position = $settings['position'];

		// Add notice to content based on position.
		if ( 'before' === $position ) {
			$content = $notice_html . $content;
		} else {
			$content = $content . $notice_html;
		}

		return $content;
	}

	/**
	 * Get the notice HTML markup.
	 *
	 * @return string The notice HTML.
	 * @since 2.0.0
	 */
	public function get_notice(): string {
		$settings = Settings::get_settings();

		// Check if notice should be displayed based on age.
		if ( ! self::is_post_old_enough( $settings ) ) {
			return '';
		}

		// Get the notice text with date replacement.
		$notice_text = $this->get_notice_text( $settings );

		// Sanitize and format the notice text.
		$notice_text = wp_kses_post( wpautop( $notice_text ) );

		// Generate inline styles.
		$inline_styles = $this->get_inline_styles( $settings );

		// Build the notice HTML.
		$notice_html = '<div class="old-post-notice"' . $inline_styles . '>' . $notice_text . '</div>';

		return $notice_html;
	}

	/**
	 * Check if the notice should be displayed.
	 *
	 * @return bool True if notice should be displayed, false otherwise.
	 * @since 2.0.0
	 */
	private function should_display_notice(): bool {
		// Check if we're on a single post page.
		if ( ! is_single() || ! in_the_loop() || ! is_main_query() ) {
			return false;
		}

		// Check if it's a post.
		if ( 'post' !== get_post_type() ) {
			return false;
		}

		$settings = Settings::get_settings();

		// Check if notice is enabled and has required settings.
		if ( '1' !== $settings['enable'] || empty( $settings['notice'] ) || empty( $settings['days'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if the post is old enough to show the notice.
	 *
	 * @param array $settings The plugin settings.
	 * @return bool True if post is old enough, false otherwise.
	 * @since 2.0.0
	 */
	public static function is_post_old_enough( array $settings ): bool {
		$date = ( 'modified' === $settings['date'] ) ? get_the_modified_date( 'Y-m-d' ) : get_the_date( 'Y-m-d' );

		return strtotime( $date ) < strtotime( '-' . $settings['days'] . ' days' );
	}

	/**
	 * Get the notice text with date replacement.
	 *
	 * @param array $settings The plugin settings.
	 * @return string The notice text.
	 * @since 2.0.0
	 */
	private function get_notice_text( array $settings ): string {
		// Get the default notice text.
		$default_notice = $settings['notice'];

		// Check for a notice set on a post.
		$notice = get_post_meta( get_the_ID(), '_old_post_notice', true );
		$behavior = get_post_meta( get_the_ID(), '_old_post_notice_behavior', true );

		// If a notice has been set on a post and the behavior is set
		if ( ! empty( $notice ) && ! empty( $behavior ) ) {

			if ( 'replace' === $behavior ) {

				// Replace the default notice
				$final_notice = $notice;

			} elseif ( 'append' === $behavior ) {
				/**
				 * Filter the content to be added before an appended notice.
				 *
				 * @param string $append_content The content to be added before the appended notice.
				 * @return string The filtered content.
				 * @since 2.1.0
				 */
				$append_content = apply_filters( 'old_post_notice_before_append', '' );

				// Append the notice to the default notice.
				$final_notice = $default_notice . $append_content . $notice;
			}
		} else {
			// Use default notice if no custom content or invalid behavior
			$final_notice = $default_notice;
		}

		// Apply date replacement once to the final notice text.
		$date_formatted = ( 'modified' === $settings['date'] ) ? get_the_modified_date() : get_the_date();
		return str_replace( '[date]', $date_formatted, $final_notice );
	}

	/**
	 * Get inline styles for the notice.
	 *
	 * @param array $settings The plugin settings.
	 * @return string The inline styles.
	 * @since 2.0.0
	 */
	private function get_inline_styles( array $settings ): string {
		$inline_styles = '';

		if ( 'none' !== $settings['styling'] && ( ! empty( $settings['color_background'] ) || ! empty( $settings['color_text'] ) ) ) {
			$inline_styles .= 'style="';
			$inline_styles .= ( ! empty( $settings['color_background'] ) ? 'background-color: ' . $settings['color_background'] . ';' : '' );
			$inline_styles .= ( ! empty( $settings['color_text'] ) ? 'color: ' . $settings['color_text'] . ';' : '' );
			$inline_styles .= '"';
		}

		return $inline_styles;
	}
}
