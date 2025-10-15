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

delete_option( 'old_post_notice_settings' );
delete_option( 'old_post_notice_version' );
