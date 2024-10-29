=== All Users Messenger ===
Contributors: Katsushi Kawamori
Donate link: https://shop.riverforest-wp.info/donate/
Tags: login, message, messenger, users
Requires at least: 5.0
Requires PHP: 7.0
Tested up to: 6.3
Stable tag: 1.24
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Messages can be sent, received, and shared among all logged-in users.

== Description ==

= Messenger =
* All logged in users post and display messages.
* The default number of items displayed is 100; if more than 100 items are displayed, they are erased in order of oldest to newest. This can be changed with the following filter.
* The default user permission is "read", so all users can use it. This can be changed with the following filter.
* The display is updated at 1-second intervals. This can be changed with the following filter.

= Notifies =
* Notify unread messages in a modal window at 60 second intervals. This can be changed with the following filter.

= Title =
* The default title is "All Users Messenger". This can be changed with the following filter.

= How it works =
[youtube https://youtu.be/htygEVp6594]

= Filter hooks =

* The number of messages displayed can be customized. The default is 100 messages.
~~~
/** ==================================================
 * Number of messages displayed filter. Default 100.
 *
 */
add_filter( 'all_users_messenger_messages_max', function(){ return 200; }, 10, 1 );
~~~

* Usage permission filter. Default is read.
~~~
/** ==================================================
 * Capability filter for use. Deafult read.
 *
 */
add_filter( 'all_users_messenger_capability', function(){ return 'edit_post'; }, 10, 1 );
~~~

* The interval between message displays can be customized. The default is 1 second.
~~~
/** ==================================================
 * Message display interval filter. Default 1 sec.
 *
 */
add_filter( 'all_users_messenger_interval', function(){ return 2; }, 10, 1 );
~~~

* The unread check interval for displaying unread messages in the modal window in the administration screen. The default is 60 seconds.
~~~
/** ==================================================
 * Notification unread messages interval seconds filter for modal windows. Default 60 sec.
 *
 */
add_filter( 'all_users_messenger_notify_interval', function(){ return 120; }, 10, 1 );
~~~

* This is the filter for the menu title. The default is "All Users Messenger".
~~~
/** ==================================================
 * Menu tite filter. Default All Users Messenger.
 *
 */
add_filter( 'all_users_messenger_page_title', function(){ return 'Chat'; }, 10, 1 );
add_filter( 'all_users_messenger_menu_title', function(){ return 'Chat'; }, 10, 1 );
~~~

* This is filter for display modal window. The default is True.
~~~
/** ==================================================
 * Display to the modal window filter. Default True.
 *
 */
add_filter( 'all_users_messenger_modal_view', function(){ return false; }, 10, 1 );
~~~

= Action hooks =

* This deletes all messages.
~~~
/** ==================================================
 * Delete all messages.
 *
 */
do_action( 'all_users_messenger_clear_messages' );
~~~

== Installation ==

1. Upload `all-users-messenger` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

none

== Screenshots ==

1. Messenger view
2. Modal window view
3. Settings page

== Changelog ==

= 1.24 =
Capability filter support for admin bar menu and modal display.

= 1.23 =
A "Close" button has been added to the modal window display.

= 1.22 =
A button for deleting all messages has been added to the settings screen.

= 1.21 =
Added action hook to delete all messages.

= 1.20 =
Fixed an issue with the mobile display of the unread view in the admin bar.

= 1.19 =
Fixed problem of modal window view.

= 1.18 =
Fixed errors during installation.

= 1.17 =
The number of unread messages can now be output to the admin bar.
Added the ability to turn on/off the display to the modal window in the settings.
Added filter for display to modal window.

= 1.16 =
WordPress footer switched from hidden to visible.

= 1.15 =
Top and Bottom buttons added.

= 1.14 =
When the browser is resized, it now moves to the message input field.

= 1.13 =
Fixed problem with line breaks in message display.
Fixed a problem in which the input field was covered by an old message displayed when entering a new message.

= 1.12 =
Shortcut keys for sending are now supported.

= 1.11 =
Fixed translation.

= 1.10 =
Added a settings page.

= 1.08 =
Fixed problem with delete button on and off.

= 1.07 =
Turned off the display of avatars of users who post.

= 1.06 =
Added the ability to fix the position of the delete button and switch between display and non-display.

= 1.05 =
Deletion of messages is now supported.

= 1.04 =
Fixed a problem in displaying the number of unread messages.

= 1.03 =
The number of unread messages is now displayed in a modal window.

= 1.02 =
The "Display name publicly as" is now reflected when it is changed.
Added filter for menu title.

= 1.01 =
Message line breaks are now supported.
The modal window display has been made smaller.

= 1.00 =
Initial release.

== Upgrade Notice ==

= 1.00 =

