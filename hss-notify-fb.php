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
	$result = mysql_fetch_assoc(mysql_query("SELECT access_token FROM fb_access_tokens WHERE username='".$current_user->user_login."'"));
	$data = array(
  'access_token' => $result['access_token'],
  'message' => $post_link
  );
	$return = json_decode(make_post('https://graph.facebook.com/' . $user->id . '/feed', $data));
}

	
	
	global $current_user;
	wp_get_current_user();
	$facebook = new Facebook(array(
		'appId'  => '156232754448221',
		'secret' => '20df61fed410ebe21fd1e187386cc893',
		'cookie' => true,
	));
	$session = $facebook->getSession();
	$access_token = $session['access_token'];
	$session = $facebook->getSession();
	if ($session) {
		$result = mysql_num_rows((mysql_query("SELECT access_token FROM fb_access_tokens WHERE username='".$current_user->user_login."'")));
		if($result==0) {
			mysql_query("INSERT INTO fb_access_tokens (username,access_token) VALUE ('".$current_user->user_login."','".$access_token."')") or die(mysql_error());
		}
	}





function add_plugin_menu()
{
  add_options_page('hss-notify-fb Settings', 'hss-notify-fb', 8, __FILE__, 'add_settings_page');
}
function add_settings_page()
{
	
  $facebook = new Facebook(array(
  'appId'  => '156232754448221',
  'secret' => '20df61fed410ebe21fd1e187386cc893',
  'cookie' => true,
));
?>
            <div id="fb-root"></div>
            
            <script src="http://connect.facebook.net/en_US/all.js"></script>

            <script>
					
               
                FB.init({
                    appId:'156232754448221', cookie:true,
                    status:true, xfbml:true
                });
                
                function fbLogin () {
                    FB.login(function(response) {
                        if (response.session) {
                            if (response.perms) {
                                window.location = "http://psd2press.com/wp-admin/options-general.php?page=hss-notify-fb/hss-notify-fb.php"
                            } else {
                                alert('No Permission Granted !!');
                            }
                        } else {
                            
                        }
                        
                    }, {perms:'publish_stream,offline_access'});
                }

            </script>
            <!-- simple HTML login button -->
            <a onclick="fbLogin();"><img src="http://psd2press.com/wp-content/plugins/hss-notify-fb/login-button.jpg" /></a>
        <?php
        

}
add_action('publish_post','hss_update_fb');
add_action('admin_menu', 'add_plugin_menu');
?>
