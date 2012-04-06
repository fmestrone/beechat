<?php
/**
 * Beechat plugin settings
 */

// TODO
//$instructions = elgg_echo('beechat:settings:instructions');

$domain_string = elgg_echo('beechat:domain');
$domain_view = elgg_view('input/text', array(
	'name' => 'params[domain]',
	'value' => $vars['entity']->domain,
	'class' => 'elgg-input-thin',
));

$group_domain_string = elgg_echo('beechat:group_domain');
$group_domain_view = elgg_view('input/text', array(
	'name' => 'params[group_domain]',
	'value' => $vars['entity']->group_domain,
	'class' => 'elgg-input-thin',
));

$xmlrpc_ip_string = elgg_echo('beechat:xmlrpc_ip');
$xmlrpc_ip_view = elgg_view('input/text', array(
	'name' => 'params[xmlrpc_ip]',
	'value' => $vars['entity']->xmlrpc_ip,
	'class' => 'elgg-input-thin',
));

$dbname_string = elgg_echo('beechat:dbname');
$dbname_view = elgg_view('input/text', array(
	'name' => 'params[dbname]',
	'value' => $vars['entity']->dbname,
	'class' => 'elgg-input-thin',
));

$dbhost_string = elgg_echo('beechat:dbhost');
$dbhost_view = elgg_view('input/text', array(
	'name' => 'params[dbhost]',
	'value' => $vars['entity']->dbhost,
	'class' => 'elgg-input-thin',
));

$dbuser_string = elgg_echo('beechat:dbuser');
$dbuser_view = elgg_view('input/text', array(
	'name' => 'params[dbuser]',
	'value' => $vars['entity']->dbuser,
	'class' => 'elgg-input-thin',
));

$dbpassword_string = elgg_echo('beechat:dbpassword');
$dbpassword_view = elgg_view('input/password', array(
	'name' => 'params[dbpassword]',
	'value' => $vars['entity']->dbpassword,
	'class' => 'elgg-input-thin',
));

$settings = <<<__HTML
<div class="elgg-content-thin mtm"><p>$instructions</p></div>
<div><label>$domain_string</label><br /> $domain_view</div>
<div><label>$group_domain_string</label><br /> $group_domain_view</div>
<div><label>$xmlrpc_ip_string</label><br /> $xmlrpc_ip_view</div>
<div><label>$dbname_string</label><br /> $dbname_view</div>
<div><label>$dbhost_string</label><br /> $dbhost_view</div>
<div><label>$dbuser_string</label><br /> $dbuser_view</div>
<div><label>$dbpassword_string</label><br /> $dbpassword_view</div>
__HTML;

echo $settings;
