import './allusersmessenger.scss';

import MessagesView from './components/messagesview';

import MessagePost from './components/messagepost';

import { render } from '@wordpress/element';

const AllUsersMessenger = () => {

	return (
		<div className="wrap">
			<MessagesView />
			<MessagePost />
		</div>
	);

};

render(
	<AllUsersMessenger />,
	document.getElementById( 'all-users-messenger-page' )
);

