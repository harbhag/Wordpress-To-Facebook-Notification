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
	$result = mysql_query("SELECT FIELD FROM TABLE WHERE username='".$current_user->user_login."'");
	$facebook = new Facebook(array(
		'appId'  => 'APP ID',
		'secret' => 'APP SECRET',
		'cookie' => true,
	));
	while($row = mysql_fetch_assoc($result)) {
		
		$data = array(
		'access_token' => $row['access_token'],
		'message' => $post_link
		);
		
		$return = $facebook->api('/me/feed', 'POST', $data);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/me/accounts?access_token='.$row['access_token']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
		$output = curl_exec($ch);
		$output_array = explode('":"',$output);
		for($i=3;$i<=count($output_array);$i+=3) {
			$output_array2 = explode('"',$output_array[$i]);
			$output_array3[] = $output_array2[0];
			unset($output_array2);
		}
		for($j=0;$j<=count($output_array3)-1;$j++) {
			$return2 = $facebook->api('/'.$output_array3[$j].'/feed', 'POST', $data);
		}
		unset($output);
		unset($output_array);
		unset($output_array3);
		curl_close($ch);
	}
	
}

	
	
	global $current_user;
	wp_get_current_user();
	$facebook = new Facebook(array(
		'appId'  => 'APP ID',
		'secret' => 'APP SECRET',
		'cookie' => true,
	));
	$session = $facebook->getSession();
	$access_token = $session['access_token'];
	$session = $facebook->getSession();
	if ($session) {
		$result = mysql_num_rows(mysql_query("SELECT FIELD FROM TABLE WHERE username='".$current_user->user_login."'"));
		if($result!=0) {
			mysql_query("DELETE FROM TABLE WHERE username='".$current_user->user_login."'") or die(mysql_error());
			mysql_query("INSERT INTO TABLE (FIELD,FIELD) VALUE ('".$current_user->user_login."','".$access_token."')") or die(mysql_error());
			
		}
		else{
			mysql_query("INSERT INTO TABLE (FIELD,FIELD) VALUE ('".$current_user->user_login."','".$access_token."')") or die(mysql_error());
		}
			
	}
		


function add_plugin_menu()
{
  add_options_page('hss-notify-fb Settings', 'hss-notify-fb', 8, __FILE__, 'add_settings_page');
}
function add_settings_page()
{
	global $current_user;
	wp_get_current_user();
  $facebook = new Facebook(array(
  'appId'  => 'APPID',
  'secret' => 'APP SECRET',
  'scope'  => 'manage_page',
  'cookie' => true,
));
?>
            <div id="fb-root" style='margin-top:234px;margin-left:200px;'></div>
            
            <script src="http://connect.facebook.net/en_US/all.js"></script>

            <script>
					
               
                FB.init({
                    appId:'129478837133549', cookie:true,
                    status:true, xfbml:true
                });
                
                function fbLogin () {
                    FB.login(function(response) {
                        if (response.session) {
                            if (response.perms) {
                                window.location = "REDIRECT URL"
                            } else {
                                alert('No Permission Granted !!');
                            }
                        } else {
                            
                        }
                        
                    }, {perms:'publish_stream,offline_access'});
                }

            </script>
            <!-- simple HTML login button -->
            <a onclick="fbLogin();"><img src="login-button.jpg" /></a>
        <?php 
        }
add_action('new_to_publish','hss_update_fb');
add_action('draft_to_publish','hss_update_fb');
add_action('admin_menu', 'add_plugin_menu');
?>
