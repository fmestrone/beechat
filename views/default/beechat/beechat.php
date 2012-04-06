<?php

if (elgg_is_logged_in()) {
	
	$home = elgg_echo('beechat:icons:home');
	$button = elgg_echo('beechat:contacts:button');
	$minimize = elgg_echo('beechat:box:minimize');
	
	$available = elgg_echo('beechat:availability:available');
	$dnd = elgg_echo('beechat:availability:dnd');
	$away = elgg_echo('beechat:availability:away');
	$xa = elgg_echo('beechat:availability:xa');
	$offline = elgg_echo('beechat:availability:offline');

?>	 
<div id="beechat">
  <div id="beechat_left">
    <div class="tooltip tooltipchat">
      <h3><?php echo $home; ?></h3>
    </div>
  </div>
  <div id="beechat_center">
    <span id="beechat_center_prev" class="prev"></span>
    <div id="beechat_scrollboxes"><ul></ul></div>
    <span id="beechat_center_next" class="next"></span>
  </div>
  <div id="beechat_right">
    <span id="beechat_contacts_button" class="offline">
      <?php echo $button; ?>
    </span>
  </div>
  <div id="beechat_contacts">
    <div id="beechat_contacts_top">
      <span class="beechat_label"><?php echo $button; ?></span>
      <div id="beechat_contacts_controls">
	<span id="beechat_contacts_control_minimize" class="beechat_control" title="<?php echo $minimize; ?>">_</span>
      </div>
      <br clear="all" />
    </div>
    <div id="beechat_availability_switcher">
      <span id="beechat_current_availability"></span>
      <span class="beechat_availability_switcher_control_down" id="beechat_availability_switcher_control"></span>
    </div>
    <div id="beechat_contacts_content">
      <ul id="beechat_contacts_list"></ul>
      <ul id="beechat_availability_switcher_list">
	<li class="beechat_left_availability_chat"><?php echo $available ?></li>
	<li class="beechat_left_availability_dnd"><?php echo $dnd; ?></li>
	<li class="beechat_left_availability_away"><?php echo $away; ?></li>
	<li class="beechat_left_availability_xa"><?php echo $xa ?></li>
	<li class="beechat_left_availability_offline"><?php echo $offline ?></li>
      </ul>
    </div>
    <div id="beechat_contacts_bottom">
      <span id="beechat_contacts_bottom_bar"></span>
    </div>
  </div>
  <div id="beechat_chatboxes"></div>
</div>
<!-- SOUNDS -->
<!--
<embed src="<?php echo $vars['config']->staticurl; ?>mod/beechat/sounds/newmessage.wav" autostart=false width=0 height=0
       id="beechat_sounds_new_message"
       enablejavascript="true" />
-->

<script>
	$(function () {
		init_beechat("<?php echo time(); ?>","<?php echo generate_action_token($ts); ?>");
	});
</script>       

<?php
}
?>
