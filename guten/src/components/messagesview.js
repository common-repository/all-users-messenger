import './messagesview.css';

import apiFetch from '@wordpress/api-fetch';

import { Button, CheckboxControl } from '@wordpress/components';

import {
	useRef,
	useState,
	useEffect
} from '@wordpress/element';

const MessagesView = () => {

	const userid = parseInt( allusersmessenger_data.userid );
	const interval_sec = parseInt( allusersmessenger_data.interval_sec ) * 1000;
	const messages = JSON.parse( allusersmessenger_data.messages );
	const messages_delete = JSON.parse( allusersmessenger_data.messages_delete );
	const [ currentMessages, updatecurrentMessages ] = useState( messages );
	const [ currentDeletemessage, updatecurrentDeletemessage ] = useState( messages_delete );
	const [ currentDeletecheck, updatecurrentDeletecheck ] = useState( false );
	const [ currentLatesttime, updatecurrentLatesttime ] = useState( parseInt( allusersmessenger_data.latest_time ) );
	const [ currentSubmitdelete, updatecurrentSubmitdelete ] = useState( false );

	const firstscrollBottomRef = useRef( true );
	const scrollBottomRef = useRef();
	const scrollTopRef = useRef();

	useEffect( () => {
		let timer = setInterval( () => {
			apiFetch( {
				path: 'rf/all_users_messenger_view_api/token',
				method: 'POST',
				data: {
					userid: userid,
					delete: currentDeletemessage,
					submit_delete: currentSubmitdelete,
				}
			} ).then( ( response ) => {
				//console.log( response );
				if ( firstscrollBottomRef.current ) {
					/* First load */
					firstscrollBottomRef.current.scrollIntoView();
					firstscrollBottomRef.current = false;
				}
				if ( JSON.stringify( currentMessages ) !== JSON.stringify( response['messages'] ) ) {
					updatecurrentLatesttime( response['latest_time'] );
					updatecurrentMessages( response['messages'] );
					scrollBottomRef.current.scrollIntoView();
				}
				if ( currentSubmitdelete ) {
					delete_assign( currentDeletemessage );
				}
				updatecurrentSubmitdelete( false );
				window.addEventListener('resize', () => {
					scrollBottomRef.current.scrollIntoView();
				});
			} );
		}, interval_sec );
		return () => {
			clearInterval( timer );
		};
	}, [ currentMessages, currentDeletemessage, currentSubmitdelete ] );

	const items_messages = [];
	if( typeof currentMessages !== 'undefined' && typeof currentLatesttime !== 'undefined' ) {
		Object.entries( currentMessages ).map(
			( key ) => {
				//console.log( key );
				if( currentMessages.hasOwnProperty ) {
					let split_message = key[1].message.split( /\r\n|\n/ );
					let items_message2 = [];
					Object.keys( split_message ).map(
						( key2 ) => {
							items_message2.push(
								<>
								{ split_message[ key2 ] }<br />
								</>
							);
						}
					);
					if ( key[1].userid == userid ) {
						items_messages.push(
							<div className="balloon_r">
								<p className={ 'says says_r_color' }>
								{ key[1].username }<br />
								{ key[1].datetime }<br />
								{ items_message2 }
								</p>
								<CheckboxControl
									checked={ currentDeletemessage[ parseInt( key[0] ) ] }
									onChange={ ( value ) => object_assign( parseInt( key[0] ), value ) }
								/>
							</div>
						);
					} else {
						items_messages.push(
							<div className="balloon_l">
								<div className="faceicon">
									<img src={ key[1].avatar } />
								</div>
								<p className={ 'says says_l_color' }>
								{ key[1].username }<br />
								{ key[1].datetime }<br />
								{ items_message2 }
								</p>
							</div>
						);
					}
				}
			}
		);
	}

	const onclick_submitdelete = () => {
		updatecurrentSubmitdelete( true );
		updatecurrentDeletecheck( false );
	};
	const items_delete_button = [];
	if( typeof currentDeletemessage !== 'undefined' &&
		typeof currentSubmitdelete !== 'undefined' &&
		typeof currentDeletecheck !== 'undefined' ) {
		if ( currentDeletecheck ) {
			items_delete_button.push(
				<div className="delete_button">
					<Button
						className = { 'button button-primary' }
						onClick = { onclick_submitdelete }
					>
					{ allusersmessenger_data.delete_label }
					</Button>
				</div>
			);
		}
	}

	const delete_assign = ( value ) => {
		Object.keys( value ).map(
			( key ) => {
				if ( value[ key ] ) {
					delete currentDeletemessage[ key ];
					let data = Object.assign( {}, currentDeletemessage );
					updatecurrentDeletemessage( data );
				}
			}
		);
		//console.log( currentDeletemessage );
		check_delete( currentDeletemessage );
	}

	const object_assign = ( key, value ) => {
		currentDeletemessage[ key ] = value;
		let data = Object.assign( {}, currentDeletemessage );
		updatecurrentDeletemessage( data );
		check_delete( currentDeletemessage );
	}

	const check_delete = ( value ) => {
		const arr = Object.values( value );
		let result = arr.includes( true );
		//console.log( arr, result );
		updatecurrentDeletecheck( result );
	}

	const items_top_button = [];
	if( scrollTopRef ) {
		const onclick_submittop = () => {
			scrollTopRef.current.scrollIntoView();
		};
		items_top_button.push(
			<Button
				className = { 'button button-large top_button' }
				onClick = { onclick_submittop }
			>
			{ allusersmessenger_data.top_button_label }&nbsp;&nbsp;&uarr;
			</Button>
		);
	}

	const items_bottom_button = [];
	if( scrollBottomRef ) {
		const onclick_submitbottom = () => {
			scrollBottomRef.current.scrollIntoView();
		};
		items_bottom_button.push(
			<Button
				className = { 'button button-large bottom_button' }
				onClick = { onclick_submitbottom }
			>
			{ allusersmessenger_data.bottom_button_label }&nbsp;&nbsp;&darr;
			</Button>
		);
	}

	return (
		<>
			<div ref = { scrollTopRef } />
			{ items_bottom_button }
			{ items_messages }
			{ items_delete_button }
			{ items_top_button }
			<div ref = { firstscrollBottomRef } />
			<div ref = { scrollBottomRef } />
		</>
	);

};

export default MessagesView;

