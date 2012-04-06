<?php
/**
 * Elgg XMPP Chat Plugin
 *
 * @package ElggBeechat
 */

elgg_register_event_handler('init', 'system', 'beechat_init');

/**
 * Initialize the chat plugin.
 *
 */
function beechat_init() {
	
	// register a library of helper functions
	elgg_register_library('elgg:beechat', elgg_get_plugins_path() . 'beechat/lib/beechat.php');
	
	if (elgg_get_plugin_setting("groupdomain", "beechat")) {
		elgg_register_event_handler('create', 'group', 'beechat_create_group');
		elgg_register_event_handler('delete', 'group', 'beechat_delete_group');
	}
	elgg_register_event_handler('create', 'member', 'beechat_member_add');
	elgg_register_event_handler('delete', 'relationship', 'beechat_member_delete');

	// elgg_register_event_handler('create', 'friend', 'beechat_xmpp_add_friend_r');
	// elgg_register_event_handler('delete', 'relationship', 'beechat_xmpp_add_friend_r');

	// Register some actions
	$action_base = elgg_get_plugins_path() . 'beechat/actions/beechat';
	elgg_register_action('beechat/enable', "$action_base/enable.php");
	elgg_register_action('beechat/disable', "$action_base/disable.php");
	elgg_register_action('beechat/join_groupchat', "$action_base/join_groupchat.php");
	elgg_register_action('beechat/leave_groupchat', "$action_base/leave_groupchat.php");
	elgg_register_action('beechat/get_statuses', "$action_base/get_statuses.php");
	elgg_register_action('beechat/get_icons', "$action_base/get_icons.php");
	elgg_register_action('beechat/get_details', "$action_base/get_details.php");
	elgg_register_action('beechat/get_connection', "$action_base/get_connection.php");
	elgg_register_action('beechat/get_state', "$action_base/get_state.php");
	elgg_register_action('beechat/save_state', "$action_base/save_state.php");

	elgg_register_event_handler('create', 'friendrequest', 'beechat_xmpp_add_friendx');
	//elgg_register_plugin_hook_handler('action', 'friends/add', 'beechat_xmpp_add_friend', 1000);
	elgg_register_plugin_hook_handler('river_update', 'river_update', 'beechat_xmpp_approve_friendx');
	elgg_register_plugin_hook_handler('river_update_foreign', 'river_update', 'beechat_xmpp_approve_friendx');
	//elgg_register_plugin_hook_handler('action', 'friendrequest/approve', 'beechat_xmpp_approve_friend', 1000);
	elgg_register_plugin_hook_handler('action', 'friendrequest/decline', 'beechat_xmpp_decline_friend', 1000);
	elgg_register_plugin_hook_handler('action', 'friends/remove', 'beechat_xmpp_remove_friend', 1000);

	$js_libs = array (
		'js/json2.js',
		'js/jquery.cookie.min.js',
		'js/jquery.scrollTo-min.js',
		'js/jquery.serialScroll-min.js',
		'js/b64.js',
		'js/sha1.js',
		'js/md5.js',
		'js/strophe.min.js',
		'js/strophe.muc.js',
		'js/jquery.tools.min.js',
		'beechat/beechat.js',
	);
	foreach ($js_libs as $lib) {
		elgg_extend_view('js/elgg', $lib);
	}
	
	elgg_extend_view('page/elements/head', 'beechat/beechat.userjs');
	elgg_extend_view('css', 'beechat/screen.css');
	elgg_extend_view('footer/analytics', 'beechat/beechat');

	elgg_set_config('chatsettings', array(
		'domain' => elgg_get_plugin_setting("domain", "beechat"),
		'groupdomain' => elgg_get_plugin_setting("groupdomain", "beechat"),
		'dbname' => elgg_get_plugin_setting("dbname", "beechat"),
		'dbhost' => elgg_get_plugin_setting("dbhost", "beechat"),
		'dbuser' => elgg_get_plugin_setting("dbuser", "beechat"),
		'dbpassword' => elgg_get_plugin_setting("dbpassword", "beechat"),
	));

	register_notification_handler('xmpp', 'beechat_notifications');
	// register_plugin_hook('notify:entity:message','object','beechat_notifications_msg');
}


