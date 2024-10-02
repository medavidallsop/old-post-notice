<?php

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'old_post_notice_settings' );
delete_option( 'old_post_notice_version' );

delete_site_option( 'old_post_notice_settings' );
delete_site_option( 'old_post_notice_version' );
