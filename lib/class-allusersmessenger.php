<?php
/**
 * All Users Messenger
 *
 * @package    All Users Messenger
 * @subpackage AllUsersMessenger Main Functions
/*  Copyright (c) 2022- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; version 2 of the License.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

$allusersmessenger = new AllUsersMessenger();

/** ==================================================
 * Main Functions
 */
class AllUsersMessenger {

	/** ==================================================
	 * Construct
	 *
	 * @since 1.00
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_filter( 'plugin_action_links', array( $this, 'settings_link' ), 10, 2 );

		add_action( 'admin_menu', array( $this, 'add_pages' ), 99999 );

		add_action( 'admin_bar_menu', array( $this, 'customize_admin_bar_menu' ), 99999 );

		add_action( 'rest_api_init', array( $this, 'register_rest' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 10, 1 );

		add_action( 'profile_update', array( $this, 'change_display_name' ), 10, 3 );

		add_action( 'admin_print_scripts', array( $this, 'admin_bar_style' ) );

		add_action( 'all_users_messenger_clear_messages', array( $this, 'clear_messages' ) );

	}

	/** ==================================================
	 * Add a "Settings" link to the plugins page
	 *
	 * @param  array  $links  links array.
	 * @param  string $file   file.
	 * @return array  $links  links array.
	 * @since 1.00
	 */
	public function settings_link( $links, $file ) {
		static $this_plugin;
		if ( empty( $this_plugin ) ) {
			$this_plugin = 'all-users-messenger/allusersmessenger.php';
		}
		if ( $file == $this_plugin ) {
			$links[] = '<a href="' . admin_url( 'admin.php?page=AllUsersMessenger' ) . '">All Users Messenger</a>';
			$links[] = '<a href="' . admin_url( 'options-general.php?page=allusersmessenger-settings' ) . '">' . __( 'Settings' ) . '</a>';
		}
		return $links;
	}

	/** ==================================================
	 * Add page
	 *
	 * @since 1.00
	 */
	public function add_pages() {

		if ( get_option( 'all_users_messages_settings' ) ) {
			$all_users_messages_settings = get_option( 'all_users_messages_settings' );
			$page_title = $all_users_messages_settings['page_title'];
			$menu_title = $all_users_messages_settings['menu_title'];
			$capability = $all_users_messages_settings['capability'];
		} else {
			$page_title = 'All Users Messenger';
			$menu_title = 'All Users Messenger';
			$capability = 'read';
		}

		add_menu_page(
			apply_filters( 'all_users_messenger_page_title', $page_title ),
			apply_filters( 'all_users_messenger_menu_title', $menu_title ),
			apply_filters( 'all_users_messenger_capability', $capability ),
			'AllUsersMessenger',
			array( $this, 'all_users_messenger_page' ),
			'dashicons-groups'
		);

		add_options_page(
			'All Users Messenger',
			'All Users Messenger',
			'manage_options',
			'allusersmessenger-settings',
			array( $this, 'settings_page' )
		);

		remove_filter( 'update_footer', 'core_update_footer' );

	}

	/** ==================================================
	 * All Users Messenger page
	 *
	 * @since 1.00
	 */
	public function all_users_messenger_page() {

		echo '<div id="all-users-messenger-page"></div>';

	}

	/** ==================================================
	 * All Users Messenger settings page
	 *
	 * @since 1.00
	 */
	public function settings_page() {

		echo '<div id="all-users-messenger-settings-page"></div>';

	}

