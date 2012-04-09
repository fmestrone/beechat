<?php

$user = elgg_get_logged_in_user_entity();
$group = get_entity(get_input('group_guid'));

if ($user && $group) {
	if (!check_entity_relationship($user->guid, 'groupchat', $group->guid)) {
		add_entity_relationship($user->guid, 'groupchat', $group->guid);
	}
}
