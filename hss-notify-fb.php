<?php
/*
Plugin Name: hss-notify-fb
Plugin URI: http://harbhag.wordpress.com
Description: Updates facebook status about new posts
Version: 0.1
Author: Harbhag Singh Sohal
Author URI: http://202.164.53.116/~harbhag
*/
require_once "facebook.php";
require_once(includes_url().'/capabilities.php');

function hss_update_fb() {
	global $current_user;
	wp_get_current_user();
	$post_link = get_permalink($post_ID);
	function make_post($url, $args) {
		$ch = curl_init();
    $timeout = 5;
    if (!empty($args)) {
			foreach ($args as $key => $value)
				$arguments .= $key . '=' . stripslashes(urldecode($value)) . '&';
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $arguments);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
  }
	$facebook = new Facebook(array(
  'appId'  => 'APP ID',
  'secret' => 'APP SECRET',
  'cookie' => true,
	));
  $loginUrl = $facebook->getLoginUrl(array(
  'canvas' => 1,
  'fbconnect' => 0,
  'req_perms' => 'publish_stream,offline_access',
  'next' => 'CANVAS URL',
  'cancel_url' => 'REDIRECT URL',
	));
	$result = mysql_fetch_assoc(mysql_query("SELECT FIELD FROM TABLE_NAME WHERE username='".$current_user->user_login."'"));
	$data = array(
  'access_token' => $result['access_token'],
  'message' => $post_link
  );
	$return = json_decode(make_post('https://graph.facebook.com/' . $user->id . '/feed', $data));
}

	
	
	global $current_user;
	wp_get_current_user();
	$facebook = new Facebook(array(
		'appId'  => 'APP ID',
		'secret' => 'APP SECRET',
		'cookie' => true,
	));
	$loginUrl = $facebook->getLoginUrl(array(
		'canvas' => 1,
		'fbconnect' => 0,
		'req_perms' => 'publish_stream,offline_access',
		'next' => 'CANVAS URL',
		'cancel_url' => 'REDIRECT URL',
	));
	$session = $facebook->getSession();
	$access_token = $session['access_token'];
	$session = $facebook->getSession();
	if ($session) {
		$result = mysql_num_rows((mysql_query("SELECT FIELD FROM TABLE_NAME WHERE username='".$current_user->user_login."'")));
		if($result==0) {
			mysql_query("INSERT INTO TABLE_NAME (username,access_token) VALUE ('".$current_user->user_login."','".$access_token."')") or die(mysql_error());
		}
	}





function add_plugin_menu()
{
  add_options_page('hss-notify-fb Settings', 'hss-notify-fb', 8, __FILE__, 'add_settings_page');
}
function add_settings_page()
{
	
  $facebook = new Facebook(array(
  'appId'  => 'APP ID',
  'secret' => 'APP SECRET',
  'cookie' => true,
));
     $loginUrl = $facebook->getLoginUrl(array(
    'canvas' => 1,
    'fbconnect' => 0,
    'req_perms' => 'publish_stream,offline_access',
    'next' => 'CANVAS URL',
    'cancel_url' => 'REDIRECT URL',
));
        $session = $facebook->getSession();
        
        $access_token = $session['access_token'];

        $data = array(
            'access_token' => $access_token,
            'message' => 'MSG'
        );

        $session = $facebook->getSession();
if ($session) {
            
             echo $access_token;
            //$return = json_decode(make_post('https://graph.facebook.com/' . $user->id . '/feed', $data));
						mysql_query("INSERT INTO TABLE_NAME (access_token) VALUE ('".$access_token."')") or die(mysql_error());
						echo "Access Granted";
					}
            
             else {
        ?>
            <div id="fb-root"></div>
            
            <script src="http://connect.facebook.net/en_US/all.js"></script>

            <script>
					
                //init function to start the facebook JS API
                FB.init({
                    appId:'APP ID', cookie:true,
                    status:true, xfbml:true
                });
                //Login function, shows the login popup and redirects to the same page
                function fbLogin () {
                    FB.login(function(response) {
                        if (response.session) {
                            if (response.perms) {
                                window.location = "URL"
                            } else {
                                alert('No Permission Granted !!');
                            }
                        } else {
                            // user is not logged in
                        }
                        // we ask for publish permissions
                    }, {perms:'publish_stream,offline_access'});
                }

            </script>
            <!-- simple HTML login button -->
            <a onclick="fbLogin();"><img src="login-button.jpg" /></a>
        <?php
        }

}
add_action('publish_post','hss_update_fb');
add_action('admin_menu', 'add_plugin_menu');
?>