	/** ==================================================
	 * Load script
	 *
	 * @param string $hook_suffix  hook_suffix.
	 * @since 1.00
	 */
	public function admin_scripts( $hook_suffix ) {

		$all_users_messages_settings = get_option( 'all_users_messages_settings' );

		if ( 'toplevel_page_AllUsersMessenger' === $hook_suffix ) {
			$asset_file = include( plugin_dir_path( __DIR__ ) . 'guten/dist/messenger/allusersmessenger.asset.php' );

			wp_enqueue_style(
				'allusersmessenger-style',
				plugin_dir_url( __DIR__ ) . 'guten/dist/messenger/allusersmessenger.css',
				array( 'wp-components' ),
				'1.0.0',
			);

			wp_enqueue_script(
				'allusersmessenger',
				plugin_dir_url( __DIR__ ) . 'guten/dist/messenger/allusersmessenger.js',
				$asset_file['dependencies'],
				$asset_file['version'],
				true
			);

			$messages = get_option( 'all_users_messages', array() );
			$messages_delete = array();
			if ( ! empty( $messages ) ) {
				foreach ( $messages as $key => $value ) {
					$messages_delete[ $key ] = false;
				}
			}

			$interval_sec = apply_filters( 'all_users_messenger_interval', $all_users_messages_settings['interval'] );

			$latest_time = 0;
			if ( ! empty( $messages ) ) {
				$latest_time = max( array_keys( $messages ) );
			}

			wp_localize_script(
				'allusersmessenger',
				'allusersmessenger_data',
				array(
					'userid' => get_current_user_id(),
					'messages' => json_encode( $messages, JSON_UNESCAPED_SLASHES ),
					'messages_delete' => json_encode( $messages_delete, JSON_UNESCAPED_SLASHES ),
					'interval_sec' => $interval_sec,
					'latest_time' => $latest_time,
					'submit_label' => __( 'Submit' ),
					'delete_label' => __( 'Delete' ),
					'input_help_label' => __( 'While typing, you can press Shift + Enter(Return) to send.', 'all-users-messenger' ),
					'top_button_label' => __( 'Top', 'all-users-messenger' ),
					'bottom_button_label' => __( 'Bottom', 'all-users-messenger' ),
				)
			);
		} else if ( 'settings_page_allusersmessenger-settings' === $hook_suffix ) {

			$asset_file = include( plugin_dir_path( __DIR__ ) . 'guten/dist/settings/allusersmessengersettings.asset.php' );

			wp_enqueue_style(
				'allusersmessengersettings-style',
				plugin_dir_url( __DIR__ ) . 'guten/dist/settings/allusersmessengersettings.css',
				array( 'wp-components' ),
				'1.0.0',
			);

			wp_enqueue_script(
				'allusersmessengersettings',
				plugin_dir_url( __DIR__ ) . 'guten/dist/settings/allusersmessengersettings.js',
				$asset_file['dependencies'],
				$asset_file['version'],
				true
			);

			$allcaps = array();
			$users = get_users();
			foreach ( $users as $user ) {
				$caps = array_keys( $user->allcaps );
				foreach ( $caps as $cap ) {
					if ( ! in_array( $cap, $allcaps, true ) ) {
						$allcaps[] = array(
							'label' => $cap,
							'value' => $cap,
						);
					}
				}
			}

			wp_localize_script(
				'allusersmessengersettings',
				'allusersmessengersettings_data',
				array(
					'messages_max_label' => __( 'Number of messages displayed', 'all-users-messenger' ),
					'capability_label' => __( 'Capability for use', 'all-users-messenger' ),
					'interval_label' => __( 'Message display interval seconds', 'all-users-messenger' ),
					'notify_interval_label' => __( 'Notification unread messages interval seconds', 'all-users-messenger' ),
					'page_title_label' => __( 'Page title', 'all-users-messenger' ),
					'menu_title_label' => __( 'Menu title', 'all-users-messenger' ),
					'modal_view_label' => __( 'Modal Window Display', 'all-users-messenger' ),
					'settings_label' => __( 'Settings' ),
					'clear_description_label' => __( 'Delete all messages', 'all-users-messenger' ),
					'clear_messages_label' => __( 'Clear' ),
					'clear_notice_label' => __( 'All messages have been deleted.', 'all-users-messenger' ),
					'settings' => json_encode( $all_users_messages_settings, JSON_UNESCAPED_SLASHES ),
					'allcaps' => json_encode( $allcaps, JSON_UNESCAPED_SLASHES ),
				)
			);

			$this->credit( 'allusersmessengersettings' );

		} else {
			$asset_file = include( plugin_dir_path( __DIR__ ) . 'guten/dist/notify/allusersmessengernotify.asset.php' );

			wp_enqueue_style(
				'allusersmessengernotify-style',
				plugin_dir_url( __DIR__ ) . 'guten/dist/notify/allusersmessengernotify.css',
				array( 'wp-components' ),
				'1.0.0',
			);

			wp_enqueue_script(
				'allusersmessengernotify',
				plugin_dir_url( __DIR__ ) . 'guten/dist/notify/allusersmessengernotify.js',
				$asset_file['dependencies'],
				$asset_file['version'],
				true
			);

			$interval_sec = apply_filters( 'all_users_messenger_notify_interval', $all_users_messages_settings['notify_interval'] );

			$notify = false;
			$message_modal = false;
			$notify_message = array();
			$messages = get_option( 'all_users_messages', array() );
			if ( ! empty( $messages ) ) {
				$times = array_keys( $messages );
				$latest_time = max( $times );
				$user_latest_time = get_user_option( 'all_users_messages_latest_time', get_current_user_id() );
				if ( $latest_time > $user_latest_time ) {
					$notify = true;
					$message_modal = apply_filters( 'all_users_messenger_modal_view', boolval( $all_users_messages_settings['modal_view'] ) );
					$unread = 0;
					foreach ( $times as $time ) {
						if ( $time > $user_latest_time ) {
							++$unread;
						}
					}
					$notify_message = array(
						'username' => $messages[ $latest_time ]['username'],
						'avatar' => $messages[ $latest_time ]['avatar'],
						'datetime' => $messages[ $latest_time ]['datetime'],
						'message' => $messages[ $latest_time ]['message'],
						'unread' => $unread,
					);
					if ( 1 < $unread ) {
						/* translators: %1$d -> unread messages count */
						$notify_message['unread_text'] = sprintf( __( 'The total number of unread messages is %1$d, including this latest message.', 'all-users-messenger' ), $unread );
					} else {
						$notify_message['unread_text'] = null;
					}
				}
			}

			wp_localize_script(
				'allusersmessengernotify',
				'allusersmessengernotify_data',
				array(
					'userid' => get_current_user_id(),
					'interval_sec' => intval( $interval_sec ),
					'notify' => $notify,
					'message_modal' => $message_modal,
					'notify_message' => json_encode( $notify_message, JSON_UNESCAPED_SLASHES ),
					'modal_title' => __( 'There are new messages', 'all-users-messenger' ),
					'menu_title' => apply_filters( 'all_users_messenger_menu_title', $all_users_messages_settings['menu_title'] ),
					'close_label' => __( 'Close' ),
					'unread_bar_label' => __( 'Unread', 'all-users-messenger' ),
				)
			);
		}

	}

