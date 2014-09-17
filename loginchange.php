<?php
/**
 * Plugin Name: OAuth for LDAP IITM
 * Description: Simple secure login for Wordpress through users' LDAP IITM (uses secure OAuth2 implemented by Avinash)
 * Version: 1.0.0
 * Author: Abhinav Parakh
 */
 
		function changelogin() {
		wp_enqueue_script('jquery');
		 ?>
	    <style type="text/css">
	        
	        
	        
	        div#login form#loginform p label[for=user_login], 
	        div#login form#loginform p label[for=user_pass],
	        div#login form#loginform p label[for=rememberme],
	        div#login form#loginform p.submit,
	        div#login p#nav {
	        	display: none;
	        } 
	         
	        
	     </style>
<?php
		}
		add_action('login_enqueue_scripts','changelogin');
		
		function addthis()
		{
			@session_start();

			require 'config/OAuth_config.php';
			require 'libs/OAuth.php';

			$oauth = new OAuth();
			$oauth->init();
			if($oauth->authCode){
				$_SESSION['authcode'] = $oauth->authCode;
			}
			if($oauth->user['loggedIn']){
				print_r($oauth->user);
				$dd =  get_user_by('login', $oauth->user);
				if(!$dd)
				{
					//$random_password = wp_generate_password();
					$userdata = array(
								'user_login'  =>  $oauth->user,
								'user_pass'   =>  $oauth->user,
								'user_nicename' => $oauth->user,
								'display_name' => $oauth->user
							);
					$user_id = wp_insert_user( $userdata );
					@add_user_meta( $user_id, 'wp_capabilities', 'a:1:{s:6:"author";b:1;}');
				}
				
				$dd =  get_user_by('login', $oauth->user);
				$creds = array();
				$a=$dd->get('$user_login');
				$creds['user_login'] = "$a";
				$creds['user_password'] = "$a";
				$creds['remember'] = false;
				$u = wp_signon($creds);
				if ( is_wp_error($u) )
					echo $u->get_error_message();
				else
				{
					$h=$_SERVER['HTTP_HOST'];
					echo '<meta http-equiv="refresh" content="1";url="$h">';
				}
			}
			else {
				echo "<a href='$oauth->signinURL'>Sign In</a> "  ;
			}
		}
		add_action('login_init','addthis');
		
		function mylogout()
		{
			require 'config/OAuth_config.php';
			$c=$_SERVER['HTTP_HOST'];
			$redirect_uri = "$c";
			@session_start();
			session_destroy();
			$signoutURL = AUTH_SERVER . CMD_SIGNOUT . "?response_type=". RESPONSE_TYPE ."&client_id=" . CLIENT_ID . "&redirect_uri=" . $redirect_uri . "&scope=". SCOPE . "&state=" . STATE;
			header('Location:'.$signoutURL);
		}
		add_action('wp_logout','mylogout');

?>