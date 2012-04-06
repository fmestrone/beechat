<?php

elgg_load_library('elgg:beechat');

class EjabberdMucRoom {
	
	function __construct($group) {
		$this->group = $group;
	}
	
	function setOption($name, $value) {
		$group = $this->group;
		ejabberd_xmlrpc_command('muc_room_change_option', array(
			"name" => elgg_get_friendly_title($group->name),
			"service" => get_plugin_setting("groupdomain", "beechat"),
			"option" => $name,
			"value" => $value
		));
	}
	
	function addMember($member) {
		//"outcast" | "none" | "member" | "admin" | "owner"
		$group = $this->group;
		if ($member->guid === $group->owner_guid) {
			$affiliation = "owner";
		} elseif ($group->canEdit($member->guid)) {
			$affiliation = "admin";
		} else {
			$affiliation = "member";
		}
		$this->setAffiliation($member, $affiliation);
	}

	function setAffiliation($member, $affiliation) {
		$group = $this->group;
		ejabberd_xmlrpc_command('muc_room_set_affiliation', array(
			"name" => elgg_get_friendly_title($group->name),
			"service" => get_plugin_setting("groupdomain", "beechat"),
			"jid" => xmpp_escape($member->username) . '@' . elgg_get_plugin_setting("domain", "beechat"),
			"affiliation" => $affiliation,
		));
		//echo "set affiliation ".$member->username."<br/>";
	}
}