	/** ==================================================
	 * Register Rest API
	 *
	 * @since 1.00
	 */
	public function register_rest() {

		register_rest_route(
			'rf/all_users_messenger_view_api',
			'/token',
			array(
				'methods' => 'POST',
				'callback' => array( $this, 'messages_api_view' ),
				'permission_callback' => array( $this, 'users_rest_permission' ),
			),
		);

		register_rest_route(
			'rf/all_users_messenger_post_api',
			'/token',
			array(
				'methods' => 'POST',
				'callback' => array( $this, 'messages_api_post' ),
				'permission_callback' => array( $this, 'users_rest_permission' ),
			),
		);

		register_rest_route(
			'rf/all_users_messenger_notify_api',
			'/token',
			array(
				'methods' => 'POST',
				'callback' => array( $this, 'messages_api_notify' ),
				'permission_callback' => array( $this, 'users_rest_permission' ),
			),
		);

		register_rest_route(
			'rf/all_users_messenger_settings_api',
			'/token',
			array(
				'methods' => 'POST',
				'callback' => array( $this, 'settings_api' ),
				'permission_callback' => array( $this, 'settings_rest_permission' ),
			),
		);

	}

	/** ==================================================
	 * Rest Permission for users
	 *
	 * @since 1.00
	 */
	public function users_rest_permission() {

		$all_users_messages_settings = get_option( 'all_users_messages_settings' );
		$capability = apply_filters( 'all_users_messenger_capability', $all_users_messages_settings['capability'] );

		return current_user_can( $capability );

	}

