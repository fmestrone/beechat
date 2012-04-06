<?php

function ejabberd_xmlrpc_send($request) {
	$context = stream_context_create(array('http' => array(
	    'method' => "POST",
	    'header' => "User-Agent: XMLRPC::Client mod_xmlrpc\r\n" .
			"Content-Type: text/xml\r\n" .
			"Content-Length: ".strlen($request),
	    'content' => $request
	)));

	$file = file_get_contents("http://".get_plugin_setting("xmlrpcip", "beechat").":4560/RPC2", false, $context);

	$response = xmlrpc_decode($file);
	if (is_array($response) && xmlrpc_is_fault($response)) {
	    trigger_error("xmlrpc: $response[faultString] ($response[faultCode])");
	} else {
	//    print_r($response);
	}
}

function ejabberd_xmlrpc_command($command, $params) {
	//error_log("send xmlrpc: ".$command);
	$request = xmlrpc_encode_request($command, $params, (array('encoding' => 'utf-8')));
	return ejabberd_xmlrpc_send($request);
}

function xmpp_escape($name) {
	// http://xmpp.org/extensions/xep-0106.html#escaping
	$name = str_replace(' ', '\\20', $name);
	$name = str_replace('"', '\\22', $name);
	$name = str_replace('&', '\\26', $name);
	$name = str_replace("'", '\\27', $name);
	$name = str_replace('/', '\\2f', $name);
	$name = str_replace(';', '\\3a', $name);
	$name = str_replace('<', '\\3c', $name);
	$name = str_replace('>', '\\3e', $name);
	$name = str_replace('@', '\\40', $name);
	$name = str_replace('\\', '\\5c', $name);
	return $name;
}

function ejabberd_create_group($group) {
	// create room
	ejabberd_xmlrpc_command('create_muc_room', array(
		"name" => elgg_get_friendly_title($group->name),
		"service" => get_plugin_setting("groupdomain", "beechat"),
		"server" => get_plugin_setting("domain", "beechat"),
	));

	// persistency

	$room = new EjabberdMucRoom($group);
	$room->setOption("persistent", true);
	$room->setOption("title", $group->name);
	// open to public?
	if ($group->isPublicMembership()) {
		$room->setOption("members_only", false);
	} else
		$room->setOption("members_only", true);

	if ($group->access_id === ACCESS_PUBLIC) {
		$room->setOption("public_list", true);
		$room->setOption("public", true);
	} else {
		$room->setOption("public_list", false);
		$room->setOption("public", false);
	}
	$members = $group->getMembers(0);
	foreach($members as $member) {
		$room->addMember($member);
	}
	$room->addMember(get_entity($group->owner_guid));
}

function ejabberd_destroy_group($group) {
	ejabberd_xmlrpc_command('delete_muc_room', array(
		"name" => elgg_get_friendly_title($group->name),
		"service" => get_plugin_setting("groupdomain", "beechat"),
		"server" => get_plugin_setting("domain", "beechat")
	));
}

function ejabberd_getjid($user, $do_external=false) {
	if ($user->foreign || ($do_external && $user->alias && get_plugin_usersetting("usealias", $user->guid,"openid_client"))) {
		if ($user->foreign) {
			$webid = $user->webid;
		} else {
			$webid = $user->alias;
		}
		if (strpos($webid, 'http') === 0) {
			// http or https addresses
			$hostparts = parse_url($webid);
                        $urlparts = explode('/', $webid);
			$host = $hostparts['host'];
			$username = $urlparts[count($urlparts)-1];
		} else {
			if (strpos($webid, ':') > 0) {
				$webidparts = explode(':', $webid);
				$hostparts = explode('@',$webidparts[1]);
			} else {
				$hostparts = explode('@',$webid);
			}
			$username = $hostparts[0];
			$host = $hostparts[1];
		}
	} else {
		$username = $user->username;
		$host = get_plugin_setting("domain", "beechat");
	}
	return xmpp_escape($username) . '@' . $host;
}

function ejabberd_friend_command($user, $friend, $command, $is_out) { // $user adds $friend

	error_log(" * ".$friend->username."->".ejabberd_getjid($user)." ".$command);
	if ($friend->foreign) {
         	error_log(" * beechat: friend is foreign!");       
		return;
	}
	$param = array("user" => elgg_get_friendly_title($friend->username),
			"server" => get_plugin_setting("domain", "beechat"),
	 		"from" => ejabberd_getjid($user),
			"subs" => $command);
	if ($is_out) {
		error_log("out");
		ejabberd_xmlrpc_command('send_roster_request_out', $param);
	} else {
		$param['reason'] = 'unknown';
		ejabberd_xmlrpc_command('send_roster_request_in', $param);
	}
}


function ejabberd_friend_request($user, $friend) { // $user adds $friend
	error_log('ejabberd_friend_request');
	ejabberd_friend_command($friend, $user, 'subscribe', true); // out:$user : $friend
	error_log('ejabberd_friend_requested');
}

function ejabberd_friend_accept($user, $friend) { // $user adds $friend

	error_log('ejabberd_friend_accept');
	ejabberd_friend_command($friend, $user, 'subscribed', true);
	// following might be needed to have symmetry (and important for remote)
	if ($friend->foreign) {
		// following is needed for xmpp nodes
		ejabberd_friend_command($friend, $user, 'subscribe', true);
	}
	// ejabberd_friend_command($friend, $user, 'subscribed', false);
	// following can't be faked
	if (!$friend->foreign) {
		ejabberd_friend_command($user, $friend, 'subscribed', true);
		error_log('ejabberd_friend_accepted');
	}
}

function ejabberd_friend_deny($user, $friend) { // $user adds $friend
	error_log('ejabberd_friedeny');
	ejabberd_friend_command($friend, $user, 'unsubscribed', true);
}

function ejabberd_friend_remove($user, $friend) { // $user adds $friend 
	error_log('ejabberd_friend_remove');
	if ($friend->foreign) {
		ejabberd_friend_command($friend, $user, 'unsubscribed', true);
		ejabberd_friend_command($friend, $user, 'unsubscribed', false);
	} else {
		ejabberd_friend_command($friend, $user, 'unsubscribed', false);
		ejabberd_friend_command($user, $friend, 'unsubscribed', false);
		error_log('ejabberd_friend_removed');
	}
}
