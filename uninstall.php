<?php
/**
 * Uninstall
 *
 * @package All Users Messenger
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

global $wpdb;

/* For Single site */
if ( ! is_multisite() ) {
	delete_option( 'all_users_messages' );
	delete_option( 'all_users_messages_settings' );
	$blogusers = get_users( array( 'fields' => array( 'ID' ) ) );
	foreach ( $blogusers as $user ) {
		delete_user_option( $user->ID, 'all_users_messages_latest_time', false );
	}
} else {
	/* For Multisite */
	$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->prefix}blogs" );
	$original_blog_id = get_current_blog_id();
	foreach ( $blog_ids as $blogid ) {
		switch_to_blog( $blogid );
		delete_option( 'all_users_messages' );
		delete_option( 'all_users_messages_settings' );
		$blogusers = get_users(
			array(
				'blog_id' => $blogid,
				'fields' => array( 'ID' ),
			)
		);
		foreach ( $blogusers as $user ) {
			delete_user_option( $user->ID, 'all_users_messages_latest_time', false );
		}
	}
	switch_to_blog( $original_blog_id );

	/* For site options. */
	delete_site_option( 'all_users_messages' );
	delete_site_option( 'all_users_messages_settings' );
}


