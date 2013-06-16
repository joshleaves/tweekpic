<?
if (!(isset($_GET['pringles']))) {
	header("HTTP/1.0 404 Not Found");
} elseif ($_GET['CALLING_USER'] == "CRON") {
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

	/*
	** DATE
	*/
	$day = strtolower(date('D'));
	$reg = mysql_query("SELECT * FROM tweekpic_jobs WHERE day='{$day}'");
	if ($reg)
	{
		if (mysql_num_rows($reg) != 0) {
			while ($row = mysql_fetch_assoc($reg))
			{
				error_reporting(2);
				$reg_user = mysql_query("SELECT * FROM tweekpic_users WHERE user_id='{$row['user_id']}'");
				if ($reg_user) {
					$tmhOAuth->config['user_token']  = mysql_result($reg_user, 0, 'oauth_token');
					$tmhOAuth->config['user_secret'] = mysql_result($reg_user, 0, 'oauth_secret');
					$filepath = './images/' . $row["pic_path"];
					$type = filetype($filepath);
					$params = array('image' => '@' . $filepath . ';type=' .$type . ';filename=' . $row["pic_path"]);
    				$code = $tmhOAuth->request('POST', $tmhOAuth->url("1/account/update_profile_image"),
						$params,
						true, // use auth
						true  // multipart
					);
					if (($code == 200) && ($row["do_ad"] == 'on')) {
						echo "DOING IT!";
						$code = $tmhOAuth->request('POST', $tmhOAuth->url("1/statuses/update"),
							array('status' => 'My profile picture got updated by #tWeekPic: http://dreamleaves.org/tweekpic/'),
							true);
						echo $tmhOAuth->response['response'];
					}
				}
			}
		} else {
			header("HTTP/1.0 404 Not Found");
		}

	} else {
		header("HTTP/1.0 404 Not Found");
	}
}
?>
