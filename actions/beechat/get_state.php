<?php
	 
header('Content-type: application/json');
gatekeeper();

global $SESSION;

if ($SESSION->offsetExists('beechat_state')) {
	echo $SESSION->offsetGet('beechat_state');
}

exit();
