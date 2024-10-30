<?php
/**
Plugin Name: Last login on dashboard 
Plugin URI: https://www.linkedin.com/in/mishrasachin
Description: Check your last login on admin dashboard widget. Plugin for security purpose. No effect on frontend.
Author: Sachin Mishra
Author URI: https://www.linkedin.com/in/mishrasachin
Version: 1.0.2
**/

function show_admin_login($user){
	// show it on the widget
	$user_id = get_current_user_id();
	if ( metadata_exists( 'user', $user_id, 'sm_last_login' ) ) {
		$user_meta_value = get_user_meta($user_id,'sm_last_login', true);
		$user_data = maybe_unserialize($user_meta_value);
		$ip = $user_data['user_ip'];
		$browser = $user_data['browser'];
		$login_time = human_time_diff($user_data['login_time']);
		echo "<li class='user-login-detail'>";
		echo "You last logged in from ".$ip." about ".$login_time." ago using ".$browser;
		echo "</li>";
	}
}

// Now we set that function up to execute when the admin_notices action is called.
add_action('dashboard_glance_items', 'show_admin_login' );

function save_admin_login_details( $user_login, $user){
	
	global $wp;

	$user_data['user_ip'] 		= 	sm_get_current_ip(); // get user ip 
	$user_data['login_time']	=	time(); // make time in current format
	$user_data['browser']	=	$_SERVER['HTTP_USER_AGENT'];
	$user_data_ser = maybe_serialize($user_data);

    update_user_meta($user->ID, 'sm_last_login', $user_data_ser); // save the secret 
}
// save upon login
add_action( 'wp_login', 'save_admin_login_details', 10, 2 );


/*
*
* Try to find the IP address /
*/
function sm_get_current_ip(){
  if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        //ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}