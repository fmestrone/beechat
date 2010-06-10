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
	
	if (!empty($_POST['beechat_roster_items_usernames']))
	{
		$iconSize = 'small';
		$rosterItemsUsernames = explode(',', $_POST['beechat_roster_items_usernames']);
		$userFriendsEntities = $_SESSION['user']->getFriends('', count($rosterItemsUsernames), 0);
		
		$res = array();
		foreach ($rosterItemsUsernames as $value)
		{
			foreach ($userFriendsEntities as $friend)
			{
				if (strtolower($friend->username) == strtolower($value))
				{
					$status = get_entities_from_metadata("state", "current", "object", "status", $friend->get('guid'));
					$res[$value] = ($status != false) ? $status[0]->description : '';
					break;
				}
			}
		}
		echo json_encode($res);
	}
	else
		echo json_encode(null);

	exit();

?>