function beechat_create_group($event, $object_type, $object){
	elgg_load_library('elgg:beechat');
	ejabberd_create_group($object);
}

function beechat_delete_group($event, $object_type, $object) {
	elgg_load_library('elgg:beechat');
	ejabberd_destroy_group($object);
}

function beechat_member_add($event, $object_type, $object) {
	if ($object->relationship == "member") {
		$user = get_entity($object->guid_one);
		$group = get_entity($object->guid_two);
		$room = new EjabberdMucRoom($group);
		$room->addMember($user);
	}
}

function beechat_member_delete($event, $object_type, $object) {
	if ($object->relationship == "member") {
		$user = get_entity($object->guid_one);
		$group = get_entity($object->guid_two);
		$room = new EjabberdMucRoom($group);
		$room->setAffiliation($user, "none");
	}
}

	
function beechat_notifications($from, $to, $subject, $topic, $params = array()) {
	elgg_load_library('elgg:beechat');
	ejabberd_send_chat($to, "<div>".$topic."</div>");
}


function beechat_friendly_title($title) {
	// need this because otherwise seems elgg
	// gets in some problem trying to call the view
	//$title = iconv('UTF-8', 'ASCII//TRANSLIT', $title);
	$title = preg_replace("/[^\w ]/","",$title);
	$title = str_replace(" ","-",$title);
	$title = str_replace("--","-",$title);
	$title = trim($title);
	$title = strtolower($title);
	return $title;
}


function beechat_xmpp_approve_friendx($hook, $entity_type, $returnvalue, $params)
{
	//ejabberd_send_chat($to, "<div>".$topic."</div>");
	$action_type = $params['action_type'];
	$object_guid = $params['object_guid'];
	if ($action_type == 'friend' && ($hook == 'river_update'|| $hook == 'river_update_foreign')) {
		set_input('guid', $object_guid);
		beechat_xmpp_approve_friend($hook, $entity_type, $returnvalue, $params);
	}
	return $return_value;

}
function beechat_xmpp_approve_friend($hook, $entity_type, $returnvalue, $params) {
	global $SESSION;
	if (!$friend = get_entity(elgg_get_input('guid', 0))){
		return false;
	}
	elgg_load_library('elgg:beechat');
	ejabberd_friend_accept(elgg_get_logged_in_user_entity(), $friend);
	return $returnvalue;
}

function beechat_xmpp_decline_friend($hook, $entity_type, $returnvalue, $params) {
	// in case later we want better integration with xmpp ;)
	GLOBAL $SESSION;
	if (!$friend = get_entity(elgg_get_input('guid', 0))){
		return false;
	}
	elgg_load_library('elgg:beechat');
	ejabberd_friend_deny(elgg_get_logged_in_user_entity(), $friend);
	return $returnvalue;
}
function beechat_xmpp_add_friendx($event, $object_type, $obj) {
	if ($event == "create" && $object_type == 'friendrequest') {
		if ($obj->relationship == 'friendrequest') {
			set_input('friend', $obj->guid_two);
			beechat_xmpp_add_friend('', 'relationship', true, $params);
		}
	}
}

function beechat_xmpp_add_friend($hook, $entity_type, $returnvalue, $params) {
	GLOBAL $SESSION;
	$friend_guid = elgg_get_input('friend', 0);
	if (!$friend_guid || !$friend = get_entity($friend_guid)) {
		return false;
	}
	elgg_load_library('elgg:beechat');
	// for now.. do this in approve
	ejabberd_friend_request(get_loggedin_user(), $friend);
	//return _beechat_xmpp_add_friend($SESSION->offsetGet('user'), $friend);
	return $returnvalue;
}
function beechat_xmpp_remove_friend($hook, $entity_type, $returnvalue, $params) {
	GLOBAL $SESSION;
	if (!$friend = get_entity(get_input('friend', 0))) {
		return (false);
	}
	elgg_load_library('elgg:beechat');
	ejabberd_friend_remove(elgg_get_logged_in_user_entity(), $friend);
	//_beechat_xmpp_remove_friend($SESSION->offsetGet('user'), $friend);
	return $returnvalue;
}

