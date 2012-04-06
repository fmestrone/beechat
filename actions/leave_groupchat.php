<?php

$user = get_loggedin_user();
$group = get_entity(get_input('group_guid'));

if ($user && $group) {
	if (check_entity_relationship($user->guid, 'groupchat', $group->guid))
		remove_entity_relationship($user->guid, 'groupchat', $group->guid);
}
error_log("leave ok");
echo "OK";
?>
