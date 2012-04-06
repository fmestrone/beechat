<?php

if (elgg_is_logged_in()) {
	elgg_get_logged_in_user_entity()->chatenabled = true;
	system_message(elgg_echo("beechat:enabled"));
}

forward(REFERER);