	/** ==================================================
	 * Rest Permission for settings
	 *
	 * @since 1.10
	 */
	public function settings_rest_permission() {

		return current_user_can( 'manage_options' );

	}

	/** ==================================================
	 * Rest API view
	 *
	 * @param object $request  changed data.
	 * @since 1.00
	 */
	public function messages_api_view( $request ) {

		$args = json_decode( $request->get_body(), true );

		$messages = get_option( 'all_users_messages', array() );
		if ( ! empty( $messages ) ) {
			if ( $args['submit_delete'] ) {
				$deletes = filter_var(
					wp_unslash( $args['delete'] ),
					FILTER_CALLBACK,
					array(
						'options' => function( $value ) {
							return intval( $value );
						},
					)
				);
				foreach ( $deletes as $key => $value ) {
					if ( 1 === $value ) {
						unset( $messages[ $key ] );
					}
				}
				update_option( 'all_users_messages', $messages );
				/* Reload for delete */
				$messages = get_option( 'all_users_messages', array() );
			}
		}

		$userid = intval( $args['userid'] );
		$args['latest_time'] = 0;
		if ( ! empty( $messages ) ) {
			$args['latest_time'] = max( array_keys( $messages ) );
		}

		update_user_option( $userid, 'all_users_messages_latest_time', $args['latest_time'] );

		$args['messages'] = $messages;

		return new WP_REST_Response( $args, 200 );

	}

	/** ==================================================
	 * Rest API post
	 *
	 * @param object $request  changed data.
	 * @since 1.00
	 */
	public function messages_api_post( $request ) {

		$args = json_decode( $request->get_body(), true );

		$message = sanitize_textarea_field( wp_unslash( $args['message'] ) );

		if ( $args['submit_message'] && ! empty( $message ) ) {
			$userid = intval( $args['userid'] );
			$datetime = wp_date( 'Y-m-d H:i' );
			$user = get_userdata( $userid );
			$avatar = get_avatar_url( $userid );
			$add_message = array(
				'userid' => $userid,
				'username' => $user->display_name,
				'avatar' => $avatar,
				'datetime' => $datetime,
				'message' => $message,
			);
			$time = time();

			$all_users_messages_settings = get_option( 'all_users_messages_settings' );
			$messages_max = apply_filters( 'all_users_messenger_messages_max', $all_users_messages_settings['messages_max'] );

			$messages = get_option( 'all_users_messages', array() );
			$messages[ $time ] = $add_message;
			krsort( $messages );
			$messages = array_slice( $messages, 0, $messages_max, true );
			update_option( 'all_users_messages', $messages );

		}

		return new WP_REST_Response( $args, 200 );

	}

	/** ==================================================
	 * Rest API notify
	 *
	 * @param object $request  changed data.
	 * @since 1.00
	 */
	public function messages_api_notify( $request ) {

		$args = json_decode( $request->get_body(), true );

		$all_users_messages_settings = get_option( 'all_users_messages_settings' );

		$messages = get_option( 'all_users_messages', array() );
		if ( ! empty( $messages ) ) {
			$times = array_keys( $messages );
			$latest_time = max( $times );
			$userid = intval( $args['userid'] );
			$user_latest_time = get_user_option( 'all_users_messages_latest_time', $userid );
			if ( $latest_time > $user_latest_time ) {
				$args['notify'] = true;
				$args['message_modal'] = apply_filters( 'all_users_messenger_modal_view', boolval( $all_users_messages_settings['modal_view'] ) );
				$args['username'] = $messages[ $latest_time ]['username'];
				$args['avatar'] = $messages[ $latest_time ]['avatar'];
				$args['datetime'] = $messages[ $latest_time ]['datetime'];
				$args['message'] = $messages[ $latest_time ]['message'];
				$unread = 0;
				foreach ( $times as $time ) {
					if ( $time > $user_latest_time ) {
						++$unread;
					}
				}
				$args['unread'] = $unread;
				if ( 1 < $unread ) {
					/* translators: %1$d -> unread messages count */
					$args['unread_text'] = sprintf( __( 'The total number of unread messages is %1$d, including this latest message.', 'all-users-messenger' ), $unread );
				} else {
					$args['unread_text'] = null;
				}
			}
		}

		return new WP_REST_Response( $args, 200 );

	}

