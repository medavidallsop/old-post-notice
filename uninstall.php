<?php
/**
 * Uninstall script for the plugin.
 *
 * This file is called when the plugin is uninstalled.
 *
 * @package OldPostNotice
 * @since 2.0.0
 */

defined( 'WP_UNINSTALL_PLUGIN' ) or exit;

global $wpdb;

// Options
delete_option( 'old_post_notice_settings' );
delete_option( 'old_post_notice_version' );

// Post meta
$wpdb->delete(
	$wpdb->postmeta,
	array( 'meta_key' => '_old_post_notice' ),
	array( '%s' )
);
$wpdb->delete(
	$wpdb->postmeta,
	array( 'meta_key' => '_old_post_notice_behavior' ),
	array( '%s' )
);
