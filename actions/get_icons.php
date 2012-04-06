<?php
	/**
	 * Beechat
	 * 
	 * @package beechat
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Beechannels <contact@beechannels.com>
	 * @copyright Beechannels 2007-2010
	 * @link http://beechannels.com/
	 */

	header('Content-type: application/json');
	gatekeeper();
	global $CONFIG;

	if (!empty($_POST['beechat_roster_items_usernames']))
	{
		$rosterItemsUsernames = explode(',', $_POST['beechat_roster_items_usernames']);

		$userFriendsEntities = $_SESSION['user']->getFriends('', 0, 0);
		
		$res = array();
		foreach ($rosterItemsUsernames as $value)
		{
			$found = false;
			$splitjid = explode('@', $value);
			$jid_name = $splitjid[0];
			$jid_host = $splitjid[1];
			foreach ($userFriendsEntities as $friend)
			{
				if ((strtolower($friend->username) == strtolower($jid_name) && $jid_host == get_plugin_setting("domain", "beechat")))
				{
					$res[$value] = array('small' => $friend->getIcon('small'), 'tiny' => $friend->getIcon('tiny'));
					$found = true;
					break;
				}
			}
			if (!$found) {
				$base = $CONFIG->wwwroot."mod/profile/graphics/default";
				$res[$value] = array('small' => $base."small.gif", 'tiny' => $base."tiny.gif");
			}
		}
		echo json_encode($res);
	}
	else
		echo json_encode(null);

	exit();

?>
