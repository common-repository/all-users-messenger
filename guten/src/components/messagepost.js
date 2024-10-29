import './messagepost.css';

import apiFetch from '@wordpress/api-fetch';

import { Button, TextareaControl } from '@wordpress/components';

import {
	useRef,
	useState,
	useEffect
} from '@wordpress/element';

const MessagePost = () => {

	const userid = parseInt( allusersmessenger_data.userid );
	const [ currentMessage, updatecurrentMessage ] = useState( '' );
	const [ currentSubmitmessage, updatecurrentSubmitmessage ] = useState( false );

	const firstUpdateMessage = useRef( true );
	useEffect( () => {
		if ( firstUpdateMessage.current ) {
			firstUpdateMessage.current = false;
			return;
		}
		apiFetch( {
			path: 'rf/all_users_messenger_post_api/token',
			method: 'POST',
			data: {
				userid: userid,
				message: currentMessage,
				submit_message: currentSubmitmessage,
			}
		} ).then( ( response ) => {
			//console.log( response );
			if ( currentSubmitmessage ) {
				updatecurrentSubmitmessage( false );
				updatecurrentMessage( '' );
			}
		} );
	}, [ currentSubmitmessage ] );

	const calcTextAreaHeight = ( value ) => {
		let rowsNum = value.split( '\n' ).length;
		return rowsNum;
	}

	const items_message = [];
	items_message.push(
		<TextareaControl
			name = "area_shift_enter"
			className = "message_text"
			help = { allusersmessenger_data.input_help_label }
			rows = { calcTextAreaHeight( currentMessage ) }
			value = { currentMessage }
			onChange = { ( value ) =>
				{
					updatecurrentMessage( value );
					document.addEventListener( "keydown", checkShiftEnterSubmit, false );
				}
			}
		/>
	);

	const checkShiftEnterSubmit = ( value ) => {
		//console.log( value );
		if ( value.target.type && 'textarea' == value.target.type ) {
			if ( value.target.name && 'area_shift_enter' == value.target.name ) {
				if ( value.shiftKey ) {
					if ( 'Enter' === value.code ) {
						updatecurrentSubmitmessage( true );
					}
				}
			}
		}
	}

	const items_message_submit = [];
	const onclick_submitmessage = () => {
		updatecurrentSubmitmessage( true );
	};
	if ( currentMessage ) {
		items_message_submit.push(
			<Button
				className = "css-button-arrow--green"
				onClick = { onclick_submitmessage }
			>
			{ allusersmessenger_data.submit_label }
			</Button>
		);
	}

	return (
		<div className="MessengerPost">
			{ items_message }
			{ items_message_submit }
		</div>
	);

};

export default MessagePost;

