<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'Old_Post_Notice_Translation' ) ) {

	class Old_Post_Notice_Translation {

		public function __construct() {

			add_action( 'init', array( $this, 'textdomain' ) );

		}

		public function textdomain() {

			load_plugin_textdomain( 'old-post-notice', false, dirname( plugin_basename( __DIR__ ) ) . '/languages' );

		}

	}

}