	/** ==================================================
	 * Admin Bar Menu
	 *
	 * @param array $wp_admin_bar  wp_admin_bar.
	 * @since 1.00
	 */
	public function customize_admin_bar_menu( $wp_admin_bar ) {

		if ( get_option( 'all_users_messages_settings' ) ) {
			$all_users_messages_settings = get_option( 'all_users_messages_settings' );
			$capability = $all_users_messages_settings['capability'];
		} else {
			$capability = 'read';
		}
		$capability = apply_filters( 'all_users_messenger_capability', $capability );

		if ( current_user_can( $capability ) ) {

			$notify = '<span id="all-users-messenger-notify"></span>';

			$wp_admin_bar->add_menu(
				array(
					'id'    => 'allusersmessenger-bar-menu',
					'title' => '<a href="' . admin_url( 'admin.php?page=AllUsersMessenger' ) . '">' . $notify . '</a>',
				)
			);

			$all_users_messages_settings = get_option( 'all_users_messages_settings' );

			$wp_admin_bar->add_menu(
				array(
					'id'        => 'allusersmessenger-bar-description',
					'parent'    => 'allusersmessenger-bar-menu',
					'title'     => '<a href="' . admin_url( 'admin.php?page=AllUsersMessenger' ) . '">' . apply_filters( 'all_users_messenger_menu_title', $all_users_messages_settings['menu_title'] ) . '</a>',
				)
			);

		}

	}

	/** ==================================================
	 * Change display name
	 *
	 * @param int    $user_id  user ID.
	 * @param object $old_user_data  old userdata.
	 * @param array  $userdata  userdata.
	 * @since 1.02
	 */
	public function change_display_name( $user_id, $old_user_data, $userdata ) {

		$messages = get_option( 'all_users_messages', array() );
		if ( ! empty( $messages ) ) {
			foreach ( $messages as $key => $value ) {
				if ( $user_id == $value['userid'] ) {
					$messages[ $key ]['username'] = $userdata['display_name'];
				}
			}
			update_option( 'all_users_messages', $messages );
		}

	}

	/** ==================================================
	 * Settings register
	 *
	 * @since 1.10
	 */
	public function register_settings() {

		if ( get_option( 'all_users_messages_settings' ) ) {
			$all_users_messages_settings = get_option( 'all_users_messages_settings' );
			if ( ! array_key_exists( 'modal_view', $all_users_messages_settings ) ) {
				$all_users_messages_settings['modal_view'] = true;
				update_option( 'all_users_messages_settings', $all_users_messages_settings );
			}
		} else {
			$all_users_messages_settings = array(
				'messages_max' => 100,
				'capability' => 'read',
				'interval' => 1,
				'notify_interval' => 60,
				'page_title' => 'All Users Messenger',
				'menu_title' => 'All Users Messenger',
				'modal_view' => true,
			);
			update_option( 'all_users_messages_settings', $all_users_messages_settings );
		}

	}

	/** ==================================================
	 * Rest API Settings
	 *
	 * @param object $request  changed data.
	 * @since 1.10
	 */
	public function settings_api( $request ) {

		$args = json_decode( $request->get_body(), true );

		$all_users_messages_settings = get_option( 'all_users_messages_settings', array() );
		if ( ! empty( $all_users_messages_settings ) ) {
			if ( $args['messages_max'] ) {
				$all_users_messages_settings['messages_max'] = intval( $args['messages_max'] );
			}
			if ( $args['capability'] ) {
				$all_users_messages_settings['capability'] = sanitize_text_field( wp_unslash( $args['capability'] ) );
			}
			if ( $args['interval'] ) {
				$all_users_messages_settings['interval'] = intval( $args['interval'] );
			}
			if ( $args['notify_interval'] ) {
				$all_users_messages_settings['notify_interval'] = intval( $args['notify_interval'] );
			}
			if ( $args['page_title'] ) {
				$all_users_messages_settings['page_title'] = sanitize_text_field( wp_unslash( $args['page_title'] ) );
			}
			if ( $args['menu_title'] ) {
				$all_users_messages_settings['menu_title'] = sanitize_text_field( wp_unslash( $args['menu_title'] ) );
			}
			if ( $args['modal_view'] ) {
				$all_users_messages_settings['modal_view'] = true;
			} else {
				$all_users_messages_settings['modal_view'] = false;
			}

			update_option( 'all_users_messages_settings', $all_users_messages_settings );
		}

		if ( $args['clear_messages'] ) {
			do_action( 'all_users_messenger_clear_messages' );
		}

		return new WP_REST_Response( $args, 200 );

	}

