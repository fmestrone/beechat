<?php
require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

global $CONFIG;

include($CONFIG->pluginspath . 'beechat/lib.php');

$user = get_entity(11092);
echo ejabberd_getjid($user);
echo ejabberd_getjid(get_entity(9236));
echo ejabberd_getjid(get_entity(5166));


?>

