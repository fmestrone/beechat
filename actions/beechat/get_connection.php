<?php
	 
header('Content-type: application/json');
gatekeeper();

global $SESSION;

if ($SESSION->offsetExists('beechat_conn')) {
	echo $SESSION->offsetGet('beechat_conn');
}
exit();