	/** ==================================================
	 * Admin Bar Style
	 *
	 * @since 1.20
	 */
	public function admin_bar_style() {

		?>
		<style>
			@media screen and (max-width: 782px) {
				li#wp-admin-bar-allusersmessenger-bar-menu {
					display: block !important;
				}
			}
		</style>
		<?php

	}

	/** ==================================================
	 * Clear all messages data
	 *
	 * @since 1.21
	 */
	public function clear_messages() {

		global $wpdb;

		/* For Single site */
		if ( ! is_multisite() ) {
			delete_option( 'all_users_messages' );
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
		}

	}

	/** ==================================================
	 * Credit
	 *
	 * @param string $handle  handle.
	 * @since 1.10
	 */
	private function credit( $handle ) {

		$plugin_name    = null;
		$plugin_ver_num = null;
		$plugin_path    = plugin_dir_path( __DIR__ );
		$plugin_dir     = untrailingslashit( wp_normalize_path( $plugin_path ) );
		$slugs          = explode( '/', $plugin_dir );
		$slug           = end( $slugs );
		$files          = scandir( $plugin_dir );
		foreach ( $files as $file ) {
			if ( '.' === $file || '..' === $file || is_dir( $plugin_path . $file ) ) {
				continue;
			} else {
				$exts = explode( '.', $file );
				$ext  = strtolower( end( $exts ) );
				if ( 'php' === $ext ) {
					$plugin_datas = get_file_data(
						$plugin_path . $file,
						array(
							'name'    => 'Plugin Name',
							'version' => 'Version',
						)
					);
					if ( array_key_exists( 'name', $plugin_datas ) && ! empty( $plugin_datas['name'] ) && array_key_exists( 'version', $plugin_datas ) && ! empty( $plugin_datas['version'] ) ) {
						$plugin_name    = $plugin_datas['name'];
						$plugin_ver_num = $plugin_datas['version'];
						break;
					}
				}
			}
		}

		wp_localize_script(
			$handle,
			'credit',
			array(
				'links'          => __( 'Various links of this plugin', 'all-users-messenger' ),
				'plugin_version' => __( 'Version:' ) . ' ' . $plugin_ver_num,
				/* translators: FAQ Link & Slug */
				'faq'            => sprintf( __( 'https://wordpress.org/plugins/%s/faq', 'all-users-messenger' ), $slug ),
				'support'        => 'https://wordpress.org/support/plugin/' . $slug,
				'review'         => 'https://wordpress.org/support/view/plugin-reviews/' . $slug,
				'translate'      => 'https://translate.wordpress.org/projects/wp-plugins/' . $slug,
				/* translators: Plugin translation link */
				'translate_text' => sprintf( __( 'Translations for %s' ), $plugin_name ),
				'facebook'       => 'https://www.facebook.com/katsushikawamori/',
				'twitter'        => 'https://twitter.com/dodesyo312',
				'youtube'        => 'https://www.youtube.com/channel/UC5zTLeyROkvZm86OgNRcb_w',
				'donate'         => __( 'https://shop.riverforest-wp.info/donate/', 'all-users-messenger' ),
				'donate_text'    => __( 'Please make a donation if you like my work or would like to further the development of this plugin.', 'all-users-messenger' ),
				'donate_button'  => __( 'Donate to this plugin &#187;' ),
			)
		);

	}

}


