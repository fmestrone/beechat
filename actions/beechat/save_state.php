<?php

gatekeeper();
global $SESSION;

if (!empty(get_input('beechat_state'))) {
	$SESSION->offsetSet('beechat_state', get_input('beechat_state'));
} elseif (!empty(get_input('beechat_conn'))) {
	$SESSION->offsetSet('beechat_conn', get_input('beechat_conn'));
}
exit();