function ejabberd_send_chat($user, $body) { // $user adds $friend
	elgg_load_library('elgg:beechat');
	//xmlrpc_set_type(&$body, "base64"); 
	ejabberd_xmlrpc_command('send_html_message', array(
		"body" => $body,
		"from" => 'notify@'.get_plugin_setting("domain", "beechat").'/net',
		"to" => ejabberd_getjid($user, true),
	));
}

function _beechat_xmpp_add_friend($curr_user, $friend)
{
	GLOBAL $CONFIG;
	
	$jabber_domain = $CONFIG->chatsettings['domain'];
	$dbname = $CONFIG->chatsettings['dbname'];
	$dbhost = $CONFIG->chatsettings['dbhost'];
	$dsn_ejabberd = "mysql:dbname={$dbname};host={$dbhost}";

	$user = $CONFIG->chatsettings['dbuser'];
	$password = $CONFIG->chatsettings['dbpassword'];

	try {
		$dbh_ejabberd = new PDO($dsn_ejabberd, $user, $password);
		$dbh_ejabberd->beginTransaction();
		
		$sql = 'INSERT INTO rosterusers (username, jid, nick, subscription, ask, server, type) VALUES (?, ?, ?, ?, ?, ?, ?);';
		$sth_ejabberd = $dbh_ejabberd->prepare($sql);
		
		$username = $curr_user->username;
		$jid = $friend->username . '@' . $jabber_domain;
		$nick = $friend->name;
		$subscription = 'B';
		$ask = 'N';
		$server = 'N';
		$type = 'item';
		
		$sth_ejabberd->execute(array($username, $jid, $nick, $subscription, $ask, $server, $type));
		
		$sql = 'INSERT INTO rosterusers (username, jid, nick, subscription, ask, server, type) VALUES (?, ?, ?, ?, ?, ?, ?);';
		$sth_ejabberd = $dbh_ejabberd->prepare($sql);
		
		$username = $friend->username;
		$jid = $curr_user->username . '@' . $jabber_domain;
		$nick = $curr_user->name;
		
		$sth_ejabberd->execute(array($username, $jid, $nick, $subscription, $ask, $server, $type));
		
		$dbh_ejabberd->commit();
		$dbh_ejabberd = null;
		
	} catch (PDOException $e) {
		$dbh_ejabberd->rollBack();
		return (false);
	}
	
	return $return_value;
}



function _beechat_xmpp_remove_friend($curr_user, $friend) {
	GLOBAL $SESSION;
	GLOBAL $CONFIG;
	
	$jabber_domain = $CONFIG->chatsettings['domain'];
	$dbname = $CONFIG->chatsettings['dbname'];
	$dbhost = $CONFIG->chatsettings['dbhost'];
	$dsn_ejabberd = "mysql:dbname={$dbname};host={$dbhost}";
	
	$user = $CONFIG->chatsettings['dbuser'];
	$password = $CONFIG->chatsettings['dbpassword'];

	try {
		$dbh_ejabberd = new PDO($dsn_ejabberd, $user, $password);
		$dbh_ejabberd->beginTransaction();
		
		$sql = 'DELETE FROM rosterusers WHERE username = ? AND jid = ?;';
		$sth_ejabberd = $dbh_ejabberd->prepare($sql);
		
		$username = $curr_user->username;
		$jid = $friend->username . '@' . $jabber_domain;
		
		$sth_ejabberd->execute(array($username, $jid));
		
		$sql = 'DELETE FROM rosterusers WHERE username = ? AND jid = ?;';
		$sth_ejabberd = $dbh_ejabberd->prepare($sql);
		
		$username = $friend->username;
		$jid = $curr_user->username . '@' . $jabber_domain;
		
		$sth_ejabberd->execute(array($username, $jid));
		
		$dbh_ejabberd->commit();
		$dbh_ejabberd = null;
		
	} catch (PDOException $e) {
		$dbh_ejabberd->rollBack();
		return (false);
	}

	return $return_value;
}

