<?php
/*
** SQL setup
*/
$the_db = mysql_connect('HOST','USER','PASS');
mysql_select_db('DATABASE', $the_db);

/*
** OAuth setup
*/
require 'tmhOAuth.php';
require 'tmhUtilities.php';
$tmhOAuth = new tmhOAuth(array(
	'consumer_key'    => 'TWITTER_CONSUMER_KEY',
	'consumer_secret' => 'TWITTER_CONSUMER_SECRET',
));
$tmhUtilities = new tmhUtilities();
$here = $tmhUtilities->php_self();

$debug = "DEBUG:<br />";

function outputError($tmhOAuth) {
	$e = json_decode($tmhOAuth->response['response']);
	$debug .= $e->error . "<br />";
	//echo 'Error: ' . . PHP_EOL;
	/*tmhUtilities::pr($tmhOAuth);*/
}

// reset request?
if ( isset($_REQUEST['wipe'])) {
	session_destroy();
	header("Location: {$here}");
} elseif ( isset($_REQUEST['forget'])) {
	if ( isset($_SESSION['id'])) {
		$reg = mysql_query("SELECT * FROM tweekpic_jobs WHERE user_id='{$_SESSION['id']}'");
		if ($reg)
		{
			if (mysql_num_rows($reg) != 0)
			{
				while ($row = mysql_fetch_assoc($reg))
				{
    				unlink('./images/' . $row['pic_path']);
				}
			}
			mysql_query("DELETE FROM tweekpic_jobs WHERE user_id='{$_SESSION['id']}'");
		}
		mysql_query("DELETE FROM tweekpic_users WHERE user_id='{$_SESSION['id']}'");
		session_destroy();
		header("Location: {$here}");
	}

} elseif ( isset($_SESSION['access_token']) ) {
	$tmhOAuth->config['user_token']  = $_SESSION['access_token']['oauth_token'];
	$tmhOAuth->config['user_secret'] = $_SESSION['access_token']['oauth_token_secret'];
 	$code = $tmhOAuth->request('GET', $tmhOAuth->url('1/account/verify_credentials'));
	if ($code == 200) {
		$resp = json_decode($tmhOAuth->response['response']);
		$_SESSION['screen_name'] = htmlentities($resp->screen_name);
		$_SESSION['id'] = mysql_real_escape_string($resp->id, $the_db);
		$_SESSION['avatar'] = $resp->profile_image_url;
		$_SESSION['logged'] = 'YES';
		$_SESSION['oauth_token'] = mysql_real_escape_string($_SESSION['access_token']['oauth_token']);
		$_SESSION['oauth_secret'] = mysql_real_escape_string($_SESSION['access_token']['oauth_token_secret']);
		$reg = mysql_query("SELECT user_id FROM tweekpic_users WHERE user_id='{$_SESSION['id']}'");
		if (mysql_num_rows($reg) == 0) {
			$reg = mysql_query("INSERT INTO tweekpic_users (user_id, gmt, oauth_token, oauth_secret)
								VALUES ('{$_SESSION['id']}', '0', '{$_SESSION['oauth_token']}','{$_SESSION['oauth_secret']}')");
			if (!$reg) { $debug .= " - Couldn't insert user values in database<br />"; }
		} else {
			$debug .= " - User was registered<br />";
			$reg = mysql_query("UPDATE tweekpic_users SET oauth_token='{$_SESSION['oauth_token']}', oauth_secret='{$_SESSION['oauth_secret']}' WHERE user_id='{$_SESSION['id']}'");
			if  (!$reg) { $debug .= " - Couldn't update user values in database<br />"; }
			else { $debug .= " - User values successfully updated.<br />"; }
		}
	} else {
		outputError($tmhOAuth);
	}
} elseif (isset($_REQUEST['oauth_verifier'])) {
	$tmhOAuth->config['user_token']  = $_SESSION['oauth']['oauth_token'];
	$tmhOAuth->config['user_secret'] = $_SESSION['oauth']['oauth_token_secret'];

	$code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/access_token', ''), array(
		'oauth_verifier' => $_REQUEST['oauth_verifier']
	));
	if ($code == 200) {
		$_SESSION['access_token'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
		unset($_SESSION['oauth']);
		header("Location: {$here}");
	} else {
		outputError($tmhOAuth);
	}
} elseif ( isset($_REQUEST['authenticate']) || isset($_REQUEST['authorize']) ) {
	$callback = isset($_REQUEST['oob']) ? 'oob' : $here;
	$params = array(
		'oauth_callback'     => $callback
	);
	if (isset($_REQUEST['force_write'])) :
		$params['x_auth_access_type'] = 'write';
	elseif (isset($_REQUEST['force_read'])) :
		$params['x_auth_access_type'] = 'read';
	endif;
	$code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/request_token', ''), $params);
	if ($code == 200) {
		$_SESSION['oauth'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
		$method = isset($_REQUEST['authenticate']) ? 'authenticate' : 'authorize';
		$force  = isset($_REQUEST['force']) ? '&force_login=1' : '';
		$authurl = $tmhOAuth->url("oauth/{$method}", '') .  "?oauth_token={$_SESSION['oauth']['oauth_token']}{$force}";
		header("Location: {$authurl}");
	} else {
		outputError($tmhOAuth);
	}
}
?>
