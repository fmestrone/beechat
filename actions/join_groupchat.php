<?php

$user = get_loggedin_user();
$group = get_entity(get_input('group_guid'));

if ($user && $group) {
	if (!check_entity_relationship($user->guid, 'groupchat', $group->guid)) {
		error_log("joinen ok");
		add_entity_relationship($user->guid, 'groupchat', $group->guid);
}
}
echo "OK";
error_log("join ok");

?>
