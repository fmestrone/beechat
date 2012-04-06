<script type="text/javascript">
BeeChat.Events.Messages = {
	    ConnectionStates: {
		CONNECTING: "<?php echo elgg_echo('beechat:connection:state:connecting'); ?>",
		AUTHENTICATING: "<?php echo elgg_echo('beechat:connection:state:authenticating'); ?>",
		FAILED: "<?php echo elgg_echo('beechat:connection:state:failed'); ?>",
		DISCONNECTING: "<?php echo elgg_echo('beechat:connection:state:disconnecting'); ?>",
		OFFLINE: "<?php echo elgg_echo('beechat:connection:state:offline'); ?>",
		ONLINE: "<?php echo elgg_echo('beechat:connection:state:online'); ?>"
	    }
	}

BeeChat.UI.Resources.Strings = {
	    Availability: {
		AVAILABLE: "<?php echo elgg_echo('beechat:availability:available'); ?>",
		CHAT: "<?php echo elgg_echo('beechat:availability:available'); ?>",
		ONLINE: "<?php echo elgg_echo('beechat:availability:available'); ?>",
		DND: "<?php echo elgg_echo('beechat:availability:dnd'); ?>",
		AWAY: "<?php echo elgg_echo('beechat:availability:away'); ?>",
		XA:"<?php echo elgg_echo('beechat:availability:xa'); ?>",
		OFFLINE: "<?php echo elgg_echo('beechat:availability:offline'); ?>"
	    },

	    Contacts: {
		BUTTON: "<?php echo elgg_echo('beechat:contacts:button'); ?>"
	    },

	    ChatMessages: {
		SELF: "<?php echo $_SESSION['user']->name; ?>",
		COMPOSING: "<?php echo elgg_echo('beechat:chat:composing'); ?>"
	    },

	    Box: {
		MINIMIZE: "<?php echo elgg_echo('beechat:box:minimize'); ?>",
		CLOSE: "<?php echo elgg_echo('beechat:box:close'); ?>",
		SHOWHIDE: "<?php echo elgg_echo('beechat:box:showhide'); ?>"
	    }
	}
g_user_rooms = new Array();
<?php
if (isloggedin()) {
	$user = get_loggedin_user();
	$chatrooms = elgg_get_entities_from_relationship(array('relationship' => 'groupchat',
								'relationship_guid' => $user->guid,
								'inverse_relationship' => false,
								'limit' => 0));
	if (!empty($chatrooms)) {
	  foreach($chatrooms as $chatroom) {
	    echo "g_user_rooms.push(['".beechat_friendly_title($chatroom->name)."@".get_plugin_setting("groupdomain", "beechat")."', '".$chatroom->guid."']);";
	  }
	}
}
?>
</script>
