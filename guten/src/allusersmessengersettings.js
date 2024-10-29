import './allusersmessengersettings.scss';

import apiFetch from '@wordpress/api-fetch';

import { SelectControl, RangeControl, TextControl, ToggleControl, Button, Notice } from '@wordpress/components';

import {
	render,
	useState,
	useEffect
} from '@wordpress/element';

import Credit from './components/credit';

const AllUsersMessengerSettings = () => {

	const options = JSON.parse( allusersmessengersettings_data.settings );
	const allcaps = JSON.parse( allusersmessengersettings_data.allcaps );

	const [ currentOptions, updatecurrentOptions ] = useState( options );
	const [ currentClearnotice, updatecurrentClearnotice ] = useState( '' );
	const [ currentClearmessages, updatecurrentClearmessages ] = useState( false );

	useEffect( () => {
		apiFetch( {
			path: 'rf/all_users_messenger_settings_api/token',
			method: 'POST',
			data: {
				messages_max: currentOptions['messages_max'],
				capability: currentOptions['capability'],
				interval: currentOptions['interval'],
				notify_interval: currentOptions['notify_interval'],
				page_title: currentOptions['page_title'],
				menu_title: currentOptions['menu_title'],
				modal_view: currentOptions['modal_view'],
				clear_messages: currentClearmessages,
			}
		} ).then( ( response ) => {
			//console.log( response );
			if ( currentClearmessages ) {
				updatecurrentClearnotice( allusersmessengersettings_data.clear_notice_label );
				updatecurrentClearmessages( false );
			}
		} );
	}, [ currentOptions, currentClearmessages, currentClearnotice ] );

	const items = [];
	Object.keys( currentOptions ).map(
		( key ) => {
			if( currentOptions.hasOwnProperty ) {
				//console.log( key );
				switch ( key ){
					case 'messages_max':
						items.push(
							<>
								<hr
									className = "width"
								/>
								<RangeControl
									className = "width"
									label = { allusersmessengersettings_data.messages_max_label }
									value = { currentOptions[ key ] }
									onChange = { ( value ) => object_assign( key, value ) }
									min = { 10 }
									max = { 1000 }
									step = { 10 }
								/>
								<hr
									className = "width"
								/>
							</>
						);
						break;
					case 'capability':
						items.push(
							<>
								<SelectControl
									className = "width"
									label = { allusersmessengersettings_data.capability_label }
									value = { currentOptions[ key ] }
									options = { allcaps }
									onChange = { ( value ) => object_assign( key, value ) }
								/>
								<hr
									className = "width"
								/>
							</>
						);
						break;
					case 'interval':
						items.push(
							<>
								<RangeControl
									className = "width"
									label = { allusersmessengersettings_data.interval_label }
									value = { currentOptions[ key ] }
									onChange = { ( value ) => object_assign( key, value ) }
									min = { 1 }
									max = { 60 }
									step = { 1 }
								/>
								<hr
									className = "width"
								/>
							</>
						);
						break;
					case 'notify_interval':
						items.push(
							<>
								<RangeControl
									className = "width"
									label = { allusersmessengersettings_data.notify_interval_label }
									value = { currentOptions[ key ] }
									onChange = { ( value ) => object_assign( key, value ) }
									min = { 30 }
									max = { 600 }
									step = { 10 }
								/>
								<hr
									className = "width"
								/>
							</>
						);
						break;
					case 'page_title':
						items.push(
							<>
								<TextControl
									className = "width"
									label = { allusersmessengersettings_data.page_title_label }
									value = { currentOptions[ key ] }
									onChange = { ( value ) => object_assign( key, value ) }
								/>
								<hr
									className = "width"
								/>
							</>
						);
						break;
					case 'menu_title':
						items.push(
							<>
								<TextControl
									className = "width"
									label = { allusersmessengersettings_data.menu_title_label }
									value = { currentOptions[ key ] }
									onChange = { ( value ) => object_assign( key, value ) }
								/>
								<hr
									className = "width"
								/>
							</>
						);
						break;
					case 'modal_view':
						items.push(
							<>
								<br />
								<ToggleControl
									className = "width"
									label = { allusersmessengersettings_data.modal_view_label }
									checked = { currentOptions[ key ] }
									onChange = { ( value ) => object_assign( key, value ) }
								/>
								<hr
									className = "width"
								/>
							</>
						);
						break;
				}
			}
		}
	);

	const object_assign = ( key, value ) => {
		currentOptions[ key ] = value;
		let data = Object.assign( {}, currentOptions );
		updatecurrentOptions( data );
	}

	const onclick_clear_messages = () => {
		updatecurrentClearmessages( true );
	};
	items.push(
		<>
			{ allusersmessengersettings_data.clear_description_label }
			<p>
				<Button
					className = { 'button button-primary' }
					onClick = { onclick_clear_messages }
				>
				{ allusersmessengersettings_data.clear_messages_label }
				</Button>
			</p>
		</>
	);

	const items_clear_notice = [];
	if ( currentClearnotice ) {
		items_clear_notice.push(
			<Notice
				status = "success"
				onRemove = { () =>
					{
						updatecurrentClearnotice( '' );
					}
				}
			>
			{ currentClearnotice }
			</Notice>
		);
	}

	return (
		<div className="wrap">
		<h2>All Users Messenger { allusersmessengersettings_data.settings_label }</h2>
			<Credit />
			<div className="wrap">
				{ items_clear_notice }
				{ items }
			</div>
		</div>
	);

};

render(
	<AllUsersMessengerSettings />,
	document.getElementById( 'all-users-messenger-settings-page' )
);

