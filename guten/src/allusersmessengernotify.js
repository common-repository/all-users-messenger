import './allusersmessengernotify.scss';

import apiFetch from '@wordpress/api-fetch';

import { Button, Modal } from '@wordpress/components';

import {
	render,
	useState,
	useEffect
} from '@wordpress/element';

const AllUsersMessengerNotify = () => {

	const userid = parseInt( allusersmessengernotify_data.userid );
	const interval_sec = parseInt( allusersmessengernotify_data.interval_sec ) * 1000;
	const notify_message = JSON.parse( allusersmessengernotify_data.notify_message );

	const [ currentUsername, updatecurrentUsername ] = useState( notify_message.username );
	const [ currentAvatar, updatecurrentAvatar ] = useState( notify_message.avatar );
	const [ currentDatetime, updatecurrentDatetime ] = useState( notify_message.datetime );
	const [ currentMessage, updatecurrentMessage ] = useState( notify_message.message );
	const [ currentUnread, updatecurrentUnread ] = useState( parseInt( notify_message.unread ) );
	const [ currentUnreadtext, updatecurrentUnreadtext ] = useState( notify_message.unread_text );

	const [ isOpen, setOpen ] = useState( Boolean( allusersmessengernotify_data.message_modal ) );
	const [ isBar, setBar ] = useState( Boolean( allusersmessengernotify_data.notify ) );
	const openModal = () => setOpen( true );
	const closeModal = () => setOpen( false );

	useEffect( () => {
		let timer = setInterval( () => {
			apiFetch( {
				path: 'rf/all_users_messenger_notify_api/token',
				method: 'POST',
				data: {
					userid: userid,
				}
			} ).then( ( response ) => {
				//console.log( response );
				if ( response['notify'] ) {
					updatecurrentUnread( response['unread'] );
					setBar( true );
					if ( response['message_modal'] ) {
						updatecurrentUsername( response['username'] );
						updatecurrentAvatar( response['avatar'] );
						updatecurrentDatetime( response['datetime'] );
						updatecurrentMessage( response['message'] );
						updatecurrentUnreadtext( response['unread_text'] );
						setOpen( true );
					}
				}
			} );
		}, interval_sec );
		return () => {
			clearInterval( timer );
		};
	}, [ currentUsername, currentDatetime, currentMessage, currentUnread ] );

	const items_modal = [];
	if ( isOpen ) {
		let split_message = currentMessage.split( /\r\n|\n/ );
		let items_message2 = [];
		Object.keys( split_message ).map(
			( key ) => {
				items_message2.push(
					<>
					{ split_message[ key ] }<br />
					</>
				);
			}
		);
		items_modal.push(
			<>
				<Modal
					title = { allusersmessengernotify_data.modal_title }
					onRequestClose = { closeModal }
					isDismissible = { true }
					className="message_notify_modal_content"
				>
					<div className="avatar-name-datetime-message">
						<img src={ currentAvatar } />
						<span>
							{ currentUsername }<br />
							{ currentDatetime }<br />
							{ items_message2 }
						</span>
					</div>
					<p>
						<p className="description">
							{ currentUnreadtext }
						</p>
					</p>
					<Button
						variant="primary"
						className="message_notify_button"
						onClick = { closeModal }
					>
						{ allusersmessengernotify_data.close_label }
					</Button>
					<p className="message_notify_title">{ allusersmessengernotify_data.menu_title }</p>
				</Modal>
			</>
		);
	}

	const items_bar = [];
	if ( isBar ) {
		items_bar.push(
			<>
				{ allusersmessengernotify_data.unread_bar_label }<span className="notify_bar_num">{ currentUnread }</span>
			</>
		);
	}

	return (
		<>
			{ items_modal }
			{ items_bar }
		</>
	);

};

render(
	<AllUsersMessengerNotify />,
	document.getElementById( 'all-users-messenger-notify' )
);

