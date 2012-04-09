<?php

header('Content-type: application/json');
gatekeeper();

if (!empty($_POST['beechat_roster_items_usernames'])) {
	
	$rosterItemsUsernames = explode(',', get_input('beechat_roster_items_usernames'));
	$userFriendsEntities = elgg_get_logged_in_user_entity->getFriends('', 0, 0);
	
	$res = array();
	foreach ($rosterItemsUsernames as $value) {
		$found = false;
		$splitjid = explode('@', $value);
		$jid_name = $splitjid[0];
		$jid_host = $splitjid[1];
		foreach ($userFriendsEntities as $friend) {
			if ((strtolower($friend->username) == strtolower($jid_name) && $jid_host == elgg_get_plugin_setting("domain", "beechat"))) {
				$res[$value] = array('small' => $friend->getIcon('small'), 'tiny' => $friend->getIcon('tiny'));
				$found = true;
				break;
			}
		}
		if (!$found) {
			$base = elgg_get_site_url() . "mod/profile/graphics/default";
			$res[$value] = array('small' => $base."small.gif", 'tiny' => $base."tiny.gif");
		}
	}
} else {
	$res = null;
}

echo json_encode($res);
exit();
