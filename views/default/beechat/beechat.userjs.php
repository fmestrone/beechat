<?php
if (elgg_is_logged_in()) {
	$user = elgg_get_logged_in_user_entity();
	$domain = elgg_get_plugin_setting("groupdomain", "beechat");
	$chatrooms = elgg_get_entities_from_relationship(array(
		'relationship' => 'groupchat',
		'relationship_guid' => $user->guid,
		'inverse_relationship' => false,
		'limit' => 0,
	));
	$user_rooms = array();
	foreach ($chatrooms as $chatroom) {
		$user_rooms[] = array(elgg_get_friendly_title($chatroom->name)."@".$domain, $chatroom->guid);
	}
	$user_rooms = json_encode($user_rooms);
	
	echo <<<__HTML
<script type="text/javascript">
g_user_rooms = $user_rooms;
</script>
__HTML;

}
