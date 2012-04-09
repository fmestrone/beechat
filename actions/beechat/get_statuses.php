<?php

header('Content-type: application/json');
gatekeeper();

$usernames = get_input('beechat_roster_items_usernames');
if (!empty($usernames)) {
	$iconSize = 'small';
	$rosterItemsUsernames = explode(',', $usernames);
	$userFriendsEntities = elgg_get_logged_in_entity()->getFriends('', count($rosterItemsUsernames), 0);
	
	$res = array();
	foreach ($rosterItemsUsernames as $value) {
		foreach ($userFriendsEntities as $friend) {
			if (strtolower($friend->username) == strtolower($value)) {
				$status = get_entities_from_metadata("state", "current", "object", "status", $friend->get('guid'));
				$res[$value] = ($status != false) ? $status[0]->description : '';
				break;
			}
		}
	}
} else {
	$res = null;
}

echo json_encode($res);
exit();
