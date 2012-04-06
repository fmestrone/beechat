<?
require_once(dirname(__FILE__) . "/lib.php");

admin_gatekeeper();

$groups = elgg_get_entities(array('types'=>'group','limit'=>0));
elgg_set_ignore_access(true);
foreach($groups as $group) {
	echo "migrating " . $group->name . "<br/>";
	ejabberd_create_group($group);
}
echo "done!";
elgg_set_ignore_access(false);

?>
