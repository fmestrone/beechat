<?php
	
gatekeeper();
header('Content-type: application/json');

$user = elgg_get_logged_in_user_entity();

echo json_encode(array(
	'username' => $user->username,
	'password' => $user->password
));
exit();
