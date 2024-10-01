<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'Old_Post_Notice_Display' ) ) {

	class Old_Post_Notice_Display {

		public function __construct() {

			add_filter( 'the_content', array( $this, 'notice' ) );

		}

		public function notice( $content ) {

			// Returns the content with the notice before or after the content on single posts

			if ( is_single() && in_the_loop() && is_main_query() ) {

				if ( 'post' == get_post_type() ) {

					$settings = get_option( 'old_post_notice_settings' );
					$enable = ( isset( $settings['enable'] ) ? $settings['enable'] : '0' ); // This uses isset as it is a checkbox, and if not checked in settings the array entry will not exist, so we need to check if set or get PHP notices
					$notice = $settings['notice'];
					$days = $settings['days'];
					$date = $settings['date'];
					$position = $settings['position'];
					$styling = $settings['styling'];
					$background_color = $settings['color_background'];
					$text_color = $settings['color_text'];

					if ( '1' == $enable && !empty( $notice ) && !empty( $days ) ) {

						$date = ( 'modified' == $date ? get_the_modified_date( 'Y-m-d' ) : get_the_date( 'Y-m-d' ) ); // Get the published or modified date in Y-m-d format
						$date_formatted = ( 'modified' == $date ? get_the_modified_date() : get_the_date() ); // Get the published or modified date formatted to the WordPress date_format setting
						$notice = str_replace( '[date]', $date_formatted, $notice ); // If the [date] placeholder is used replace it with the formatted date

						if ( strtotime( $date ) < strtotime( '-' . $days . ' days' ) ) {

							$inline_styles = '';

							if ( 'none' !== $styling && ( !empty( $background_color ) || !empty( $text_color ) ) ) {

								$inline_styles .= 'style="';
								$inline_styles .= ( !empty( $background_color ) ? 'background-color: ' . $background_color . '; ' : '' );
								$inline_styles .= ( !empty( $text_color ) ? 'color: ' . $text_color . ';' : '' );
								$inline_styles .= '"';

							}

							$notice_html = '<div class="old-post-notice"' . $inline_styles . '>' . wp_kses_post( wpautop( $notice ) ) . '</div>';

							if ( 'before' == $position ) {

								$content = $notice_html . $content;

							} else {

								$content = $content . $notice_html;

							}

						}

					}

				}

			}

			return $content;

		}

	}

}
